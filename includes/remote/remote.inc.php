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
|	remote.inc.php
|   ========================================
|	Manages remote calls from 3rd party servers
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

/* 
	Fix to force _GET to take preference over _POST. In particular this fixes PayPal Standard $_POST['call'] = _flow bug. 
	_REQUEST takes preference to _POST so it's no good.
*/
$allowed_request = array('module','cmd','type');
foreach($allowed_request as $k) {
	$request[$k] = (isset($_POST[$k]) && !empty($_POST[$k])) ? $_POST[$k] : (isset($_GET[$k]) && !empty($_GET[$k])) ? $_GET[$k] : '' ;
}

## START  MAIN CONTENT
if (in_array($request['type'], array('gateway', 'altCheckout'))) {
	## make sure module is enabled
	if (isset($request['module'])) {
		$query = 'SELECT folder FROM '.$glob['dbprefix'].'CubeCart_Modules WHERE `module` = '.$db->mySQLSafe($request['type']).' AND `folder` = '.$db->mySQLSafe($request['module']).' AND `status` = 1';
		$gatewayStatus = $db->select($query);
	}
	if (!$gatewayStatus) exit;

	$modulePath = 'modules'.CC_DS.$request['type'].CC_DS.sanitizeVar($request['module']).CC_DS;

	$module = fetchDbConfig($gatewayStatus[0]['folder']);

	switch(sanitizeVar($request['cmd'])) {
		## Payment Gateway Callbacks
		case 'call':
			$moduleFullPath = $modulePath.'call.inc.php';
			break;
		## Process Payment
		case 'process':
			$moduleFullPath = $modulePath.'process.inc.php';
			break;
	}

	if (file_exists($moduleFullPath)) {
		require_once 'classes'.CC_DS.'cart'.CC_DS.'order.php';
		$order = new order();
		include_once $moduleFullPath;
	} else {
		die('Module path doesn\'t exist!');
	}
}

if ($request['cmd'] == 'process') {
	/*
	1 = Payment Failed link to try again
	2 = Payment successful and complete
	3 = Payment may or may not have been approved yet
	*/
	if (!isset($paymentResult)) {
		$paymentResult = '3';
	}

	$redirect = $glob['storeURL'].'/index.php?_g=co&_a=confirmed&amp;s='.$paymentResult;

	if(isset($cart_order_id) && !empty($cart_order_id)) {
		$redirect .= '&amp;cart_order_id='.$cart_order_id;
	}
	## Some payment modules mask URL's :( e.g WorldPay/PayPoint (headache)
	if($use_html_meta_refresh) {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Redirecting&hellip;</title>
<meta http-equiv="Refresh" content="0;URL='.$redirect.'" />
<meta http-equiv="Window-target" content="_top" />
</head>

<body>
</body>
</html>';
	} else {
		httpredir($redirect);
	}
}
?>