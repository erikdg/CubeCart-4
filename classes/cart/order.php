<?php
/*

+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	order.php
|   ========================================
|	Core Order Class
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

class order {

	var $order;
	var $orderSum;
	var $orderInv;

	/*
	function order() {
		## Process level constants
		define('ORDER_PENDING', 1);
		define('ORDER_PROCESSING', 2);
		define('ORDER_COMPLETE', 3);
		define('ORDER_DECLINED', 4);
		define('ORDER_FAILED', 5);
		define('ORDER_CANCELLED', 6);
	}
	*/

	function storeTrans($transData, $forceLog = true) {
		global $glob, $db;

		$transDataSQL['time'] 			= $db->MySQLSafe(time());
		$transDataSQL['customer_id'] 	= $db->MySQLSafe($transData['customer_id']);
		$transDataSQL['gateway'] 		= $db->MySQLSafe($transData['gateway']);
		$transDataSQL['extra'] 			= $db->MySQLSafe($transData['extra']);
		$transDataSQL['trans_id'] 		= $db->MySQLSafe($transData['trans_id']);
		$transDataSQL['order_id']		= $db->MySQLSafe($transData['order_id']);
		$transDataSQL['status'] 		= $db->MySQLSafe($transData['status']);
		$transDataSQL['amount'] 		= $db->MySQLSafe($transData['amount']);
		$transDataSQL['notes'] 			= $db->MySQLSafe($transData['notes']);

		// make sure status isn't repeated on last call
		$maxStatus = $db->select('SELECT max(`id`), `status` FROM '.$glob['dbprefix'].'CubeCart_transactions WHERE `trans_id` = '.$transDataSQL['trans_id'].' GROUP BY `id` DESC');

		if (!$forceLog && ($maxStatus[0]['status'] != $transData['status'] || !$maxStatus)) {
			$db->insert($glob['dbprefix'].'CubeCart_transactions', $transDataSQL);
		} else if ($forceLog) {
			$db->insert($glob['dbprefix'].'CubeCart_transactions', $transDataSQL);
		}
	}

	function mkOrderNo() {
		global $config;

		$sign = substr($config['timeOffset'],0,1);
		$value = substr($config['timeOffset'],1);

		if($sign=='+'){
			$timeNow = time() + $value;
		} elseif($sign=='-'){
			$timeNow = time() - $value;
		} elseif($value>0){
			$timeNow = time() + $value;
		} else {
			$timeNow = time();
		}

		$this->cart_order_id = strftime('%y%m%d-%H%M%S-',$timeNow).rand(1000, 9999);
		return $this->cart_order_id;
	}

	function getOrderSum($cart_order_id) {
		global $db, $glob;
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_sum INNER JOIN '.$glob['dbprefix'].'CubeCart_customer ON '.$glob['dbprefix'].'CubeCart_order_sum.customer_id = '.$glob['dbprefix'].'CubeCart_customer.customer_id WHERE '.$glob['dbprefix'].'CubeCart_order_sum.cart_order_id = '.$db->mySQLSafe($cart_order_id);
		//$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_order_sum  WHERE `cart_order_id` = ".$db->mySQLSafe($cart_order_id);
		$order = $db->select($query);
		$this->orderSum = $order[0];
		return $order[0];
	}

	function getOrderInv($cart_order_id) {
		global $db, $glob;
		$products = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_inv WHERE `cart_order_id` = '.$db->mySQLSafe($cart_order_id));
		$this->orderInv = $products;
		return $this->orderInv;
	}

	function deleteOrder($cart_order_id) {
		global $db, $glob;
		$where = "`cart_order_id` = '".$cart_order_id."'";
		$delete = $db->delete($glob['dbprefix'].'CubeCart_order_sum', $where);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_order_inv', $where);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_Downloads', $where);
	}

	function customerOrderCount($customerId, $value) {
		global $db, $glob;

		$record['noOrders'] = ($value>0) ? 'noOrders + '.$value : 'noOrders - '.$value;
		$where = '`customer_id` = '.$customerId;
		$update = $db->update($glob['dbprefix'].'CubeCart_customer', $record, $where);
	}

	function manageStock($statusId,$cart_order_id){
		global $db, $glob, $config;

		if(!is_array($this->orderInv)){
			$this->getOrderInv($cart_order_id);
		}

		for($i = 0, $maxi = count($this->orderInv); $i < $maxi; ++$i) {

			// see if product uses stock or not
			$useStock = $db->select('SELECT `useStockLevel` FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE `productId` = '.$db->mySQLSafe($this->orderInv[$i]['productId']));

			// if it does continue
			if ($useStock[0]['useStockLevel']) {
				// When order has been completed (Order status: Complete)
				if ($config['stock_change_time'] == 0) {
					$reduceStockStatus = 3;
				// When payment has been received (Order status: Processing)
				} elseif ($config['stock_change_time'] == 1) {
					$reduceStockStatus = 2;
				// When order is built (Order status: Pending)
				} elseif($config['stock_change_time'] == 2) {
					$reduceStockStatus = 1;
					// override possible config error cant put stock back for pending orders in this state
					$config['stock_replace_time'][1] = 0;

				}

				// reduce stock if not already and status matches time to reduce stock
				if($this->orderInv[$i]['stockUpdated']==0 && $statusId == $reduceStockStatus) {
					$this->stockLevel($this->orderInv[$i]['quantity'], '-', $this->orderInv[$i]['productId'], $this->orderInv[$i]['id'], 1);
				// replace stock if reduced already and status permits
				} elseif($this->orderInv[$i]['stockUpdated'] && $config['stock_replace_time'][$statusId]) {
					$this->stockLevel($this->orderInv[$i]['quantity'], '+', $this->orderInv[$i]['productId'], $this->orderInv[$i]['id'], 0);
				}

			}

		}

	}

	function getOrderStatus($cart_order_id) {
		global $db;
		$currentStatus = $db->select('SELECT status FROM '.$GLOBALS['glob']['dbprefix'].'CubeCart_order_sum WHERE `cart_order_id` = '.$db->MySQLSafe($cart_order_id));
		if ($currentStatus) {
			return $currentStatus[0]['status'];
		}
		return false;
	}


	function orderStatus($statusId, $cart_order_id, $force = false, $skipEmail = false, $digital = false) {
		global $db, $glob, $config;

		/*
		1. Pending (New Order)
		2. Processing (See order notes)
		3. Order Complete & Dispatched
		4. Declined (See notes)
		5. Failed Fraud Review
		6. Cancelled
		*/

		// First make sure this process isn't being repeated! Some payment processors
		// send more than once in the XML if other attributes have changed

		$currentStatus = $db->select('SELECT `status` FROM '.$glob['dbprefix'].'CubeCart_order_sum WHERE `cart_order_id` = '.$db->MySQLSafe($cart_order_id) );

		$this->manageStock($statusId, $cart_order_id);

		if ($currentStatus[0]['status'] != $statusId) {
			switch($statusId) {
				case 2;		## Processing Nothing to do
					## Email the customer to say payment has been accepted and cleared
					$this->getOrderSum($cart_order_id);
					$lang = getLang('email.inc.php', $this->orderSum['lang']);

					$macroArray = array(
						'ORDER_ID'		=> $this->orderSum['cart_order_id'],
						'RECIP_NAME'	=> $this->orderSum['name'],
						'STORE_URL'		=> $glob['storeURL']
					);

					$text = macroSub($lang['email']['payment_complete_body'], $macroArray);
					unset($macroArray);

					if (!empty($_POST['extra_notes'])) {
						$text .= "\n\n---\n".$_POST['extra_notes'];
					}

					if ($digital) $this->digitalAccess();

					## Send email
					require_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';
					$mail = new htmlMimeMail();

					$mail->setText($text);
					$mail->setReturnPath($config['masterEmail']);
					$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
					$mail->setSubject(macroSub($lang['email']['payment_complete_subject'], array('ORDER_ID' => $this->orderSum['cart_order_id'])));
					$mail->setHeader('X-Mailer', 'CubeCart Mailer');
					$mail->setBcc($config['masterEmail']);
					$mail->send(array($this->orderSum['email']), $config['mailMethod']);
					break;

				case 3:		## Order Complete (Payment Taken/Cleared)
					$breakStatus = false;
					## Look up order
					$this->getOrderSum($cart_order_id);
					$this->getOrderInv($cart_order_id);

				#	$count = count($this->orderInv);
				#	for ($i=0; $i<$count; $i++) {
					if (is_array($this->orderInv)) {
						foreach ($this->orderInv as $i => $orderItem) {

							## If the order contains tangible items, we set it to ORDER_PROCESSING, and break the loop
							if (!$this->orderInv[$i]['digital'] && !$force) {
								$this->orderStatus(2, $cart_order_id, false, false, true);
								$statusId = 2; ## Safeguard
								$breakStatus = true;  ## Stops email sending below no way to stop this case ?!
								break;
							}

							## Send Gift Certificate
							if (!empty($this->orderInv[$i]['custom'])) {
								$customArray = unserialize(html_entity_decode($this->orderInv[$i]['custom']));
								if ($customArray['cert']) {
									$this->sendCoupon($customArray, $this->orderInv[$i]['id']);
								}
							}
						}

						if(!$breakStatus) {

							## If order is completely digital send digital file and keep status as complete
							$this->digitalAccess();
							## Send order complete email OOOOOH it's a bit diiiirty
							$lang = getLang('email.inc.php',$this->orderSum['lang']);
							$langAdmin = getLang('email.inc.php');
							if ($this->orderSum['discount']>0) {
								$grandTotal = priceFormat($this->orderSum['prod_total'], true).' (-'.priceFormat($this->orderSum['discount'], true).')';
							} else {
								$grandTotal = priceFormat($this->orderSum['prod_total'], true);
							}


							## Get taxes
							$tax_cost = '';
							$lang_tax = getLang('admin'.CC_DS.'admin_orders.inc.php');
							$config_tax_mod = fetchDbConfig('Multiple_Tax_Mod');
							if ($config_tax_mod['status']) {
								for ($i=0; $i<3; ++$i) {
									$tax_key_name = 'tax'.($i+1).'_disp';
									$tax_key_value = 'tax'.($i+1).'_amt';
									if (!empty($this->orderSum[$tax_key_name])) {
										$name	= $this->orderSum[$tax_key_name];
										$value	= priceFormat($this->orderSum[$tax_key_value], true);
										$tax_cost .= $name.' '.$value."\n";
									} else if ($i==0) {
										$tax_key_value = 'total_tax';
										$name	= $lang_tax['admin']['orders_total_tax'];
										$value	= priceFormat($this->orderSum[$tax_key_value], true);
										$tax_cost .= $name.' '.$value."\n";
									} else {
										break;
									}

								}
								$tax_cost = substr($tax_cost, 0, -1);
							} else {
								$tax_cost = $lang_tax['admin']['orders_total_tax'].' '.priceFormat($this->orderSum['total_tax'], true);
							}

							$macroArray = array(
								'RECIP_NAME'		=> $this->orderSum['name'],
								'ORDER_ID'			=> $this->orderSum['cart_order_id'],
								'ORDER_DATE'		=> formatTime($this->orderSum['time']),
								'INVOICE_NAME'		=> $this->orderSum['name'],
								'SUBTOTAL'			=> priceFormat($this->orderSum['subtotal'], true),
								'SHIPPING_COST'		=> priceFormat($this->orderSum['total_ship'], true),
								'TAX_COST'			=> $tax_cost,
								'GRAND_TOTAL'		=> $grandTotal,
								'INVOICE_COMPANY'	=> $this->orderSum['companyName'],
								'INVOICE_ADD_1'		=> $this->orderSum['add_1'],
								'INVOICE_ADD_2'		=> $this->orderSum['add_2'],
								'INVOICE_CITY'		=> $this->orderSum['town'],
								'INVOICE_REGION'	=> $this->orderSum['county'],
								'INVOICE_POSTCODE'	=> $this->orderSum['postcode'],
								'INVOICE_COUNTRY'	=> getCountryFormat($this->orderSum['country'], 'id', 'printable_name'),
								'DELIVERY_NAME'		=> $this->orderSum['name_d'],
								'DELIVERY_COMPANY'	=> $this->orderSum['companyName_d'],
								'DELIVERY_ADD_1'	=> $this->orderSum['add_1_d'],
								'DELIVERY_ADD_2'	=> $this->orderSum['add_2_d'],
								'DELIVERY_CITY'		=> $this->orderSum['town_d'],
								'DELIVERY_REGION'	=> $this->orderSum['county_d'],
								'DELIVERY_POSTCODE'	=> $this->orderSum['postcode_d'],
								'DELIVERY_COUNTRY'	=> $this->orderSum['country_d'],
								"PAYMENT_METHOD"	=> $this->orderSum['gateway'],
								"DELIVERY_METHOD"	=> (!empty($this->orderSum['courier_tracking'])) ? $this->orderSum['shipMethod']."\n".$this->orderSum['courier_tracking']."\n" : $this->orderSum['shipMethod'],
							);

							$text = macroSub($lang['email']['order_breakdown_1'],$macroArray);
							$textAdmin = macroSub($langAdmin['email']['order_breakdown_1'],$macroArray);
							unset($macroArray);

							if(!empty($this->orderSum['customer_comments'])) {
								$macroArray = array(
									'CUSTOMER_COMMENTS' => $this->orderSum['customer_comments']
								);

								$text .= macroSub($lang['email']['order_breakdown_2'],$macroArray);
								$textAdmin .= macroSub($langAdmin['email']['order_breakdown_2'],$macroArray);

								unset($macroArray);
							}

							$text .= $lang['email']['order_breakdown_3'];
							$textAdmin .= $langAdmin['email']['order_breakdown_3'];

							for ($i = 0, $maxi = count($this->orderInv); $i < $maxi; ++$i) {
								$macroArray = array(
									'PRODUCT_NAME' => html_entity_decode($this->orderInv[$i]['name'])
								);

								$text .= macroSub($lang['email']['order_breakdown_4'],$macroArray);
								$textAdmin .= macroSub($langAdmin['email']['order_breakdown_4'],$macroArray);
								unset($macroArray);

								$macroArray = array(
									'PRODUCT_OPTIONS' => $this->orderInv[$i]['product_options']
								);

								$text .= macroSub($lang['email']['order_breakdown_5'],$macroArray);
								$textAdmin .= macroSub($langAdmin['email']['order_breakdown_5'],$macroArray);
								unset($macroArray);

								$macroArray = array(
									'PRODUCT_QUANTITY' => $this->orderInv[$i]['quantity'],
									'PRODUCT_CODE' => $this->orderInv[$i]['productCode'],
									'PRODUCT_PRICE' => priceFormat($this->orderInv[$i]['price'],true)
								);

								$text .= macroSub($lang['email']['order_breakdown_6'], $macroArray);
								$textAdmin .= macroSub($langAdmin['email']['order_breakdown_6'], $macroArray);
								unset($macroArray);

							}

							if (!empty($_POST['extra_notes'])) {
								$text .= "\n\n---\n".$_POST['extra_notes'];
								$textAdmin .= "\n\n---\n".$_POST['extra_notes'];
							}

							## Send email
							require_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';

							$mail = new htmlMimeMail();
							$mail->setText($text);
							$mail->setReturnPath($config['masterEmail']);
							$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
							$mail->setSubject(macroSub($lang['email']['order_breakdown_subject'], array('ORDER_ID' => $this->orderSum['cart_order_id'])));
							$mail->setHeader('X-Mailer', 'CubeCart Mailer');
							$mail->send(array($this->orderSum['email']), $config['mailMethod']);

							$mailAdmin = new htmlMimeMail();
							$mailAdmin->setText($textAdmin);
							$mailAdmin->setReturnPath($config['masterEmail']);
							$mailAdmin->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
							$mailAdmin->setSubject(macroSub($langAdmin['email']['order_breakdown_subject'], array('ORDER_ID' => $this->orderSum['cart_order_id'])));
							$mailAdmin->setHeader('X-Mailer', 'CubeCart Mailer');
							$mailAdmin->send(array($config['masterEmail']), $config['mailMethod']);

						}

					}
					break;

				//case 4: // Declined nothing to do

				//break;

				case 5:
					## email customer to explain their order failed fraud review
					$this->orderSum = $this->getOrderSum($cart_order_id);

					$lang = getLang('email.inc.php',$this->orderSum['lang']);

					$macroArray = array(
						'ORDER_ID' => $this->orderSum['cart_order_id'],
						'RECIP_NAME' => $this->orderSum['name'],
						'ORDER_URL_PATH' => $glob['storeURL'].'/index.php?_g=co&_a=viewOrder&cart_order_id='.$this->orderSum['cart_order_id'],
						'STORE_URL' => $glob['storeURL']
					);

					$text = macroSub($lang['email']['fraud_body'],$macroArray);
					unset($macroArray);

					if (!empty($_POST['extra_notes'])) {
						$text .= "\n\n---\n".$_POST['extra_notes'];
					}

					## send email
					require_once CC_ROOT_DIR.CC_DS."classes".CC_DS."htmlMimeMail".CC_DS."htmlMimeMail.php";

					$mail = new htmlMimeMail();
					$mail->setText($text);
					$mail->setReturnPath($config['masterEmail']);
					$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
					$mail->setSubject(macroSub($lang['email']['fraud_subject'],array('ORDER_ID' => $this->orderSum['cart_order_id'])));
					$mail->setHeader('X-Mailer', 'CubeCart Mailer');
					$mail->setBcc($config['masterEmail']);
					$mail->send(array($this->orderSum['email']), $config['mailMethod']);
					break;

				case 6: 
					## cancelled (Can be cancelled by either admin/customer)
					$lang = getLang('email.inc.php',$this->orderSum['lang']);
					
					$this->orderSum = $this->getOrderSum($cart_order_id);

					## Prevent Voucher Fraud (#1400)
					$db->update($glob['dbprefix'].'CubeCart_Coupons', array('status' => '0'), array('cart_order_id' => $this->orderSum['cart_order_id']));

					if (!$skipEmail) {
						$lang = getLang('email.inc.php',$this->orderSum['lang']);
						$macroArray = array(
							'ORDER_ID' => $this->orderSum['cart_order_id'],
							'RECIP_NAME' => $this->orderSum['name'],
							'ORDER_URL_PATH' => $glob['storeURL'].'/index.php?_g=co&_a=viewOrder&cart_order_id='.$this->orderSum['cart_order_id'],
							'STORE_URL' => $glob['storeURL']
						);

						$text = macroSub($lang['email']['payment_cancelled_body'],$macroArray);

						unset($macroArray);

						if (!empty($_POST['extra_notes'])) {
							$text .= "\n\n---\n".$_POST['extra_notes'];
						}

						## Send email
						require_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';
						$mail = new htmlMimeMail();
						$mail->setText($text);
						$mail->setReturnPath($config['masterEmail']);
						$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
						$mail->setSubject(macroSub($lang['email']['payment_cancelled_subject'],array('ORDER_ID' => $this->orderSum['cart_order_id'])));
						$mail->setHeader('X-Mailer', 'CubeCart Mailer');
						$mail->setBcc($config['masterEmail']);
						$mail->send(array($this->orderSum['email']), $config['mailMethod']);
						break;

					}
			}

			$data['status'] = $statusId;
			$db->update($glob['dbprefix']."CubeCart_order_sum", $data, "cart_order_id=".$db->mySQLSafe($cart_order_id));

			return true;
		}
		return false;
	}


	function stockLevel($level, $sign, $productId, $orderInvId, $stockUpdated) {
		global $db, $glob;

		$query = 'UPDATE '.$glob['dbprefix'].'CubeCart_inventory SET `stock_level` = `stock_level` '.$sign.' '.$level.' WHERE `productId` = '.$productId;
		$update = $db->misc($query);

		$query = 'UPDATE '.$glob['dbprefix'].'CubeCart_order_inv SET `stockUpdated` =  '.$stockUpdated.' WHERE `id` = '.$orderInvId;
		$update = $db->misc($query);

	}


	function sendCoupon($customArray, $id) {

		global $db, $cart_order_id, $glob, $lang, $config, $order;

		## Create coupon code for the gift certificate
		$chars		= array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z');
		$max_chars	= count($chars)-1;
		$coupon		= sprintf('%s-%d-%d', $chars[mt_rand(0, $max_chars)].$chars[mt_rand(0, $max_chars)], time(), mt_rand(1000, 9999));

		## e.g: RW-1147691506-6723

		$data['status']				= $db->mySQLSafe(1);
		$data['code']				= $db->mySQLSafe($coupon);
		$data['discount_percent']	= $db->mySQLSafe(0);
		$data['discount_price']		= $db->mySQLSafe($customArray['amount']);
		$data['expires']			= $db->mySQLSafe(0);
		$data['allowed_uses']		= $db->mySQLSafe(0);
		$data['cart_order_id']		= $db->mySQLSafe($this->orderSum['cart_order_id']);

		$db->insert($glob['dbprefix'].'CubeCart_Coupons', $data);

		$couponId['couponId']		= $db->insertid();
		$db->update($glob['dbprefix'].'CubeCart_order_inv', $couponId, '`id` ='.$db->mySQLSafe($id));

		if($customArray['delivery']=='e') {

			$lang = getLang('email.inc.php',$this->orderSum['lang']);

			$macroArray = array(
				'RECIP_NAME'	=> $customArray['recipName'],
				'SENDER_NAME'	=> $this->orderSum['name'],
				'SENDER_EMAIL'	=> $this->orderSum['email'],
				'AMOUNT'		=> priceFormat($customArray['amount'], true),
				'MESSAGE'		=> html_entity_decode($customArray['message']),
				'COUPON'		=> $coupon,
				'STORE_URL'		=> $glob['storeURL']
			);

			$couponText = macroSub($lang['email']['coupon_body'], $macroArray);
			unset($macroArray);

			## Send email
			require_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';

			$mail = new htmlMimeMail();
			$mail->setText($couponText);
			$mail->setReturnPath($config['masterEmail']);
			$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
			$mail->setSubject($lang['email']['coupon_subject']);
			$mail->setHeader('X-Mailer', 'CubeCart Mailer');
			$mail->send(array($customArray['recipEmail']), $config['mailMethod']);
		}

	}

	function digitalAccess() {
		global $db, $glob, $lang, $config;
		$digitalProducts = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_Downloads INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON '.$glob['dbprefix'].'CubeCart_Downloads.productId =  '.$glob['dbprefix'].'CubeCart_inventory.productId WHERE cart_order_id = '.$db->mySQLSafe($this->orderSum['cart_order_id']));

		if($digitalProducts) {

			require_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';
			$lang = getLang('email.inc.php',$this->orderSum['lang']);
			$mail = new htmlMimeMail();
			## build email with access details

			$macroArray = array(
			'RECIP_NAME' => $this->orderSum['name'],
			'ORDER_ID' => $this->orderSum['cart_order_id'],
			'ORDER_DATE' => formatTime($this->orderSum['time']),
			'EXPIRE_DATE' => formatTime($digitalProducts[0]['expire']),
			'DOWNLOAD_ATTEMPTS' => $config['dnLoadTimes'],
			);

			$text = macroSub($lang['email']['downloads_body'],$macroArray);
			unset($macroArray);

			for($i = 0, $maxi = count($digitalProducts); $i < $maxi; ++$i) {
				$macroArray = array(
					'PRODUCT_NAME' => $digitalProducts[$i]['name'],
					'DOWNLOAD_URL' => $glob['storeURL'].'/index.php?_g=dl&pid='.$digitalProducts[$i]['productId'].'&oid='.base64_encode($this->orderSum['cart_order_id']).'&ak='.$digitalProducts[$i]['accessKey']
				);
				$text .= macroSub($lang['email']['downloads_body_2'], $macroArray);
				unset($macroArray);
			}

			$mail->setText($text);
			$mail->setReturnPath($config['masterEmail']);
			$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
			$macroArray = array('ORDER_ID' => $this->orderSum['cart_order_id']);
			$mail->setSubject(macroSub($lang['email']['downloads_subject'],$macroArray));
			$mail->setHeader('X-Mailer', 'CubeCart Mailer');
			$mail->send(array($this->orderSum['email']), $config['mailMethod']);
		}
	}

	function cancelOldOrders() {

		global $db, $glob, $config;

		if($config['orderExpire']==0) {

			return false;

		} else {

			$expiryLimit = time() - $config['orderExpire'];

			$expiredOrders = $db->select('SELECT `cart_order_id` FROM '.$glob['dbprefix'].'CubeCart_order_sum WHERE `status` = 1 AND `time` < '.$expiryLimit);

			if($expiredOrders) {
				for($i = 0, $maxi = count($expiredOrders); $i < $maxi; ++$i) {
					$this->orderStatus(6, $expiredOrders[$i]['cart_order_id']);
				}
			}
			return true;
		}

	}

	function createOrder($orderInv, $orderSum, $skipEmail = false, $lang = false, $code_used = false) {
		global $glob, $config, $db;
		/*
		Repeated Order Inventory Variables

		$orderInv[$i]['productId']
		$orderInv[$i]['name']
		$orderInv[$i]['price']
		$orderInv[$i]['quantity']
		$orderInv[$i]['product_options']
		$orderInv[$i]['productCode']
		$orderInv[$i]['digital']
		$orderInv[$i]['custom']

		Order Summary Variables

		$orderSum['cart_order_id']
		$orderSum['customer_id']
		$orderSum['email']
		$orderSum['name']
		$orderSum['add_1']
		$orderSum['add_2']
		$orderSum['town']
		$orderSum['county']
		$orderSum['postcode']
		$orderSum['country']
		$orderSum['phone']
		$orderSum['mobile']
		$orderSum['currency']

		$orderSum['name_d']
		$orderSum['add_1_d']
		$orderSum['add_2_d']
		$orderSum['town_d']
		$orderSum['county_d']
		$orderSum['postcode_d']
		$orderSum['country_d']

		$orderSum['subtotal']
		$orderSum['discount']
		$orderSum['total_ship']
		$orderSum['total_tax']
		$orderSum['prod_total']
		$orderSum['shipMethod']

		$orderSum['tax'.$i.'_disp'] = $taxes[$i]['display'];
		$orderSum['tax'.$i.'_amt'] = $taxes[$i]['amount'];

		$orderSum['gateway']

		$orderSum['basket']

		*/
		if(!empty($code_used)){
			$orderSumIn['comments'] = $db->mySQLSafe('Voucher: '.$code_used);
		}

		$gc	= fetchDbConfig('gift_certs');
		if (is_array($orderInv)) {
			for ($i = 1, $maxi = count($orderInv); $i <= $maxi; ++$i) {
				foreach ($orderInv[$i] as $key => $value) {
					$orderInvIn[$key] = $db->mySQLSafe($value);
					$orderInvIn['cart_order_id'] = $db->mySQLSafe($orderSum['cart_order_id']);
				}
				$insert		= $db->insert($glob['dbprefix'].'CubeCart_order_inv', $orderInvIn);

				/*
				$useStock	= $db->select("SELECT useStockLevel FROM ". $glob['dbprefix'] . "CubeCart_inventory WHERE productId = ".$db->mySQLSafe($orderInv[$i]['productId']));

				// lower stock level IF it is set to change on order creation
				if($useStock[0]['useStockLevel']==1 && $config['stock_change_time']==2) {
					$this->stockLevel($orderInv[$i]['quantity'],$orderInv[$i]['productId'],$orderSum['cart_order_id']);
				}
				*/
				if ($orderInv[$i]['digital'] && $orderInv[$i]['productCode'] != $gc['productCode']) {
					$digitalProduct['cart_order_id'] = $db->mySQLSafe($orderSum['cart_order_id']);
					$digitalProduct['customerId'] = $db->mySQLSafe($orderSum['customer_id']);
					$digitalProduct['expire'] = $db->mySQLSafe(time()+$config['dnLoadExpire']);
					$digitalProduct['productId'] = $db->mySQLSafe($orderInv[$i]['productId']);
					$digitalProduct['accessKey'] = $db->mySQLSafe(randomPass());
					$insert = $db->insert($glob['dbprefix'].'CubeCart_Downloads', $digitalProduct);
				}
			}
			if (!$insert) {
				echo 'An error building the order inventory was encountered. Please inform a member of staff.';
				exit;
			}
		}

		## Insert order summary
		if (is_array($orderSum)) {
			foreach ($orderSum as $key => $value) {
				$orderSumIn[$key] 	= $db->mySQLSafe($value);
			}
			$orderLang = ($lang) ? $lang : $config['defaultLang'];
			$orderSumIn['ip']  		= $db->mySQLSafe(get_ip_address());
			$orderSumIn['time'] 	= $db->mySQLSafe(time());
			$orderSumIn['lang'] 	= $db->mySQLSafe($orderLang);

			$db->insert($glob['dbprefix'].'CubeCart_order_sum', $orderSumIn);
		}

		## update customers order count + 1
		$this->customerOrderCount($orderSum['customer_id'], 1);
		$this->orderSum = $orderSum;
		if(!$skipEmail && !$config['disable_alert_email']) {
			$this->newOrderEmail();
		}
		##  set order status to 1, this will reduce stock accordingly
		$this->orderStatus(1, $orderSum['cart_order_id']);
		$this->cancelOldOrders();
	}

	function newOrderEmail($cart_order_id = '') {
		global $glob, $config, $lang;
		if (!empty($cart_order_id)) {
			$this->getOrderSum($cart_order_id);
			$this->getOrderInv($cart_order_id);
		}

		if (!class_exists('htmlMimeMail')) {
			require_once CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';
		}
		$lang = getLang('email.inc.php',$this->orderSum['lang']);
		$langDefault = getLang('email.inc.php');
		## email to storekeeper
		$mail = new htmlMimeMail();

		$macroArray = array(
			'CUSTOMER_NAME' => $this->orderSum['name'],
			'ORDER_ID' => $this->orderSum['cart_order_id'],
			'ADMIN_ORDER_URL' => $glob['storeURL'].'/'.$glob['adminFile'].'?_g=orders/orderBuilder&edit='.$this->orderSum['cart_order_id'],
			'SENDER_ID' => get_ip_address(),
		);
		$text = macroSub($langDefault['email']['admin_pending_order_body'],$macroArray);
		unset($macroArray);
		$mail->setText($text);
		$mail->setReturnPath($config['masterEmail']);
		$mail->setFrom($this->orderSum['name'].' <'.$this->orderSum['email'].'>');
		$mail->setSubject(macroSub($langDefault['email']['admin_pending_order_subject'],array('ORDER_ID' => $this->orderSum['cart_order_id'])));
		$mail->setHeader('X-Mailer', 'CubeCart Mailer');
		$mail->send(array($config['masterEmail']), $config['mailMethod']);

		## email to customer
		/*
		$mail = new htmlMimeMail();
		$macroArray = array(
			"CUSTOMER_NAME" => $this->orderSum['name'],
			"ORDER_ID" => $this->orderSum['cart_order_id'],
			"ORDER_URL" => $glob['storeURL']."/index.php?_g=co&_a=viewOrder&cart_order_id=".$this->orderSum['cart_order_id']
		);

		$text = macroSub($lang['email']['order_acknowledgement_body'],$macroArray);
		unset($macroArray);
		$mail->setText($text);
		$mail->setReturnPath($config['masterEmail']);
		$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
		$mail->setSubject(macroSub($lang['email']['order_acknowledgement_subject'],array("ORDER_ID" => $this->orderSum['cart_order_id'])));
		$mail->setHeader('X-Mailer', 'CubeCart Mailer');
		$mail->send(array($this->orderSum['email']), $config['mailMethod']);
		*/
	}
}

?>