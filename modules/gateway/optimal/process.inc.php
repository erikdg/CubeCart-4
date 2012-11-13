<?php
if (isset($_GET['cart_order_id']) && !empty($_GET['cart_order_id'])) {
	$status = $db->select("SELECT `status` FROM `".$glob['dbprefix']."CubeCart_order_sum` WHERE `cart_order_id` = ".$db->MySQLSafe($_GET['cart_order_id']));
	if ($status) {
		switch ((int)$status[0]['status']) {
			case 4:
				$paymentResult = 1;
				break;
			case 2:
			case 3:
				$paymentResult = 2;
				break;
			default:
				$paymentResult = 3;
		}
	}
} else {
	$paymentResult = 3;
}
$cart_order_id = $_GET['cart_order_id'];
?>