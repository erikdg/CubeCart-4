<?php

/************************************************
* HSBC API Module by Adam Harris @ XOMY Limited *
* http://www.xomy.com | adam@xomy.com           *
*                                               *
* Before making any modifications, please       *
* contact me at the above email so that we can  *
* discuss the implications and advantages for   *
* the module.                                   *
*                                               *
* This module is released for the benefit of    *
* the community and should not be sold.         *
*                                               *
* This module is not released under GPL and     *
* cannot be redistributed without permission    *
* from myself.                                  *
************************************************/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

	$pasData = explode('|', base64_decode($_POST['MD']));

	switch($_POST['CcpaResultsCode']) {
		// Payer authentication was successful.
		case '0':
			$pasCondig['display'] = 'Successful';
			$pasConfig['PayerSecurityLevel'] = '2';
			$pasConfig['PayerAuthenticationCode'] = $_POST['CAVV'];
			$pasConfig['PayerTxnId'] = $_POST['XID'];
			$pasConfig['CardholderPresentCode'] = '13';
			break;

		// The cardholder�s card was not within a participating BIN range.
		case '1':
			$pasCondig['display'] = 'Not Participating';
			$pasConfig['PayerSecurityLevel'] = '5';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '13';
			break;

		// The cardholder was in a participating BIN range, but was not enrolled in 3-D Secure.
		case '2':
			$pasCondig['display'] = 'Not Enrolled';
			$pasConfig['PayerSecurityLevel'] = '1';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '13';
			break;

		// The cardholder was not enrolled in 3-D Secure. However, the cardholder was authenticated using the 3-D Secure attempt server.
		case '3':
			$pasCondig['display'] = 'Enrolled Successfully';
			$pasConfig['PayerSecurityLevel'] = '6';
			$pasConfig['PayerAuthenticationCode'] = $_POST['CAVV'];
			$pasConfig['PayerTxnId'] = $_POST['XID'];
			$pasConfig['CardholderPresentCode'] = '13';
			break;

		// The cardholder was enrolled in 3-D Secure. A PARes has not yet been received for this transaction.
		case '4':
			$pasCondig['display'] = 'PAS Timed Out';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// The cardholder has failed payer authentication.
		case '5':
			//$pasCondig['display'] = 'Failed';
			//header('Location: '.$config['storeURL_SSL'].'/confirmed.php?f=1');
			//exit;

		// Signature validation of the results from the ACS failed.
		case '6':
			//$pasCondig['display'] = 'Failed';
			//header('Location: '.$config['storeURL_SSL'].'/confirmed.php?f=1');
			//exit;

		// The ACS was unable to provide authentication results.
		case '7':
			$pasCondig['display'] = 'No Result';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// The CCPA failed to communicate with the Directory Server.
		case '8':
			$pasCondig['display'] = '3-D Secure Unavailable';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// The CCPA was unable to interpret the results from payer authentication or enrolment verification.
		case '9':
			$pasCondig['display'] = 'Corrupted Result';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// The CCPA failed to locate or access configuration information for this merchant.
		case '10':
			$pasCondig['display'] = '3-D Secure Unavailable';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// Data submitted or configured in the CCPA has failed validation checks.
		case '11':
			$pasCondig['display'] = '3-D Secure Unavailable';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// Unexpected system error from CCPA.
		case '12':
			$pasCondig['display'] = 'System Error';
			$pasConfig['PayerSecurityLevel'] = '4';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;

		// Indicates that card submitted is not recognised, or the PAS does not support the card type.
		case '14':
			$pasCondig['display'] = 'Not Supported';
			$pasConfig['PayerSecurityLevel'] = '7';
			$pasConfig['PayerAuthenticationCode'] = '';
			$pasConfig['PayerTxnId'] = '';
			$pasConfig['CardholderPresentCode'] = '';
			break;
	}

	include dirname(__FILE__).CC_DS.'func_https_libcurl.php';

	$module = fetchDbConfig('HSBC');

	switch ($module['test']) {
		case 2:
			## Testmode - Always Declined
			$pp_mode = 'N';
			break;
		case "1":
			## Testmode - Always Approved
			$pp_mode = 'Y';
			break;
		case 0:
		default:
			## Live Mode
			$pp_mode = 'P';
	}

	$domen		= $module['url'];
	$port		= '443';
	$pp_login	= $module['userID'];
	$pp_pass	= $module['passPhrase'];
	$pp_client	= $module['acNo'];
	$curr		= '826';

	switch ($module['authmode']) {
		case '1':
			$authMode = 'PreAuth';
			break;
		case '0':
		default:
			$authMode = 'Auth';
	}

	## Rewrite me for SimpleXML/XMLWriter...

	$XPost[] = "<?xml version='1.0' encoding='UTF-8' ?>";
	$XPost[] = "	<EngineDocList>";
	$XPost[] = "	<DocVersion>1.0</DocVersion>";
	$XPost[] = "	<EngineDoc>";
	$XPost[] = "		<ContentType>OrderFormDoc</ContentType>";

	$XPost[] = "		<User>";
	$XPost[] = "			<Name>$pp_login</Name>";
	$XPost[] = "			<Password>$pp_pass</Password>";
	$XPost[] = "			<ClientId DataType='S32'>$pp_client</ClientId>";
	$XPost[] = "		</User>";

	$XPost[] = "		<Instructions>";
	$XPost[] = "			<Pipeline>Payment</Pipeline>";
	$XPost[] = "		</Instructions>";

	$XPost[] = "		<OrderFormDoc>";
	$XPost[] = "			<Mode>$pp_mode</Mode>";
	$XPost[] = "			<Id>".$pasData[17]."</Id>";
	$XPost[] = "			<Consumer>";
	$XPost[] = "				<Email>".$pasData[0]."</Email>";
	$XPost[] = "				<BillTo>";
	$XPost[] = "					<Location>";
	$XPost[] = "						<TelVoice>".$pasData[1]."</TelVoice>";
	$XPost[] = "						<Address>";
	$XPost[] = "							<Name>".$pasData[2]." ".$pasData[3]."</Name>";
	$XPost[] = "							<City>".$pasData[4]."</City>";
	$XPost[] = "							<Street1>".$pasData[5]."</Street1>";
	$XPost[] = "							<Street2>".$pasData[6]."</Street2>";
	$XPost[] = "							<StateProv>".$pasData[7]."</StateProv>";
	$XPost[] = "							<PostalCode>".$pasData[8]."</PostalCode>";
	$XPost[] = "						</Address>";
	$XPost[] = "					</Location>";
	$XPost[] = "				</BillTo>";

	$XPost[] = "				<PaymentMech>";
	$XPost[] = "					<CreditCard>";
	$XPost[] = "						<Cvv2Indicator>".(!empty($pasData[9])?1:2)."</Cvv2Indicator>";
	$XPost[] = "						<Cvv2Val>".$pasData[9]."</Cvv2Val>";
	$XPost[] = "						<Expires DataType='ExpirationDate' Locale='840'>".$pasData[10]."</Expires>";
	if ((($pasData[14] == 9)||($pasData[14] == 10))&&($pasData[11])){
		$XPost[] = "					<IssueNum>".$pasData[11]."</IssueNum>";
	}
	$XPost[] = "						<Number>".$pasData[12]."</Number>";
	
	if ((($pasData[14] == 9)||($pasData[14] == 10))&&($pasData[13] !== "00/00")){
		$XPost[] = "					<StartDate DataType='StartDate' Locale='840'>".$pasData[13]."</StartDate>";
	} 
	
	$XPost[] = "						<Type>".$pasData[14]."</Type>";
	$XPost[] = "					</CreditCard>";
	$XPost[] = "				</PaymentMech>";
	$XPost[] = "			</Consumer>";
	$XPost[] = "			<Transaction>";
	$XPost[] = "				<Type>".$authMode."</Type>";
	$XPost[] = "				<ChargeDesc1></ChargeDesc1>";
	$XPost[] = "				<CurrentTotals>";
	$XPost[] = "					<Totals>";
	$XPost[] = "                        <Total DataType='Money' Currency='".$curr."'>".preg_replace('#[^0-9]#i', '', $pasData[15])."</Total>";
	$XPost[] = "					</Totals>";
	$XPost[] = "				</CurrentTotals>";
	// PAS
	if ($pasData[14] != 8) {
		$XPost[] = "				<PayerSecurityLevel>".$pasConfig['PayerSecurityLevel']."</PayerSecurityLevel>";
		$XPost[] = "				<PayerAuthenticationCode>".$pasConfig['PayerAuthenticationCode']."</PayerAuthenticationCode>";
		$XPost[] = "				<PayerTxnId>".$pasConfig['PayerTxnId']."</PayerTxnId>";
		$XPost[] = "				<CardholderPresentCode>".$pasConfig['CardholderPresentCode']."</CardholderPresentCode>";
	}
	$XPost[] = "			</Transaction>";
	$XPost[] = "		</OrderFormDoc>";
	$XPost[] = "	</EngineDoc>";
	$XPost[] = "</EngineDocList>";
	
	$pst = array('CLRCMRC_XML='.implode('', $XPost));
	list($a, $return) = func_https_request('POST', 'https://'.$domen.":$port/", $pst);
	$return	= str_replace("\n", '', $return);

	$orderSum = $order->getOrderSum($_GET['cart_order_id']);

## Transaction Data
$transData['customer_id']	= $orderSum['customer_id'];
$transData['order_id']		= $orderSum['cart_order_id'];
$transData['amount']		= $orderSum['prod_total'];
$transData['gateway']		= $module['desc'];


if (class_exists('SimpleXMLElement')) {
	## Spiffy new PHP 5 procedures
	$xmldata = new SimpleXMLElement($return);
	$cart_order_id = sanitizeVar($_GET['cart_order_id']);
	if (in_array((string)$xmldata->EngineDoc->Overview->TransactionStatus, array('A', 'C'))) {
		# Approved - Yay!
		$paymentResult = 2;
		$order->orderStatus(3, $_GET['cart_order_id']);

		$transData['notes']		= 'Card charged successfully.';
		$transData['status']	= 'Success';
		$transData['trans_id']	= (string)$xmldata->EngineDoc->Overview->AuthCode;
	} elseif($xmldata->EngineDoc->Overview->CcErrCode == 502) {
		// fraud review!!!
		$paymentResult = 3;
		$transData['notes']		= 'Fraud Review';
		$transData['status']	= 'Review';
		$transData['trans_id']	= (string)$xmldata->EngineDoc->Overview->AuthCode;
	} else {
		# Fail!
		$paymentResult = 1;

		if (stristr((string)$xmldata->EngineDoc->OrderFormDoc->FraudInfo->Alerts->Action, 'Reject')) {
			# Fraud Alert - Epic Fail!
			$transData['notes']	= (string)$xmldata->EngineDoc->Overview->CcReturnMsg;
			$order->orderStatus(5, $_GET['cart_order_id']);
		} else {
			$transData['notes']	= 'Payment failed.';
		}
		$transData['status']	= 'Fail';
		$transData['trans_id']	= 'n/a';
	}
} else {
	## PHP4 style - a bit of a cludge, but does the magic
	preg_match('#<TransactionStatus DataType\="String">([A-Z])</TransactionStatus>#i', $return, $out);
	$authCode = strtoupper($out[1]);

	if (in_array($authCode, array('A', 'C'))) {
		## Approved
		$paymentResult = 2;
		$order->orderStatus(3, $_GET['cart_order_id']);

		$transData['notes']		= 'Card charged successfully.';
		$transData['status']	= 'Success';
		$transData['trans_id']	= $authCode;
	} else {
		## Failed
	#	if ($authCode == 'F') {
	#		# Fraud?
	#	#	$order->orderStatus(5, $_GET['cart_order_id']);
	#	}
		$paymentResult = 1;

		$transData['notes']		= 'Payment failed.';
		$transData['status']	= 'Fail';
		$transData['trans_id']	= 'n/a';
	}
}
$order->storeTrans($transData);

?>