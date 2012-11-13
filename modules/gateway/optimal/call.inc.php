<?php
// return 204 status so it knows there is no content to return
header("HTTP/1.0 204 No Content");

$decodedMessage = base64_decode($_GET['encodedMessage']);

// Decode signature received back and check against shop key to verify the message was not tampered with
$computedSignature = base64_encode(hash_hmac('SHA1', $decodedMessage, $module['sharedKey'], TRUE));

if($computedSignature == $_GET['signature']) {
	/*
	Example checkout response:
	<?xml version="1.0" encoding="UTF-8"?>
	<checkoutResponse xmlns="www.optimalpayments.com/checkout">
		<confirmationNumber>11115555</confirmationNumber> 
		<merchantRefNum>12312331</merchantRefNum>
		<accountNum>123456789</accountNum> 
		<cardType>VI</cardType> 
		<decision>ACCEPTED</decision> 
		<code>0</code>
		<description>Transaction processed successfully.</description>
		<txnTime>2009-09-17T09:30:47.0Z</txnTime>
	</CheckoutResponse>
	*/
	$xmldata = new SimpleXMLElement($decodedMessage);
	$decoded_merchantRefNum = base64_decode($xmldata->merchantRefNum);
	
	$order_data = $db->select(sprintf("SELECT `cart_order_id`, `prod_total`, `customer_id` FROM `%sCubeCart_order_sum` WHERE `sec_order_id` = %s",$glob['dbprefix'],$db->MySQLSafe($xmldata->merchantRefNum)));
	
switch (strtoupper($xmldata->decision)) {
	case "ACCEPTED":
		$order->orderStatus(2, $order_data[0]['cart_order_id']);
	break;
	case "ERROR":
		// Keep status as pending I think
	break;
	case "DECLINED":
		$order->orderStatus(4, $order_data[0]['cart_order_id']);
	break;
} 
$transData['status'] = $xmldata->decision;
$transData['customer_id'] = $order->customer_id;
$transData['gateway'] = 'Optimal Payments';
$transData['trans_id'] = $xmldata->confirmationNumber;
$transData['order_id'] = $order_data[0]['cart_order_id'];
$transData['amount'] = $order_data[0]['prod_total'];
$transData['notes'] = $xmldata->description;
$order->storeTrans($transData);
}
?>