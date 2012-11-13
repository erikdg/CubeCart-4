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
|	Process eWay Gateway
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die('Access Denied');

	if($module['test']) {
		$module['customerid'] 	= '87654321';
		$module['customername']	= 'TestAccount';
	}

	$querystring='CustomerID='.$module['customerid'].'&UserName='.$module['customername'].'&AccessPaymentCode='.$_REQUEST['AccessPaymentCode'];

	$postdomain = ($module['mode']=='NZ') ? 'nz.ewaygateway.com' : 'payment.ewaygateway.com';

	$posturl	= 'https://'.$postdomain.'/Result/?'.$querystring;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $posturl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	if (CURL_PROXY_REQUIRED == 'True') {
		$proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
		curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
		curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
	}

	  function fetch_data($string, $start_tag, $end_tag) {
		$position = stripos($string, $start_tag);
		$str = substr($string, $position);
		$str_second = substr($str, strlen($start_tag));
		$second_positon = stripos($str_second, $end_tag);
		$str_third = substr($str_second, 0, $second_positon);
		$fetch_data = trim($str_third);
		return $fetch_data;
	  }

	 $response = curl_exec($ch);

	 $authecode = fetch_data($response, '<authCode>', '</authCode>');
	 $responsecode = fetch_data($response, '<responsecode>', '</responsecode>');
	 $retrunamount = fetch_data($response, '<returnamount>', '</returnamount>');
	 $trxnnumber = fetch_data($response, '<trxnnumber>', '</trxnnumber>');
	 $trxnstatus = fetch_data($response, '<trxnstatus>', '</trxnstatus>');
	 $trxnresponsemessage = fetch_data($response, '<trxnresponsemessage>', '</trxnresponsemessage>');

	 $merchantoption1 = fetch_data($response, '<merchantoption1>', '</merchantoption1>');
	 $merchantoption2 = fetch_data($response, '<merchantoption2>', '</merchantoption2>');
	 $merchantoption3 = fetch_data($response, '<merchantoption3>', '</merchantoption3>');
	 $merchantreference = fetch_data($response, '<merchantreference>', '</merchantreference>');
	 $merchantinvoice = fetch_data($response, '<merchantinvoice>', '</merchantinvoice>');


	$cart_order_id = $merchantinvoice; // Used in remote.php $cart_order_id is important for failed orders

	$customer = $db->select('SELECT `customer_id` FROM `'.$glob['dbprefix'].'CubeCart_order_sum` WHERE `cart_order_id` = '.$db->MySQLSafe($cart_order_id));

	$transData['customer_id']	= $customer[0]['customer_id'];
	$transData['gateway']		= 'eWay';
	$transData['trans_id']		= $trxnnumber;
	$transData['order_id']		= $cart_order_id;
	$transData['amount']		= $retrunamount;
	$transData['notes']			= $trxnresponsemessage;

	$success_responsecodes = array(
		'00', // Transaction Approved
		'08', // Honour With Identification
		'10', // Approved For Partial Amount
		'11', // Approved, VIP
		'16'  // Approved, Update Track 3
	);

	if(in_array($responsecode,$success_responsecodes)) {
		$order->orderStatus(3,$cart_order_id);
		$paymentResult = 2; // Success
		$transData['status'] = 'Success';
	} else {
		$paymentResult = 1; // Fail
		$transData['status'] = 'Fail';
	}
	$order->storeTrans($transData);

?>