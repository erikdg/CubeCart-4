<?php
if (!defined('CC_INI_SET')) die('Access Denied');

//if ($_SERVER['PHP_AUTH_USER'] == $module['post_user'] && $_SERVER['PHP_AUTH_PW'] == $module['post_pass']) {
	$proceed 	= true;
	if ($_POST['clientid'] != $module['clientid'] || !isset($_POST['transactionstatus'])) {
		$proceed = false;
	}
	if ((bool)$proceed) {
		$order->getOrderSum($_POST['oid']);
		
		switch ($_POST['transactionstatus']) {
			case 'Success':
				$transData['status']	= 'Success';
				$order->orderStatus(3, $_POST['oid']);
				break;
			default:
				$transData['status']	= 'Declined';
				$order->orderStatus(4, $_POST['oid']);
		}
		
		$transData['notes']		= $_POST['transactionstatus'];
		$transData['customer_id'] = $order->orderSum['customer_id'];
		$transData['gateway']	= 'ePDQ';
		$transData['trans_id']	= '';
		$transData['order_id']	= $_POST['oid'];
		$transData['amount']	= $_POST['total'];
		$order->storeTrans($transData);
	}
//} else {
//	header('WWW-Authenticate: Basic realm="Payment"');
//	header('HTTP/1.0 401 Unauthorized');
//}