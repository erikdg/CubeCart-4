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
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	form.inc.php
|   ========================================
|	eWay Processing
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

if($_GET['process']==1) {

	$transData['customer_id'] = $orderSum['customer_id'];
	$transData['order_id'] = $orderSum['cart_order_id'];
	$transData['amount'] = $orderSum['prod_total'];
	$transData['gateway'] = "eWay";

	// first check card
	require("classes".CC_DS."validate".CC_DS."validateCard.php");
	$card = new validateCard();

	$cardNo			= $_POST['cardNumber'];
	$issueNo		= 0;
	$issueDate		= 0;
	$issueFormat	= 4;
	$expireDate		= str_pad(trim($_POST['expirationYear']), 2, '0', STR_PAD_LEFT).str_pad(trim($_POST['expirationMonth']), 2, '0', STR_PAD_LEFT);
	$expireFormat	= 4;
	$scReqd			= true;
	$securityCode	= $_POST['cvc2'];

	$card = $card->check($cardNo,
						$issueNo,
						$issueDate,
						$issueFormat,
						$expireDate,
						$expireFormat,
						$scReqd,
						$securityCode);

	if($module['validation']==1 && $card['response']=="FAIL"){

		$errorMsg = '';

		foreach($card['error'] as $val){
			$errorMsg .= $val.'<br />';
		}

	} else {

		require_once('EwayPayment.php');

		if ($module['test']==true) {
			$module['acNo'] = '87654321';
			$gatewayURL = 'https://www.eway.com.au/gateway_cvn/xmltest/TestPage.asp';
		} else {
			$gatewayURL = 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp';
		}

		$eway = new EwayPayment($module['acNo'], $gatewayURL);
		$eway->setCustomerFirstname($_POST['firstName']);
		$eway->setCustomerLastname($_POST['lastName']);
		$eway->setCustomerEmail($_POST['emailAddress']);
		$eway->setCustomerAddress($_POST['addr1'].' '.$_POST['addr2'].', '.$_POST['city'].', '.$_POST['state'].', '.$_POST['country']);
		$eway->setCustomerPostcode($_POST['postalCode']);
		$eway->setCustomerInvoiceDescription('Payment for order# '.$orderSum['cart_order_id']);
		$eway->setCustomerInvoiceRef($orderSum['cart_order_id']);
		$eway->setCardHoldersName($_POST['firstName'].' '.$_POST['lastName']);
		$ccdelimeters = array(' ','-');
		$eway->setCardNumber(str_replace($ccdelimeters,'',$_POST['cardNumber']));
		$eway->setCardExpiryMonth($_POST['expirationMonth']);
		$eway->setCardExpiryYear($_POST['expirationYear']);
		$eway->setCVN($_POST['cvc2']);
		$eway->setTrxnNumber(str_replace('-','',$orderSum['cart_order_id']));

		// Eway takes payments in Cents
		$cents = $orderSum['prod_total'] * 100;
		$eway->setTotalAmount($cents);

			if($eway->doPayment() == EWAY_TRANSACTION_OK) {
				$order->orderStatus(3,$orderSum['cart_order_id']);

				$transData['trans_id'] = $eway->myTrxnNumber;
				$transData['status'] = 'Success';
				$transData['notes'] = $eway->getErrorMessage();
				$order->storeTrans($transData);

				httpredir('index.php?_g=co&_a=confirmed&s=2');
			} else {
				$errorMsg = $eway->getErrorMessage();
			}

	}

	$transData['trans_id'] = '';
	$transData['status'] = 'Fail';
	$transData['notes'] = $errorMsg;
	$order->storeTrans($transData);

} elseif($module['mode']!='AU') {

		$pathvalue='http://www.ewaypayment.com.php5-2.dfw1-2.websitetestlink.com/php/';

		$billingName = makeName($orderSum['name']);

		if($module['test']) {
			$module['customerid'] 	= '87654321';
			$module['customername']	= 'TestAccount';
		}


		$eway_params = array (
			'CustomerID' => $module['customerid'],
			'UserName' => $module['customername'],
			'Amount' => $orderSum['prod_total'],
			'Currency' => $config['defaultCurrency'],
			'PageTitle' => $config['siteTitle'],
		    'PageDescription' => '',
			'PageFooter' => '',
			'Language' => 'EN',
			'CompanyName' => $config['storeName'],
			'CustomerFirstName' => $billingName[2],
		    'CustomerLastName' => $billingName[3],
			'CustomerAddress' => $orderSum['add_1'].' '.$orderSum['add_2'],
			'CustomerCity' => $orderSum['town'],
			'CustomerState' => $orderSum['county'],
			'CustomerPostCode' => $orderSum['postcode'],
			'CustomerCountry'=> getCountryFormat($orderSum['country'],'id','printable_name'),
			'CustomerEmail' => $orderSum['email'],
			'CustomerPhone' => $orderSum['phone'],
			'InvoiceDescription' => '',
			'CancelURL' => $GLOBALS['storeURL'].'/index.php?_g=co&_a=step3&contShop=1&cart_order_id' . $orderSum['cart_order_id'],
			'ReturnUrl' => $GLOBALS['storeURL'].'/modules/gateway/eway/return.php',
			'CompanyLogo' => $module['companylogo'],
			'PageBanner' => $module['pagebanner'],
			'MerchantReference' => $orderSum['cart_order_id'],
			'MerchantInvoice' => $orderSum['cart_order_id'],
			'MerchantOption1' => '',
			'MerchantOption2' => '',
			'MerchantOption3' => '',
			'ModifiableCustomerDetails' => ''		
		);
		
	    $postdomain 	= ($module['mode']=='NZ') ? 'nz.ewaygateway.com' : 'payment.ewaygateway.com';
	    $posturl		= 'https://'.$postdomain.'/Request/?'.http_build_query($eway_params, '', '&');

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

		$response = curl_exec($ch);

		function fetch_data($string, $start_tag, $end_tag) {
			$position = stripos($string, $start_tag);
			$str = substr($string, $position);
			$str_second = substr($str, strlen($start_tag));
			$second_positon = stripos($str_second, $end_tag);
			$str_third = substr($str_second, 0, $second_positon);
			$fetch_data = trim($str_third);
			return $fetch_data;
		}

		$responsemode = fetch_data($response, '<result>', '</result>');
	    $responseurl = fetch_data($response, '<uri>', '</uri>');

		if($responsemode=='True') {
		  httpredir($responseurl);
		} else {
		  die('There has been an error.'.htmlspecialchars($response));
		}
}


$formTemplate = new XTemplate ('modules/gateway/'.$_POST['gateway'].'/form.tpl','',null,'main',true,true);

if(isset($errorMsg)) {
	$formTemplate->assign('LANG_ERROR',$errorMsg);
	$formTemplate->parse('form.error');
}

$billingName = makeName($orderSum['name']);

$formTemplate->assign('VAL_FIRST_NAME',$billingName[2]);
$formTemplate->assign('VAL_LAST_NAME',$billingName[3]);
$formTemplate->assign('VAL_EMAIL_ADDRESS',$orderSum['email']);
$formTemplate->assign('VAL_ADD_1',$orderSum['add_1']);
$formTemplate->assign('VAL_ADD_2',$orderSum['add_2']);
$formTemplate->assign('VAL_CITY',$orderSum['town']);
$formTemplate->assign('VAL_COUNTY',$orderSum['county']);
$formTemplate->assign('VAL_POST_CODE',$orderSum['postcode']);


$countries = $db->select('SELECT `id`, `iso`, `printable_name` FROM '.$glob['dbprefix'].'CubeCart_iso_countries ORDER BY `printable_name`');

	for($i = 0, $maxi = count($countries); $i < $maxi; ++$i){

		if($countries[$i]['id'] == $orderSum['country']){
			$formTemplate->assign('COUNTRY_SELECTED',"selected='selected'");
		} else {
			$formTemplate->assign('COUNTRY_SELECTED','');
		}

		$formTemplate->assign('VAL_COUNTRY_ISO',$countries[$i]['iso']);

		$countryName = $countries[$i]['printable_name'];

		if(strlen($countryName)>20){
			$countryName = substr($countryName,0,20).'&hellip;';
		}

		$formTemplate->assign('VAL_COUNTRY_NAME',$countryName);
		$formTemplate->parse('form.repeat_countries');
	}

	$formTemplate->assign('LANG_CC_INFO_TITLE',$lang['gateway']['cc_info_title']);
	$formTemplate->assign('LANG_FIRST_NAME',$lang['gateway']['first_name']);
	$formTemplate->assign('LANG_LAST_NAME',$lang['gateway']['last_name']);
	//$formTemplate->assign('LANG_CARD_TYPE',$lang['gateway']['card_type']);
	$formTemplate->assign('LANG_CARD_NUMBER',$lang['gateway']['card_number']);
	$formTemplate->assign('LANG_EXPIRES',$lang['gateway']['expires']);
	if(!empty($_POST['expirationMonth']) && !empty($_POST['expirationYear'])) {
		$formTemplate->assign('VAL_MONTH',str_pad(trim($_POST['expirationMonth']), 2, '0', STR_PAD_LEFT));
		$formTemplate->assign('VAL_YEAR',str_pad(trim($_POST['expirationYear']), 2, '0', STR_PAD_LEFT));
	}
	$formTemplate->assign('LANG_MMYY',$lang['gateway']['mmyy']);
	$formTemplate->assign('LANG_SECURITY_CODE',$lang['gateway']['security_code']);
	$formTemplate->assign('LANG_CUST_INFO_TITLE',$lang['gateway']['customer_info']);
	$formTemplate->assign('LANG_EMAIL',$lang['gateway']['email']);
	$formTemplate->assign('LANG_ADDRESS',$lang['gateway']['address']);
	$formTemplate->assign('LANG_CITY',$lang['gateway']['city']);
	$formTemplate->assign('LANG_STATE',$lang['gateway']['state']);
	$formTemplate->assign('LANG_ZIPCODE',$lang['gateway']['zipcode']);
	$formTemplate->assign('LANG_COUNTRY',$lang['gateway']['country']);
	$formTemplate->assign('LANG_OPTIONAL',$lang['gateway']['optional']);


$formTemplate->parse('form');
$formTemplate = $formTemplate->text('form');
?>