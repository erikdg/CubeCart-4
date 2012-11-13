<?php
if (!defined('CC_INI_SET')) die("Access Denied");

require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'wpp-UK'.CC_DS.'CallerService.php';

require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'wpp-UK'.CC_DS.'constants.php';

session_start();

if($_GET['cancel']) {
	unset($_SESSION['reshash'],$_SESSION['ec_stage'],$_SESSION['cart_order_id']);
	httpredir("index.php?_g=co&_a=cart");
}

$TRXTYPE = $module['paymentAction'] == "Sale" ? "S" : "A";

if(isset($_REQUEST['token'])) {

	$token = $_REQUEST['token'];

	$nvpStr = "&ACTION[1]=G&TOKEN[".strlen($token)."]=".$token;
	$request_id = md5($token.date('YmdGis')."1");
	//$resArray=hash_call("GetExpressCheckoutDetails",$nvpStr);
	$resArray = hash_call("P",$TRXTYPE,$nvpStr,$request_id);
	
	$_SESSION['reshash']=$resArray;
	$_SESSION['PAYERSTATUS'] = $resArray['PAYERSTATUS'];
	$_SESSION['ec_stage'] = "GetExpressCheckoutDetails";
	
	if($resArray["RESULT"]==0){			
		require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'wpp-UK'.CC_DS."GetExpressCheckoutDetails.php";
		exit;				 
	} else  { 
		include(CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'wpp-UK'.CC_DS."APIError.php");
		exit;
	}
	   	
} else {

	$parts = explode(",",base64_decode($_GET['ccb']));
	$paymentAmount = $parts[0];
	$currencyCodeType = $parts[1];
	$paymentType = $module['paymentAction'];
	
	$storeURL = (!empty($config['storeURL_SSL']) && $config['ssl']) ? $config['storeURL_SSL'] : $glob['storeURL'];
	
	$returnURL = $storeURL.'/index.php?_g=rm&type=altCheckout&cmd=process&module=PayPal_Pro&currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&paymentAmount='.$paymentAmount;
	$cancelURL = $storeURL."/index.php?_g=rm&type=altCheckout&cmd=process&module=PayPal_Pro&paymentType=".$paymentType."&cancel=1";
	
	$AMT = sprintf("%.2f",$paymentAmount);
	
	$nvpStr =	"&ACTION[1]=S".
				"&CURRENCY[3]=GBP".
				"&AMT[".strlen($AMT)."]=".$AMT.
				"&RETURNURL[".strlen($returnURL)."]=".$returnURL.
				"&CANCELURL[".strlen($cancelURL)."]=".$cancelURL;
				
	if($module['confAddress']==1) {
		$nvpStr.="&REQCONFIRMSHIPPING[1]=1";
	} else {
		$nvpStr.="&REQCONFIRMSHIPPING[1]=0";
	}
	
	
	$BUTTONSOURCE = "CubeCart_Cart_PRO2EC";
	
	$nvpStr.="&BUTTONSOURCE[".strlen($BUTTONSOURCE)."]=".$BUTTONSOURCE;
	
	$request_id = md5($_POST['ACCT'].$paymentAmount.date('YmdGis')."1");
	//$resArray = hash_call("SetExpressCheckout",$nvpStr);
	$resArray = hash_call("P",$TRXTYPE,$nvpStr,$request_id);
	
	$_SESSION['reshash'] = $resArray;
	$_SESSION['ec_stage'] = "SetExpressCheckout";
	
	if($resArray["RESULT"]==0){
			// Redirect to paypal.com here
			$token = urldecode($resArray["TOKEN"]);
			$payPalURL = PAYPAL_URL.$token;
			httpredir($payPalURL);
	} else  {
			 include(CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'altCheckout'.CC_DS.'PayPal_Pro'.CC_DS.'wpp-UK'.CC_DS."APIError.php");
			exit;
	}
	   
}
?>

