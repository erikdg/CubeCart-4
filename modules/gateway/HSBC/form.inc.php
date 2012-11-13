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

$module = fetchDbConfig('HSBC');

include CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'gateway'.CC_DS.'HSBC'.CC_DS.'func_https_libcurl.php';

if (isset($_GET['process'])) {


	// first check card
	require('classes'.CC_DS.'validate'.CC_DS.'validateCard.php');

	$card = new validateCard();

	$cardNo			= trim($_POST['cardNumber']);
	$issueNo		= true;
	$issueDate		= str_pad(trim($_POST['startYear']), 2, '0', STR_PAD_LEFT).str_pad(trim($_POST['startMonth']), 2, '0', STR_PAD_LEFT);
	$issueFormat	= 4;
	$expireDate		= trim($_POST['expirationYear']).str_pad(trim($_POST['expirationMonth']), 2, '0', STR_PAD_LEFT);
	$expireFormat	= 4;
	if($module['reqCvv']==1) {
		$scReqd			= true;
	} else {
		$scReqd			= false;
	}
	$securityCode	= trim($_POST['cvc2']);

	$card = $card->check($cardNo,
						$issueNo,
						$issueDate,
						$issueFormat,
						$expireDate,
						$expireFormat,
						$scReqd,
						$securityCode);

	if($module['validation']==1 && $card['response']=='FAIL') {

		$errorMsg = '';

		foreach($card['error'] as $val) {
			$errorMsg .= $val.'<br />';
		}


	} else {


	$ccPassthru[] = $_POST['emailAddress'];
	$ccPassthru[] = $_POST['phone'];
	$ccPassthru[] = $_POST['firstName'];
	$ccPassthru[] = $_POST['lastName'];
	$ccPassthru[] = $_POST['city'];
	$ccPassthru[] = $_POST['addr1'];
	$ccPassthru[] = $_POST['addr2'];
	$ccPassthru[] = $_POST['state'];
	$ccPassthru[] = $_POST['postalCode'];
	$ccPassthru[] = $_POST['cvc2'];
	$ccPassthru[] = str_pad(trim($_POST['expirationMonth']), 2, '0', STR_PAD_LEFT).'/'.str_pad(trim($_POST['expirationYear']), 2, '0', STR_PAD_LEFT);
	$ccPassthru[] = $_POST['issueNumber'];
	$ccPassthru[] = $_POST['cardNumber'];
	$ccPassthru[] = str_pad(trim($_POST['startMonth']), 2, '0', STR_PAD_LEFT).'/'.str_pad(trim($_POST['startYear']), 2, '0', STR_PAD_LEFT);
	$ccPassthru[] = $_POST['cardType'];
	$ccPassthru[] = $_POST['amount'];
	$ccPassthru[] = $_POST['customerId'];
	$ccPassthru[] = $_POST['cart_order_id'];
	?>
	<html>
	<head></head>
	<body onLoad="document.getElementById('ccVerifyCC').submit();">
	<div style="text-align: center; border: 1px solid darkred; font-family: arial;">
		<br />
		<img src="<?php echo $config['storeURL_SSL'];?>/modules/gateway/HSBC/admin/logo.gif" /><br /><br />
		Cardholder Authentication in Progress<br /><br />
		<img src="<?php echo $config['storeURL_SSL'];?>/modules/gateway/HSBC/ajax.gif" /><br /><br />
		<img src="<?php echo $config['storeURL_SSL'];?>/modules/gateway/HSBC/vbv.png" /><img src="<?php echo $config['storeURL_SSL'];?>/modules/gateway/HSBC/mcs.png" /><br /><br />
	</div>
	<form method="POST" action="https://<?php echo $module['pas'];?>" id="ccVerifyCC">
	<input type="hidden" name="CardExpiration" value="<?php echo $_POST['expirationYear'].$_POST['expirationMonth'];?>" />
	<input type="hidden" name="CardholderPan" value="<?php echo $_POST['cardNumber']; ?>" />
	<input type="hidden" name="CcpaClientId" value="<?php echo $module['alias']; ?>01" />
	<input type="hidden" name="CurrencyExponent" value="2" />
	<input type="hidden" name="PurchaseAmount" value="<?php echo $_POST['amount'];?>" />
	<input type="hidden" name="PurchaseAmountRaw" value="<?php echo preg_replace('#[^0-9]#', '', $_POST['amount']);?>" />
	<input type="hidden" name="PurchaseCurrency" value="826" />
	<input type="hidden" name="MD" value="<?php echo base64_encode(implode('|', $ccPassthru));?>" />
	<input type="hidden" name="ResultUrl" value="<?php echo $config['storeURL_SSL'];?>/index.php?_g=rm&type=gateway&cmd=process&module=HSBC&cart_order_id=<?php echo $orderSum['cart_order_id'] ?>" />
	</form>
	</body>
	</html>
<?php
	exit;

	}
}

$formTemplate = new XTemplate('modules/gateway/HSBC/form.tpl', '', null, 'main', true, true);

if(isset($errorMsg)) {

	$formTemplate->assign('LANG_ERROR',$errorMsg);
	$formTemplate->parse('form.error');

}

$formTemplate->assign('VAL_FIRST_NAME', $cc_session->ccUserData['firstName']);
$formTemplate->assign('VAL_LAST_NAME', $cc_session->ccUserData['lastName']);
$formTemplate->assign('VAL_EMAIL_ADDRESS', $cc_session->ccUserData['email']);
$formTemplate->assign('VAL_ADD_1', $cc_session->ccUserData['add_1']);
$formTemplate->assign('VAL_ADD_2', $cc_session->ccUserData['add_2']);
$formTemplate->assign('VAL_CITY', $cc_session->ccUserData['town']);
$formTemplate->assign('VAL_COUNTY', $cc_session->ccUserData['county']);
$formTemplate->assign('VAL_POST_CODE', $cc_session->ccUserData['postcode']);
$formTemplate->assign('VAL_PHONE', $cc_session->ccUserData['phone']);

if ($module['amex'] == 1) {
	$formTemplate->assign('AMEX_OPT', '<option value="8">American Express</option>');
	$formTemplate->assign('MAX_CVV2', '4');
} else {
	$formTemplate->assign('MAX_CVV2', '3');
}
$formTemplate->assign('VAL_CART_ORDER_ID', $orderSum['cart_order_id']);
$formTemplate->assign('VAL_GRAND_TOTAL', $orderSum['prod_total']);

$formTemplate->assign('VAL_MERCH_ID', $module['acNo']);

if ($module['avs']) {
	$formTemplate->assign('VAL_AVSMSG', $module['avstext']);

	$countries = $db->select('SELECT `id`, `iso`, `printable_name` FROM '.$glob['dbprefix'].'CubeCart_iso_countries WHERE `id` = '.$cc_session->ccUserData['country'].' LIMIT 1');
	foreach ($countries as $country) {
		$formTemplate->assign('VAL_COUNTRY_ISO', $country['iso']);
		$countryName = $country['printable_name'];
		if (strlen($countryName)>20) {
			$countryName = substr($countryName, 0, 20).'&hellip;';
		}
		$formTemplate->assign('VAL_COUNTRY_NAME', $countryName);
		$formTemplate->parse('form.avs.repeat_countries');
	}
	$formTemplate->parse('form.avs');

} else {

	$countries = $db->select('SELECT id, iso, printable_name FROM '.$glob['dbprefix'].'CubeCart_iso_countries ORDER BY printable_name');
	foreach ($countries as $country) {
		$formTemplate->assign('COUNTRY_SELECTED', ($country['id'] == $cc_session->ccUserData['country']) ? 'selected="selected"' : '');
		$formTemplate->assign('VAL_COUNTRY_ISO', $country['iso']);
		$countryName = $country['printable_name'];
		if (strlen($countryName)>20) {
			$countryName = substr($countryName, 0, 20).'&hellip;';
		}
		$formTemplate->assign('VAL_COUNTRY_NAME', $countryName);
		$formTemplate->parse('form.standard.repeat_countries');
	}
	$formTemplate->parse('form.standard');
}

$formTemplate->parse('form');
$formTemplate = $formTemplate->text('form');

?>