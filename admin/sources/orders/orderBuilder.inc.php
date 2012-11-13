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
|	orderBuilder.inc.php
|   ========================================
|	Ability to add/edit orders
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

// Check order id is legal!
if(isset($_GET['edit']) && !preg_match('#^([0-9-]+)$#', $_GET['edit']) || isset($_POST['edit']) && !preg_match('#^([0-9-]+)$#', $_POST['edit'])) {
	httpredir('?_g=orders/index');
}

$lang = getLang('admin'.CC_DS.'admin_orders.inc.php');
$lang = getLang('orders.inc.php');

require_once 'classes'.CC_DS.'cart'.CC_DS.'order.php';
$order = new order();

require $glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php';

permission('orders', 'write', true);

if (isset($_POST['ajax'])) {
	if (!isset($_POST['customer']) || empty($_POST['customer'])) {
		$sql = 'SELECT DISTINCT * FROM `'.$glob['dbprefix'].'CubeCart_customer` WHERE `type` > 0 ORDER BY `lastName`, `firstName` ASC';
	} else {
		$customer = $db->mySQLSafe((string)$_POST['customer']);
		$sql = 'SELECT DISTINCT * FROM `'.$glob['dbprefix'].'CubeCart_customer` WHERE (`email` RLIKE '.$customer.' OR `lastName` RLIKE '.$customer.' OR `firstName` RLIKE '.$customer.' OR `customer_id` RLIKE '.$customer.') AND `type` > 0 ORDER BY `lastName`, `firstName` ASC';
	}
	$customers = $db->select($sql);

	?>
	<select name="customer_id" id="customer_select" onchange="populate();">
	<option value="0" <?php if(!$_POST['customer_id'] && !$orderSum) { echo 'selected="selected"'; } ?>>-- <?php echo $lang['admin_common']['na'];?> --</option>
	<?php

	if ($customers) {
		for ($i =  0, $maxi = count($customers); $i < $maxi; ++$i) {
			?>
			<option value="<?php echo $customers[$i]['customer_id'];?>"
			onmouseover="findObj('name').value='<?php echo addslashes($customers[$i]['title'].' '.html_entity_decode($customers[$i]['firstName'].' '.$customers[$i]['lastName'], ENT_QUOTES));?>';findObj('companyName').value='<?php echo addslashes(html_entity_decode($customers[$i]['companyName'], ENT_QUOTES));?>';findObj('add_1').value='<?php echo addslashes(html_entity_decode($customers[$i]['add_1'], ENT_QUOTES));?>';findObj('add_2').value='<?php echo addslashes(html_entity_decode($customers[$i]['add_2'], ENT_QUOTES));?>';findObj('town').value='<?php echo addslashes(html_entity_decode($customers[$i]['town'], ENT_QUOTES));?>';findObj('country').value='<?php echo $countriesArray[$customers[$i]['country']];?>';findObj('postcode').value='<?php echo $customers[$i]['postcode'];?>';findObj('county').value='<?php echo $customers[$i]['county'];?>';findObj('phone').value='<?php echo $customers[$i]['phone'];?>';findObj('mobile').value='<?php echo $customers[$i]['mobile'];?>';findObj('email').value='<?php echo $customers[$i]['email'];?>';"
			> <?php echo $customers[$i]['lastName'];?>, <?php echo $customers[$i]['firstName'];?> (<?php echo $customers[$i]['customer_id'];?>)</option>
			<?php
		}
	}
	?>
	</select>
	<?php
	die();
}

if (isset($_GET['reset']) && $_GET['reset']>0) {
	$record['noDownloads']	= 0;
	$record['expire']		= time()+$config['dnLoadExpire'];

	$where	= '`id` = '.$_GET['reset'];
	$update	= $db->update($glob['dbprefix'].'CubeCart_Downloads', $record, $where);

	httpredir($glob['adminFile'].'?_g=orders/orderBuilder&edit='.(int)$_GET['edit']);
}

if (isset($_POST['cart_order_id']) && !isset($_POST['prodRowsSubmit']) && $_POST['customer_id']>0) {

	$cache = new cache();
	$cache->clearCache();

	// ORDER INVENTORY
	if (count($_POST['id'])>0) {
		for ($i = 0, $maxi = count($_POST['id']); $i <= $maxi; ++$i) {
			if ((!empty($_POST['prodName'][$i]) && !empty($_POST['quantity'][$i]) && !empty($_POST['price'][$i])) || $_POST['delId'][$i] == 1) {

				$newOrderInv['name'] = $db->mySQLSafe($_POST['prodName'][$i]);
				$newOrderInv['productCode'] = $db->mySQLSafe($_POST['productCode'][$i]);
				$newOrderInv['product_options'] = $db->mySQLSafe($_POST['product_options'][$i]);
				$newOrderInv['quantity'] = $db->mySQLSafe($_POST['quantity'][$i]);
				$newOrderInv['price'] = $db->mySQLSafe($_POST['price'][$i]);
				$newOrderInv['cart_order_id'] = $db->mySQLSafe($_POST['cart_order_id']);

				if ($_POST['delId'][$i] == 1) {
					$where = '`id` = '.$db->mySQLSafe($_POST['id'][$i]);
					$delete =  $db->delete($glob['dbprefix'].'CubeCart_order_inv', $where);
				} else if ($_POST['id'][$i]>0) {
					$where = '`id` = '.$db->mySQLSafe($_POST['id'][$i]);
					$update = $db->update($glob['dbprefix'].'CubeCart_order_inv', $newOrderInv, $where);
				} else {
					$insert = $db->insert($glob['dbprefix'].'CubeCart_order_inv', $newOrderInv);
				}
			}
		}
	}
	// ORDER SUMMARY
	$newOrderSum['cart_order_id'] 	= $db->mySQLSafe($_POST['cart_order_id']);
	$newOrderSum['customer_id'] 	= $db->mySQLSafe($_POST['customer_id']);
	$newOrderSum['name'] 			= $db->mySQLSafe($_POST['name']);
	$newOrderSum['add_1'] 			= $db->mySQLSafe($_POST['add_1']);
	$newOrderSum['add_2'] 			= $db->mySQLSafe($_POST['add_2']);
	$newOrderSum['town'] 			= $db->mySQLSafe($_POST['town']);
	$newOrderSum['county'] 			= $db->mySQLSafe($_POST['county']);
	$newOrderSum['postcode'] 		= $db->mySQLSafe($_POST['postcode']);
	$newOrderSum['country'] 		= $db->mySQLSafe($_POST['country']);
	$newOrderSum['name_d'] 			= $db->mySQLSafe($_POST['name_d']);
	$newOrderSum['companyName'] 	= $db->mySQLSafe($_POST['companyName']);
	$newOrderSum['companyName_d'] 	= $db->mySQLSafe($_POST['companyName_d']);
	$newOrderSum['add_1_d'] 		= $db->mySQLSafe($_POST['add_1_d']);
	$newOrderSum['add_2_d'] 		= $db->mySQLSafe($_POST['add_2_d']);
	$newOrderSum['town_d'] 			= $db->mySQLSafe($_POST['town_d']);
	$newOrderSum['county_d'] 		= $db->mySQLSafe($_POST['county_d']);
	$newOrderSum['postcode_d'] 		= $db->mySQLSafe($_POST['postcode_d']);
	$newOrderSum['country_d'] 		= $db->mySQLSafe($_POST['country_d']);
	$newOrderSum['phone'] 			= $db->mySQLSafe($_POST['phone']);
	$newOrderSum['mobile'] 			= $db->mySQLSafe($_POST['mobile']);
	$newOrderSum['subtotal'] 		= $db->mySQLSafe($_POST['subtotal']);
	$newOrderSum['discount'] 		= $db->mySQLSafe($_POST['discount']);
	$newOrderSum['prod_total'] 		= $db->mySQLSafe($_POST['prod_total']);

	if(!empty($_POST['tax1_disp'])) $newOrderSum['tax1_disp'] = $db->mySQLSafe($_POST['tax1_disp']);
	if(!empty($_POST['tax1_amt'])) 	$newOrderSum['tax1_amt'] = $db->mySQLSafe($_POST['tax1_amt']);
	if(!empty($_POST['tax2_disp'])) $newOrderSum['tax2_disp'] = $db->mySQLSafe($_POST['tax2_disp']);
	if(!empty($_POST['tax2_amt'])) 	$newOrderSum['tax2_amt'] = $db->mySQLSafe($_POST['tax2_amt']);
	if(!empty($_POST['tax3_disp'])) $newOrderSum['tax3_disp'] = $db->mySQLSafe($_POST['tax3_disp']);
	if(!empty($_POST['tax3_amt'])) 	$newOrderSum['tax3_amt'] = $db->mySQLSafe($_POST['tax3_amt']);

	if(!isset($_POST['total_tax'])) { $_POST['total_tax'] = $_POST['tax1_amt'] + $_POST['tax2_amt'] + $_POST['tax3_amt']; }

	$newOrderSum['total_tax'] 		= $db->mySQLSafe($_POST['total_tax']);
	$newOrderSum['total_ship'] 		= $db->mySQLSafe($_POST['total_ship']);
	// removed as this is done further down $newOrderSum['status'] = $db->mySQLSafe($_POST['status']);
	$newOrderSum['comments'] 		= $db->mySQLSafe($_POST['comments']);
	$newOrderSum['customer_comments'] = $db->mySQLSafe($_POST['customer_comments']);
	$newOrderSum['extra_notes'] 	= $db->mySQLSafe($_POST['extra_notes']);

	$newOrderSum['email'] 			= $db->mySQLSafe($_POST['email']);
	$newOrderSum['ship_date'] 		= $db->mySQLSafe($_POST['ship_date']);
	$newOrderSum['shipMethod'] 		= $db->mySQLSafe($_POST['shipMethod']);
	$newOrderSum['gateway'] 		= $db->mySQLSafe($_POST['gateway']);

	$newOrderSum['courier_tracking'] = $db->mySQLsafe($_POST['courier_tracking']);

	if (isset($_GET['edit'])) {

		$where = '`cart_order_id` = '.$db->mySQLSafe($_GET['edit']);
		$update = $db->update($glob['dbprefix'].'CubeCart_order_sum', $newOrderSum, $where);

		if (isset($_POST['cc_delete'])) {
			$record['offline_capture'] = "''";
			$db->update($glob['dbprefix'].'CubeCart_order_sum', $record, array('customer_id' => $_POST['customer_id'], 'cart_order_id' => $_POST['cart_order_id']));
		## If not under SSL card fileds defalt to "xxx"
		} elseif($_POST['card_type']!="xxx") {
			if (isset($_POST['card_number'])) {
				$cardData = array(
					'card_type'		=> $_POST['card_type'],
					'card_number'	=> $_POST['card_number'],
					'card_expire'	=> $_POST['card_expire'],
					'card_valid'	=> $_POST['card_valid'],
					'card_issue'	=> $_POST['card_issue'],
					'card_cvv'		=> $_POST['card_cvv'],
				);
				if (function_exists('mcrypt_module_open')) {
					require_once('classes'.CC_DS.'cart'.CC_DS.'encrypt.inc.php');
					$keyArray = array($_POST['cart_order_id']);
					$crypt = new encryption($keyArray);
					$record['offline_capture'] = "'".base64_encode($crypt->encrypt(serialize($cardData)))."'";
				}
				$db->update($glob['dbprefix'].'CubeCart_order_sum', $record, array('customer_id' => $_POST['customer_id'], 'cart_order_id' => $_POST['cart_order_id']));
			}
		}

		if ($update) {
			$msg .= "<p class='infoText'>".sprintf($lang['admin']['orders_updated_successfully'],$_GET['edit'])."</p>";
		}/* else {
			$msg .= "<p class='warnText'>".sprintf($lang['admin']['orders_update_failed'], $_GET['edit'])."</p>";
		} */

	} else {
		$newOrderSum['ip'] = $db->mySQLSafe(get_ip_address());
		$newOrderSum['time'] = $db->mySQLSafe(time());
		$insert = $db->insert($glob['dbprefix']."CubeCart_order_sum", $newOrderSum);

		if ($_POST['customer_id']>0) {
			$record['noOrders'] = 'noOrders + 1';
			$where = '`customer_id` = '.$_POST['customer_id'];
			$update = $db->update($glob['dbprefix'].'CubeCart_customer', $record, $where);
		}

		if ($insert) {
			$msg .= "<p class='infoText'>".sprintf($lang['admin']['orders_add_success'],$_POST['cart_order_id'])."</p>";
			// send email confirmation
			$order->newOrderEmail($_POST['cart_order_id']);
		} else {
			$msg .= "<p class='warnText'>".sprintf($lang['admin']['orders_add_fail'],$_POST['cart_order_id'])."</p>";
		}
	}

	// update order status email etc
	$order->orderStatus($_POST['status'], $_POST['cart_order_id'], true);

	if ($_POST['cart_order_id']!=$_GET['edit']) {
		httpredir($glob['adminFile'].'?_g=orders/orderBuilder&edit='.$_POST['cart_order_id']);
	}
}

if (isset($_GET['edit'])) {
	$orderSum = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_sum WHERE `cart_order_id` = '.$db->mySQLSafe($_GET['edit']));
	$orderInv = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_order_inv WHERE `cart_order_id` = '.$db->mySQLSafe($_GET['edit']));
}

if (count($orderInv) < 1 && !empty($_GET['edit'])) {
	$msg .= "<p class='warnText'>".sprintf($lang['admin']['orders_no_products'], $_GET['edit'])."</p>";
}
$sql = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `type` > 0';
$noCustomers = $db->numrows($sql);
## Work around to change the drop dowm menu to a text box if there are over 500 customers. Current
## solution drastically slows or even halts the page. Ajax lookup required.
## See bug 1212
if($noCustomers<500) {
	$customers = $db->select($sql.' ORDER BY `lastName`, `firstName` ASC');
}
$countries = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_iso_countries');

if ($countries) {
	$countriesArray = array();
	for($i = 0, $maxi = count($countries); $i <= $maxi; ++$i){
		$countriesArray[$countries[$i]['id']] = $countries[$i]['printable_name'];
	}
}

if(isset($_GET['PayPal-Pro']) && !empty($_GET['PayPal-Pro'])) {

	// Get Module Config
	$module = fetchDbConfig('PayPal_Pro');

	$basePPPath = 'modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'wpp-'.str_replace(array('ECO','DPO'),'',$module['mode']).CC_DS;

	$order_id = $_GET['edit'];

	$ppfunction = preg_replace('#[^a-z]#i', '', $_GET['PayPal-Pro']);

	switch($ppfunction) {

		case 'doCapture':
			require_once($basePPPath.'DoCaptureReceipt.php');
		break;

		case 'doAuth':
		case 'doReAuth':
			require_once($basePPPath.'DoReauthorizationReceipt.php');
		break;

		case 'doRefund':
			require_once($basePPPath.'RefundReceipt.php');
		break;

		case 'doVoidAuth':
			require_once($basePPPath.'DoVoidReceipt.php');
		break;

		case 'doFMF':
			require_once($basePPPath.'ManagePendingTransactionStatus.php');
		break;


	}
}

require_once($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

?>

<p class="pageTitle">
  <?php if(isset($_GET['edit'])) { echo $lang['admin_common']['edit']; } else { echo $lang['admin_common']['add']; } ?>
  <?php echo $lang['admin']['orders_order'];?></p>
<?php if (isset($msg)) echo $msg; ?>
<p>
  <input type="button" class="submit" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=orders/print&amp;cart_order_id=<?php echo $_GET['edit']; ?>', 'PrintSlip', 600, 550, '1,toolbar=1')" value="<?php echo $lang['admin']['orders_print_packing_slip'];?>" />
</p>
<?php
## Discontinued from 4.1.0 final onwards
//if(getCountryFormat($config['siteCountry'],'id','iso')=="GB") {
?>
<!--
<div style="border:#666666 1px solid; background:#FFFFFF; padding: 5px; width: 400px; margin-bottom: 10px;"><strong><a href="http://labels.cubecart.com" target="_blank"><img src="images/admin/integratedPackSlip.jpg" align="left" alt="Integrated Packing Slip Icon" width="86" height="94" hspace="3" border="0" /></a>Single Integrated Label Sheets</strong><br />
  Speed up the dispatch process with compatible single integrated label sheets. These can be used to peel off a delivery address which can be stuck straight on to the package. <br />
  <span style="float: right;"><a href="http://labels.cubecart.com" target="_blank" class="txtLink">Purchase</a> | <a href="http://labels.cubecart.com" target="_blank" class="txtLink">Learn More &raquo;</a></span><br clear='all' />
</div>
-->
<?php
//}
?>
<form action="<?php echo $glob['adminFile']; ?>?_g=orders/orderBuilder<?php if(isset($_GET['edit'])) { echo "&amp;edit=".$_GET['edit']; } ?>" method="post" enctype="multipart/form-data" name="orderBuilder" target="_self">
  <table cellspacing="1" cellpadding="3" class="mainTable" width="100%">
    <tr>
      <td colspan="4" class="tdTitle"><?php echo $lang['admin']['orders_order_summary'];?></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin_common']['other_order_no']; ?></strong></td>
      <td class="tdText"><input name="cart_order_id" type="text" class="textbox" value="<?php if(isset($orderSum[0]['cart_order_id'])) { echo $orderSum[0]['cart_order_id'].'" readonly="readonly'; } else { echo $order->mkOrderNo(); } ?>" size="22" /></td>
      <td class="tdText"><strong><?php echo $lang['admin_common']['other_customer']; ?></strong></td>
      <td class="tdText">
      <?php
      if ($customers) {
      ?>
      <select name="customer_id" id="customer_select" onchange="populate();">
		<?php if($orderSum) { ?>
		<option value="0" <?php if(!$_POST['customer_id'] && !$orderSum) { echo 'selected="selected"'; } ?>>-- <?php echo $lang['admin_common']['na'];?> --</option>
		<?php
		}

			//for ($i=0; $i<count($customers); $i++) {
			foreach ($customers as $customer) {
			#	$customer = array_map('html_entity_decode', $customer);
			#	$customer = array_map('addslashes', $customer);
		?>
		<option value="<?php echo $customer['customer_id'];?>"
		<?php if($customer['customer_id']==$_POST['customer_id'] || $customer['customer_id']==$orderSum[0]['customer_id']){ echo 'selected="selected"'; } ?>
		onmouseover="findObj('name').value='<?php echo addslashes($customer['title'].' '.html_entity_decode($customer['firstName'].' '.$customer['lastName'], ENT_QUOTES));?>';findObj('companyName').value='<?php echo addslashes(html_entity_decode($customer['companyName'], ENT_QUOTES));?>';findObj('add_1').value='<?php echo addslashes(html_entity_decode($customer['add_1'], ENT_QUOTES));?>';findObj('add_2').value='<?php echo addslashes(html_entity_decode($customer['add_2'], ENT_QUOTES));?>';findObj('town').value='<?php echo addslashes(html_entity_decode($customer['town'], ENT_QUOTES));?>';findObj('country').value='<?php echo $countriesArray[$customer['country']];?>';findObj('postcode').value='<?php echo $customer['postcode'];?>';findObj('county').value='<?php echo $customer['county'];?>';findObj('phone').value='<?php echo $customer['phone'];?>';findObj('mobile').value='<?php echo $customer['mobile'];?>';findObj('email').value='<?php echo $customer['email'];?>';"
		> <?php echo $customer['lastName'];?>, <?php echo $customer['firstName'];?> (<?php echo $customer['customer_id'];?>)</option>
		<?php
			}
		?>
        </select>
        <?php } else { ?>
        <script type="text/javascript">
		document.observe("dom:loaded", function() {
			$('lookup_customer').observe('click', function() {
				var customer_id = $F('customer_id');
				var post_hash = new Hash({
					ajax: '1',
					customer: $F('customer_id')
				});

				new Ajax.Request("<?php echo $glob['adminFile']; ?>?_g=orders/orderBuilder", {
					parameters: post_hash,
					method: 'post',
					onCreate: function() {
						showloader();
					},
					onComplete: function() {
						hideloader();
					},
					onSuccess: function(transport) {
						if (!transport.responseText.empty()) {
							$('customer_id_result').update('<br />'+transport.responseText);
						} else {
							$('customer_id_result').update('<br />N/A');
						}
					}
				});
			});
		});
		</script>
        <input type="text" name="customer_id" class="textbox" id="customer_id" value="<?php echo isset($_POST['customer_id']) ? $_POST['customer_id'] : $orderSum[0]['customer_id']; ?>" /><input type="button" id="lookup_customer" value="<?php echo $lang['admin_common']['update']; ?>"  />
        <span id="customer_id_result"></span>
        <?php } ?>
	  </td>
    </tr>
    <tr>
      <td colspan="2" class="tdTitle"><?php echo $lang['admin']['orders_billing_info']; ?></td>
      <td colspan="2" class="tdTitle"><?php echo $lang['admin']['orders_shipping_info']; ?></td>
    </tr>
    <tr>
      <td class="tdText">&nbsp;</td>
      <td class="tdText"><input type="button" name="clear" value="<?php echo $lang['admin']['orders_reset_billing'];?>" onclick="findObj('name').value='';findObj('companyName').value='';findObj('add_1').value='';findObj('add_2').value='';findObj('town').value='';findObj('country').value='';findObj('postcode').value='';findObj('county').value='';findObj('phone').value='';findObj('mobile').value='';findObj('email').value='';" class="submit" /></td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText"><input type="button" name="shipCopy" value="<?php echo $lang['admin']['orders_copy_from_billing']; ?>" onclick="findObj('name_d').value = findObj('name').value;findObj('companyName_d').value = findObj('companyName').value;findObj('add_1_d').value = findObj('add_1').value;findObj('add_2_d').value = findObj('add_2').value;findObj('town_d').value = findObj('town').value;findObj('country_d').value = findObj('country').value;findObj('postcode_d').value = findObj('postcode').value;findObj('county_d').value = findObj('county').value;"  class="submit"/></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_name']; ?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="name" id="name" value="<?php echo $orderSum[0]['name']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_name']; ?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="name_d" id="name_d" value="<?php echo $orderSum[0]['name_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_company_name']; ?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="companyName" id="companyName" value="<?php echo $orderSum[0]['companyName']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_company_name']; ?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="companyName_d" id="companyName_d" value="<?php echo $orderSum[0]['companyName_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_address'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="add_1" id="add_1" value="<?php echo $orderSum[0]['add_1']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_address'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="add_1_d" id="add_1_d" value="<?php echo $orderSum[0]['add_1_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText">&nbsp;</td>
      <td class="tdText"><input type="text" class="textbox" name="add_2" id="add_2" value="<?php echo $orderSum[0]['add_2']; ?>" /></td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText"><input type="text" class="textbox" name="add_2_d" id="add_2_d" value="<?php echo $orderSum[0]['add_2_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_town'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="town" id="town" value="<?php echo $orderSum[0]['town']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_town'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="town_d" id="town_d" value="<?php echo $orderSum[0]['town_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_state'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="county" id="county" value="<?php echo $orderSum[0]['county']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_state'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="county_d" id="county_d" value="<?php echo $orderSum[0]['county_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_postcode'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="postcode" id="postcode" value="<?php echo $orderSum[0]['postcode']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_postcode'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="postcode_d" id="postcode_d" value="<?php echo $orderSum[0]['postcode_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_country'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="country" id="country" value="<?php echo $orderSum[0]['country']; ?>" /></td>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_country'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="country_d" id="country_d" value="<?php echo $orderSum[0]['country_d']; ?>" /></td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_phone'];?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="phone" id="phone" value="<?php echo $orderSum[0]['phone']; ?>" /></td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_cell_phone']; ?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="mobile" id="mobile" value="<?php echo $orderSum[0]['mobile']; ?>" /></td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_email']; ?></strong></td>
      <td class="tdText"><input type="text" class="textbox" name="email" id="email" value="<?php echo $orderSum[0]['email']; ?>" /></td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_status']; ?></strong></td>
      <td valign="top" class="tdText"><select name="status" class="dropDown">
          <?php
		for ($i=1; $i<=6; ++$i) {
		?>
          <option value="<?php echo $i; ?>" <?php if($orderSum[0]['status']==$i) { echo 'selected="selected"'; } ?>><?php echo $lang['glob']['orderState_'.$i]; ?></option>
          <?php
		}
		?>
        </select>      </td>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_shipping_date']; ?></strong></td>
      <td class="tdText"><input name="ship_date" type="text" value="<?php echo $orderSum['0']['ship_date']; ?>" class="textbox" id="ship_date" size="25" />
        <br />
        <?php echo $lang['admin']['orders_ship_today']; ?>
        <input name="shipToday" type="checkbox" id="shipToday" value="checkbox" onclick="findObj('ship_date').value='<?php echo strip_tags(date($config['dateFormat'], time()+$config['timeOffset'])); ?>';" /></td>
    </tr>
    <tr>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_customer_comments']; ?></strong></td>
      <td class="tdText"><textarea name="customer_comments" cols="30" rows="3" class="textbox"><?php echo $orderSum['0']['customer_comments']; ?></textarea></td>
      <td valign="top" class="tdText">&nbsp;</td>
      <td valign="top" class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_staff_comments']; ?></strong></td>
      <td class="tdText"><textarea name="comments" cols="30" rows="3" class="textbox"><?php echo $orderSum['0']['comments']; ?></textarea></td>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_ship_method']; ?></strong> </td>
      <td valign="top" class="tdText"><input type="text" name="shipMethod" class="textbox" value="<?php echo str_replace("_"," ",$orderSum['0']['shipMethod']); ?>" /></td>
    </tr>
    <tr>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_extra_notes']; ?></strong></td>
      <td class="tdText"><textarea name="extra_notes" cols="30" rows="3" class="textbox"><?php echo $orderSum['0']['extra_notes']; ?></textarea></td>
      <td valign="top" class="tdText">&nbsp;</td>
      <td valign="top" class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td class="tdText"><strong><?php echo $lang['admin']['orders_payment_method']; ?></strong></td>
      <td class="tdText">
	  <?php if(strstr($orderSum['0']['gateway'], "PayPal Website Payments Pro")) { ?>
	  <input type="hidden" name="gateway" value="<?php echo str_replace('_',' ',$orderSum['0']['gateway']); ?>" />
	  <?php echo str_replace('_',' ',$orderSum['0']['gateway']); ?>
	  <?php } else { ?>
	  <input type="text" name="gateway" class="textbox" value="<?php echo str_replace('_',' ',$orderSum['0']['gateway']); ?>" />
	  <?php
	  }
	  ?></td>
      <td class="tdText">&nbsp;</td>
      <td class="tdText">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" align="right" class="tdText"><span class="tdTitle">
        <input type="submit" name="submit22" value="<?php if(isset($_GET['edit'])) { echo $lang['admin_common']['edit']; } else { echo $lang['admin_common']['add']; } ?> <?php echo $lang['admin']['orders_order'];?>"  class="submit" />
      </span></td>
    </tr>
  </table>
  <br />
  <table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
    <tr>
      <td class="tdTitle"><?php echo $lang['admin']['orders_courier_tracking'];?></td>
    </tr>
    <tr>
      <td valign="top" class="tdText"><strong><?php echo $lang['admin']['orders_courier_tracking_url'];?>:</strong>        <br />
      <textarea name="courier_tracking" rows="1" class="textbox" style="width: 99%;"><?php echo $orderSum[0]['courier_tracking']; ?></textarea></td>
    </tr>
	<tr>
      <td align="right" class="tdText"><input type="submit" name="submit2" value="<?php if(isset($_GET['edit'])) { echo $lang['admin_common']['edit']; } else { echo $lang['admin_common']['add']; } ?> <?php echo $lang['admin']['orders_order'];?>"  class="submit" /></td>
    </tr>
  </table>
  <?php if ((!empty($orderSum[0]['offline_capture']) || !isset($_GET['edit'])) && function_exists('mcrypt_module_open')) { ?>
  <br />
  <table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
    <tr>
      <td class="tdTitle" colspan="2"><?php echo $lang['admin']['orders_card_details'] ?></td>
    </tr>
    <?php
	require_once('classes'.CC_DS.'cart'.CC_DS.'encrypt.inc.php');
	//echo $orderSum[0]['offline_capture'];
	$decrypt = new encryption(array($orderSum[0]['cart_order_id']));
	$card = unserialize($decrypt->decrypt(base64_decode($orderSum[0]['offline_capture'])));
	$card = (!empty($card)) ? $card : array('card_type' => '', 'card_number' => '', 'card_expire' => '', 'card_valid' => '', 'card_issue' => '', 'card_cvv' => '');
	$lang['admin']['orders_card_cvv'] = 'Security Code';

	$cardfield = 0;
	$showField = detectSSL();
	foreach ($card as $field => $value) {
		$disabled = false;
		if (!$showField) {
			$value		= 'xxx';
			$disabled	= true;
		}
		echo sprintf('<tr><td class="tdText">'.$lang['admin']['orders_'.$field].'</td><td class="tdText"><input type="text" class="textbox" name="'.$field.'" value="%s" %s /></td></tr>', $value, ($disabled) ? 'disabled="disabled"' : '');
		++$cardfield;
	}
	unset($cardfield);
?>
	<tr>
	  <td class="tdText" colspan="2" align="right"><input type="submit" class="submit" name="cc_delete" id="cc_delete" value="Delete Card Details" /></td>
	</tr>
  </table>
  <?php } ?>
  <br />
  <table border="0" cellspacing="1" cellpadding="3" class="mainTable" id="productList" width="100%">
    <tr>
      <td class="tdTitle">&nbsp;</td>
      <td class="tdTitle"><?php echo $lang['admin']['orders_product'];?></td>
      <td class="tdTitle"><?php echo $lang['admin']['orders_code'];?></td>
      <td class="tdTitle"><?php echo $lang['admin']['orders_options'];?></td>
      <td align="center" class="tdTitle"><?php echo $lang['admin']['orders_quantity'];?></td>
      <td width="150" align="center" class="tdTitle"><?php echo $lang['admin']['orders_price'];?></td>
    </tr>
    <?php
  $rows = (isset($_GET['edit'])) ? count($orderInv) : 1;
  if (is_numeric($_POST['prodRowsAdd']) && isset($_POST['prodRowsSubmit']))  $rows=($_POST['prodRowsAdd']+$_POST['currentRowCount']);

  for ($i=0; $i < $rows; ++$i) {
	$cellColor = '';
	$cellColor = cellColor($i);

	if (!isset($orderInv[$i])) {
		$orderInv[$i] = array (
			'name' => $_POST['prodName'][$i],
			'productCode' => $_POST['productCode'][$i],
			'product_options' => $_POST['product_options'][$i],
			'quantity' => $_POST['quantity'][$i],
			'price' => $_POST['price'][$i],

		);
	}


?>
    <tr id="productItem_<?php echo $i ?>">
      <td align="center" valign="top"><?php if ($orderInv[$i]['id']>0) { ?>
        <a href="javascript:toggleProdStatus(<?php echo $i; ?>,'<?php echo sprintf($lang['admin']['orders_prod_will_be_removed'],str_replace(array("'","&#39;"),"\'",$orderInv[$i]['name']));?>','<?php echo sprintf($lang['admin']['orders_prod_wont_be_removed'],str_replace(array("'","&#39;"),"\'",$orderInv[$i]['name']));?>','<?php echo $glob['adminFolder']; ?>/images/del.gif','<?php echo $glob['adminFolder']; ?>/images/no_del.gif');"><img src="<?php echo $glob['adminFolder']; ?>/images/del.gif" id="del[<?php echo $i; ?>]" width="12" height="12" border="0" /></a>
        <?php } else { ?>
        &nbsp;
        <?php } ?>      </td>
      <td valign="top"><input type="hidden" name="id[<?php echo $i; ?>]" value="<?php echo $orderInv[$i]['id']; ?>" />
        <input type="hidden" name="delId[<?php echo $i; ?>]" value="0" />
        <input type="text" name="prodName[<?php echo $i; ?>]" class="textbox" value="<?php echo htmlspecialchars($orderInv[$i]['name']); ?>" />
		<?php if($orderInv[$i]['couponId']>0) {
			$coupon = $db->select('SELECT `code` FROM '.$glob['dbprefix'].'CubeCart_Coupons WHERE `id` = '.$orderInv[$i]['couponId']);

			if($coupon) {
				echo "<br />".$coupon[0]['code'];
			} else {
				echo "<br />".$lang['admin_common']['na'];
			}
		}
		?>
		</td>
      <td valign="top"><input name="productCode[<?php echo $i; ?>]" type="text" class="textbox" value="<?php echo $orderInv[$i]['productCode']; ?>" size="15" /></td>
      <td valign="top">
      <textarea name="product_options[<?php echo $i; ?>]" cols="30" rows="1" class="textbox">
      <?php
      //echo stripslashes(str_replace("&amp;#39;","&#39;",$orderInv[$i]['product_options']));
      echo str_replace('&amp;','&',$orderInv[$i]['product_options']);
      ?>
      </textarea>
      </td>
      <td align="center" valign="top"><input name="quantity[<?php echo $i; ?>]" type="text" class="textbox" style="text-align:center;" value="<?php echo $orderInv[$i]['quantity']; ?>" size="3" /></td>
      <td width="150" align="center" valign="top"><input name="price[<?php echo $i; ?>]" type="text" class="textbox" style="text-align:right;" value="<?php echo $orderInv[$i]['price']; ?>" size="7" /></td>
    </tr>
    <tr id="productItem_<?php echo $i ?>">
      <td align="center" valign="top">&nbsp;</td>
      <td colspan="5" valign="top">
	  <?php
	  if ($orderInv[$i]['digital'] && empty($orderInv[$i]['custom'])) {
		// get digital info
		$query = 'SELECT D.* FROM '.$glob['dbprefix'].'CubeCart_Downloads AS D INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory AS I ON D.productId = I.productId WHERE D.cart_order_id = '.$db->mySQLSafe($_GET['edit']).' AND D.productId = '.$db->mySQLSafe($orderInv[$i]['productId']);

		$download = $db->select($query);

		?>
		<hr />
		<strong><?php echo $lang['admin']['orders_download_link']; ?></strong> [<a href="<?php echo $glob['adminFile']; ?>?_g=orders/orderBuilder&amp;edit=<?php echo $_GET['edit']; ?>&amp;reset=<?php echo $download[0]['id'];?>" class="txtLink" onclick="alert('<?php echo $lang['admin']['orders_warn_reset'];?>')"><?php echo $lang['admin']['orders_reset_link'];?></a>]
		<br />
		<?php
		echo $glob['storeURL']."/index.php?_g=dl&amp;pid=".$download[0]['productId']."&amp;oid=".base64_encode($_GET['edit'])."&amp;ak=".$download[0]['accessKey']
		?>
		  <br />
		 <i><?php echo sprintf($lang['admin']['orders_download_stats'], $download[0]['noDownloads'], formatTime($download[0]['expire'])); ?></i>
		<?php } ?>
		</td>
    </tr>
    <?php
  }
  ?>
  </table>
  <input type="hidden" id="rowCount" value="<?php echo $rows ?>" />
  <table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%" style="border-top: none;">
    <tr>
      <td class="tdText"><input type="hidden" name="currentRowCount" value="<?php echo $rows; ?>" />
        <?php echo $lang['admin_common']['add'];?>
        <input type="text" name="prodRowsAdd" id="prodRowsAdd" value="1" size="2" style="text-align: center;" class="textbox" />
        <input type="submit" name="prodRowsSubmit" value="Product Rows" class="submit" onclick="return addRows('productList', 'prodRowsAdd')" /></td>
      <td align="right" class="tdText"><?php echo $lang['admin']['orders_subtotal']; ?></td>
      <td width="150" align="center"><input name="subtotal" id="subtotal" type="text" class="textbox" style="text-align:right;" value="<?php echo $orderSum[0]['subtotal']; ?>" size="7" /></td>
    </tr>
    <tr>
      <td rowspan="4">&nbsp;</td>
      <td align="right" class="tdText"><?php echo $lang['admin']['orders_discount']; ?></td>
      <td width="150" align="center"><input name="discount" id="discount" type="text" class="textbox" style="text-align:right;" value="<?php echo $orderSum[0]['discount']; ?>" size="7" /></td>
    </tr>
    <tr>
      <td align="right" class="tdText"><?php echo $lang['admin']['orders_shipping']; ?></td>
      <td width="150" align="center"><input name="total_ship" type="text" class="textbox"  style="text-align:right;" value="<?php echo $orderSum[0]['total_ship']; ?>" size="7" /></td>
    </tr>
    <?php
    $config_tax_mod = fetchDbConfig('Multiple_Tax_Mod');
	if ($config_tax_mod['status']) {
		for ($i=0; $i<3; ++$i) {
			$tax_key_name = 'tax'.($i+1).'_disp';
			$tax_key_value = 'tax'.($i+1).'_amt';
			if (!empty($orderSum[0][$tax_key_name])) {
				$name	= $orderSum[0][$tax_key_name];
				$value	= $orderSum[0][$tax_key_value];

			} else if ($i==0) {
				$tax_key_value = 'total_tax';
				$name	= $lang['admin']['orders_total_tax'];
				$value	= $orderSum[0][$tax_key_value];

			} else {
				break;
			}
?>
	<tr>
      <td align="right" class="tdText"><?php echo $name; ?></td>
      <td width="150" align="center"><input name="<?php echo $tax_key_value; ?>" id="<?php echo $tax_key_value; ?>" type="text" class="textbox"  style="text-align:right;" value="<?php echo $value; ?>" size="7" /></td>
    </tr>
<?php
		}
	} else {
?>
	<tr>
      <td align="right" class="tdText"><?php echo $lang['admin']['orders_total_tax']; ?></td>
      <td width="150" align="center"><input name="total_tax" id="total_tax" type="text" class="textbox"  style="text-align:right;" value="<?php echo $orderSum[0]['total_tax']; ?>" size="7" /></td>
    </tr>

<?php

	}
?>

    <tr>
      <td align="right" class="tdText"><?php echo $lang['admin']['orders_grand_total']; ?></td>
      <td width="150" align="center"><input name="prod_total" id="prod_total" type="text" class="textbox" style="text-align:right;" value="<?php echo $orderSum[0]['prod_total']; ?>" size="7" /></td>
    </tr>
    <tr>
      <td colspan="2" align="right">&nbsp;</td>
      <td align="right"><input type="submit" name="submit" value="<?php if(isset($_GET['edit'])) { echo $lang['admin_common']['edit']; } else { echo $lang['admin_common']['add']; } ?> <?php echo $lang['admin']['orders_order'];?>"  class="submit" /></td>
    </tr>
  </table>
</form>
<script type="text/javascript">
function populate() {
//	var json = $('customer_select').readAttribute('json');
}
</script>

<?php
include('modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'admin.php');
?>
</table>