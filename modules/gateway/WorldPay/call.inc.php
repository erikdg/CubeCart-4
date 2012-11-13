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
|	process.inc.php
|   ========================================
|	Process Worldpay Payment
+--------------------------------------------------------------------------
*/
// read the post from PayPal system and add 'cmd'
if (!defined('CC_INI_SET')) die("Access Denied");
$customer = $db->select('SELECT `customer_id` FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `email` = '.$db->MySQLSafe($_REQUEST['email']));


if ($customer) {

	$cart_order_id = $_REQUEST['cartId'];

	$transData['customer_id'] = $customer[0]['customer_id'];
	$transData['gateway'] = 'WorldPay';
	$transData['trans_id'] = $_REQUEST['transId'];
	$transData['order_id'] = $cart_order_id;
	$transData['amount'] = $_REQUEST['amount'];

	if ($_REQUEST['transStatus'] == 'Y') {
		$transData['status'] = 'Success';
		$paymentResult = 2;
		$order->orderStatus(3,$cart_order_id);
		$transData['notes'] = 'Payment was successful.';
	} else {
		$transData['status'] = 'Fail';
		$paymentResult = 1;
		$order->orderStatus(1,$cart_order_id);
		$transData['notes'] = 'Payment unsuccessful. More information may be available in the WorldPay control panel.';
	}

	$order->storeTrans($transData);
} else {
	die('<strong>Fatal Error:</strong> Customer not found from email address.');
}
?>
