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
|	Process PayPal Gateway
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
$status = $db->select('SELECT `status` FROM `'.$glob['dbprefix'].'CubeCart_order_sum` WHERE `cart_order_id` = '.$db->MySQLSafe($_GET['cart_order_id']));

if($status) {

	$cart_order_id = $_GET['cart_order_id']; // Used in remote.php $cart_order_id is important for failed orders

	if($status[0]['status']==2 || $status[0]['status']==3) {
		$paymentResult = 2; // Success
	} elseif($_GET['c']==1) {
		$paymentResult = 1;
	} else {
		$paymentResult = 3; // Not processed yet or unknown
	}
} else {
	die('<strong>Fatal Error:</strong> Order id not found!');
}
?>