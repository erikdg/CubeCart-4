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
|	transfer.inc.php
|   ========================================
|	Core functions for the PayPoint Gateway	
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die('Access Denied');

function repeatVars() {
	return false;
}

function fixedVars() {
	global $module, $orderSum, $config;

	$trans_id	= 'CC4'.(rand(0,32000)*rand(0,32000));
	$callback	= $GLOBALS['storeURL'].'/index.php?_g=rm&type=gateway&cmd=process&module=PayPoint&cart_order_id='.$orderSum['cart_order_id'];

	$hiddenVars = array(
		'<input type="hidden" name="trans_id" value="'.$trans_id.'" />',
		'<input type="hidden" name="amount" value="'.$orderSum['prod_total'].'" />',
		'<input type="hidden" name="callback" value="'.$callback.'" />',
		'<input type="hidden" name="merchant" value="'.$module['merchant'].'" />',
		'<input type="hidden" name="options" value="test_status='.$module['testmode'].',currency='.$config['default_currency'].',cart=cubecart" />',
		'<input type="hidden" name="bill_name" value="'.$orderSum['name'].'" />',
		'<input type="hidden" name="bill_company" value="'.$orderSum['companyName'].'" />',
		'<input type="hidden" name="bill_addr_1" value="'.$orderSum['add_1'].'" />',
		'<input type="hidden" name="bill_addr_2" value="'.$orderSum['add_2'].'" />',
		'<input type="hidden" name="bill_city" value="'.$orderSum['town'].'" />',
		'<input type="hidden" name="bill_state" value="'.$orderSum['state'].'" />',
		'<input type="hidden" name="bill_country" value="'.getCountryFormat($orderSum['country'],"id","iso").'" />',
		'<input type="hidden" name="bill_post_code" value="'.$orderSum['postcode'].'" />',
		'<input type="hidden" name="bill_tel" value="'.$orderSum['phone'].'" />',
		'<input type="hidden" name="bill_email" value="'.$orderSum['email'].'" />',
		'<input type="hidden" name="ship_name" value="'.$orderSum['name_d'].'" />',
		'<input type="hidden" name="ship_company" value="'.$orderSum['companyName_d'].'" />',
		'<input type="hidden" name="ship_addr_1" value="'.$orderSum['add_1_d'].'" />',
		'<input type="hidden" name="ship_addr_2" value="'.$orderSum['add_2_d'].'" />',
		'<input type="hidden" name="ship_city" value="'.$orderSum['town_d'].'" />',
		'<input type="hidden" name="ship_state" value="'.$orderSum['state_d'].'" />',
		'<input type="hidden" name="ship_country" value="'.getCountryFormat($orderSum['country_d'],"id","iso").'" />',
		'<input type="hidden" name="ship_post_code" value="'.$orderSum['postcode_d'].'" />',
		'<input type="hidden" name="ship_tel" value="'.$orderSum['phone_d'].'" />',
		'<input type="hidden" name="ship_email" value="'.$orderSum['email_d'].'" />',
	);
	if (!empty($module['remote_password'])) {
		$digest = md5($trans_id.$orderSum['prod_total'].$module['remote_password']);
		$hiddenVars[] = '<input type="hidden" name="digest" value="'.$digest.'" />';
	}
	return implode("\n", $hiddenVars);
}

///////////////////////////
// Other Vars
////////	
$formAction = 'https://www.secpay.com/java-bin/ValCard';
$formMethod = 'post';
$formTarget = '_self';
$transfer	= 'auto';
?>
