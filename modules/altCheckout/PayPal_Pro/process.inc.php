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
|	Process PayPal Express Checkout
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
$ppGate = substr($module['mode'],0,2);
if($_GET['payment']==1) {
	$orderSum = $order->getOrderSum($_GET['cart_order_id']);
	include("modules".CC_DS."altCheckout".CC_DS."PayPal_Pro".CC_DS."wpp-".$ppGate.CC_DS."DoExpressCheckoutPayment.php");
} else {
	include("modules".CC_DS."altCheckout".CC_DS."PayPal_Pro".CC_DS."wpp-".$ppGate.CC_DS."ReviewOrder.php");
}
?>