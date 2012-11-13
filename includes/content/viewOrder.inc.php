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
|	viewOrder.inc.php
|   ========================================
|	Displays the Customers Specific Order
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'viewOrder.inc.php');
$lang = getLang('orders.inc.php');

	$view_order=new XTemplate (CC_DS.'content'.CC_DS.'viewOrder.tpl');

	$view_order->assign('LANG_YOUR_VIEW_ORDER',$lang['viewOrder']['order_no'].' '.sanitizeVar($_GET['cart_order_id']));

	$order = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_sum INNER JOIN '.$glob['dbprefix'].'CubeCart_customer ON '.$glob['dbprefix'].'CubeCart_order_sum.customer_id = '.$glob['dbprefix'].'CubeCart_customer.customer_id WHERE '.$glob['dbprefix'].'CubeCart_order_sum.cart_order_id = '.$db->mySQLSafe($_GET['cart_order_id']).' AND '.$glob['dbprefix'].'CubeCart_order_sum.customer_id='.$db->mySQLsafe($cc_session->ccUserData['customer_id']));

	if ($order) {

		if (in_array($order[0]['status'], array(1,4))) {
			$view_order->assign('LANG_MAKE_PAYMENT',sprintf($lang['viewOrder']['make_payment'],'index.php?_g=co&amp;_a=step3&amp;cart_order_id='.$_GET['cart_order_id']));
			$view_order->parse('view_order.session_true.order_true.make_payment');

		}

		$view_order->assign('LANG_CUSTOMER_INFO',$lang['viewOrder']['customer_info']);

		$view_order->assign('LANG_INVOICE_ADDRESS',$lang['viewOrder']['invoice_address']);
		$view_order->assign('VAL_INVOICE_NAME',$order[0]['name']);
	  	$view_order->assign('VAL_INVOICE_COMPANY_NAME',$order[0]['companyName']);
	  	$view_order->assign('VAL_INVOICE_ADD_1',$order[0]['add_1']);
	  	$view_order->assign('VAL_INVOICE_ADD_2',$order[0]['add_2']);
	  	$view_order->assign('VAL_INVOICE_TOWN',$order[0]['town']);
	 	$view_order->assign('VAL_INVOICE_POSTCODE',$order[0]['postcode']);
	  	$view_order->assign('VAL_INVOICE_COUNTRY',getCountryFormat($order[0]['country'],'id','printable_name'));
		$view_order->assign('VAL_INVOICE_COUNTY',$order[0]['county']);


		$view_order->assign('LANG_DELIVERY_ADDRESS',$lang['viewOrder']['delivery_address']);
		$view_order->assign('VAL_DELIVERY_NAME',$order[0]['name_d']);
		$view_order->assign('VAL_DELIVERY_COMPANY_NAME',$order[0]['companyName_d']);
	  	$view_order->assign('VAL_DELIVERY_ADD_1',$order[0]['add_1_d']);
	  	$view_order->assign('VAL_DELIVERY_ADD_2',$order[0]['add_2_d']);
	  	$view_order->assign('VAL_DELIVERY_TOWN',$order[0]['town_d']);
	 	$view_order->assign('VAL_DELIVERY_POSTCODE',$order[0]['postcode_d']);
	  	$view_order->assign('VAL_DELIVERY_COUNTRY',$order[0]['country_d']);
		$view_order->assign('VAL_DELIVERY_COUNTY',$order[0]['county_d']);


		if(empty($order[0]['customer_comments']))
		{
			$view_order->assign('VAL_CUSTOMER_COMMENTS',$lang['viewOrder']['na']);
		}
		else
		{
			$view_order->assign('VAL_CUSTOMER_COMMENTS',$order[0]['customer_comments']);
		}
		$view_order->assign('LANG_CUSTOMER_COMMENTS',$lang['viewOrder']['customer_comments']);
		$view_order->assign('LANG_ORDER_SUMMARY',$lang['viewOrder']['order_summary']);

		$view_order->assign('LANG_PRODUCT',$lang['viewOrder']['product']);
		$view_order->assign('LANG_PRODUCT_CODE',$lang['viewOrder']['product_code']);
		$view_order->assign('LANG_QUANTITY',$lang['viewOrder']['quantity']);
		$view_order->assign('LANG_PRICE',$lang['viewOrder']['price']);

		$products = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_inv WHERE `cart_order_id` = '.$db->mySQLSafe($_GET['cart_order_id']));

		for($i = 0, $maxi = count($products); $i < $maxi; ++$i)
		{


			if($products[$i]['digital']==1 && ($order[0]['status']>=2 && $order[0]['status']<=3) )
			{
				// get digital info
				$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_Downloads INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON '.$glob['dbprefix'].'CubeCart_Downloads.productId =  '.$glob['dbprefix'].'CubeCart_inventory.productId WHERE cart_order_id = '.$db->mySQLSafe($_GET['cart_order_id']).' AND '.$glob['dbprefix'].'CubeCart_Downloads.productId = '.$db->mySQLSafe($products[$i]['productId']);

				$download = $db->select($query);
				if($download) {
					$view_order->assign('VAL_DOWNLOAD_LINK',$glob['storeURL'].'/index.php?_g=dl&amp;pid='.$download[0]['productId'].'&oid='.base64_encode($_GET['cart_order_id']).'&ak='.$download[0]['accessKey']);
					$view_order->assign('LANG_DOWNLOAD_LINK',$lang['viewOrder']['download_here']);
					$view_order->parse('view_order.session_true.order_true.repeat_products.digital_link');
				}

			}


			$view_order->assign('TD_CLASS',cellColor($i, 'tdcartEven', 'tdcartOdd'));
			$view_order->assign('VAL_PRODUCT',$products[$i]['name']);
			$view_order->assign('VAL_PRODUCT_OPTS', nl2br($products[$i]['product_options']));
			$view_order->assign('VAL_IND_QUANTITY',$products[$i]['quantity']);
			$view_order->assign('VAL_IND_PROD_CODE',$products[$i]['productCode']);
			$view_order->assign('VAL_IND_PRICE',priceFormat($products[$i]['price'],true));
			$view_order->parse('view_order.session_true.order_true.repeat_products');

		}


		$view_order->assign('LANG_ORDER_LIST',$lang['viewOrder']['review_below']);

		$view_order->assign('LANG_ORDER_TIME',$lang['viewOrder']['order_date_time']);
		$view_order->assign('VAL_ORDER_TIME',formatTime($order[0]['time']));

		$view_order->assign('LANG_ORDER_STATUS',$lang['viewOrder']['order_status']);
		$view_order->assign('VAL_ORDER_STATUS',$lang['glob']['orderState_'.$order[0]['status']]);

		$view_order->assign('LANG_GATEWAY',$lang['viewOrder']['payment_method']);
		$view_order->assign('VAL_GATEWAY',str_replace('_',' ',$order[0]['gateway']));

		$view_order->assign('LANG_SHIP_METHOD',str_replace('_',' ',$lang['viewOrder']['ship_method']));
		$view_order->assign('VAL_SHIP_METHOD',$order[0]['shipMethod']);

		if (!empty($order[0]['courier_tracking'])) {
			$view_order->assign('LANG_SHIP_TRACK', $lang['viewOrder']['courier_tracking']);
			$view_order->assign('VAL_SHIP_TRACK',$order[0]['courier_tracking']);
		}

		$view_order->assign('LANG_SUBTOTAL',$lang['viewOrder']['subtotal']);
		$view_order->assign('VAL_SUBTOTAL',priceFormat($order[0]['subtotal'],true));

		$view_order->assign('LANG_DISCOUNT',$lang['viewOrder']['discount']);
		$view_order->assign('VAL_DISCOUNT',priceFormat($order[0]['discount'],true));

		$view_order->assign('LANG_TOTAL_TAX',$lang['viewOrder']['total_tax']);
		$view_order->assign('VAL_TOTAL_TAX',priceFormat($order[0]['total_tax'],true));

		$view_order->assign('LANG_TOTAL_SHIP',$lang['viewOrder']['shipping']);
		$view_order->assign('VAL_TOTAL_SHIP',priceFormat($order[0]['total_ship'],true));

		$view_order->assign('LANG_GRAND_TOTAL',$lang['viewOrder']['grand_total']);
		$view_order->assign('VAL_GRAND_TOTAL',priceFormat($order[0]['prod_total'],true));

		$view_order->parse('view_order.session_true.order_true');

	}
	else
	{
		$view_order->assign('LANG_NO_ORDERS',$lang['viewOrder']['order_not_found']);
		$view_order->parse('view_order.session_true.order_false');

	}


	$view_order->assign('LANG_LOGIN_REQUIRED',$lang['viewOrder']['login_required']);

	if($cc_session->ccUserData['customer_id']>0) $view_order->parse('view_order.session_true');

	else $view_order->parse('view_order.session_false');

	$view_order->parse('view_order');

$page_content = $view_order->text('view_order');
?>