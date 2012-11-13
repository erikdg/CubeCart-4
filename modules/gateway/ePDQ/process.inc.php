<?php
if (!defined('CC_INI_SET')) die('Access Denied');
if ($status = $db->select("SELECT `status` FROM `".$glob['dbprefix']."CubeCart_order_sum` WHERE `cart_order_id` = ".$db->MySQLSafe($_GET['cart_order_id'])." LIMIT 1;")) {
	$cart_order_id = $_GET['cart_order_id'];
	
	switch($status[0]['status']) {
		case 2:
		case 3:
			$paymentResult = 2;
			break;
		default:
			$paymentResult = 1;
	}
} else {
	die("<strong>Fatal Error:</strong> Order ID not found!");
}