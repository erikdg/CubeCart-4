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
|	calc.php
|   ========================================
|	Sets shipping to be free
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
// free shipping
function Free_Shipping() {
	global $lang, $subTotal;

	$moduleName	= 'Free_Shipping';
	$module		= fetchDbConfig('Free_Shipping');

	if ($module['status']) {
		if (is_numeric($module['trigger']) && $module['trigger'] > 0) {
			if ($subTotal < $module['trigger']) return false;
		}
		$out = array(0 => array());
		$out[0]['value']  = 0;
		$out[0]['desc']	  = priceFormat(0,true).' ('.$lang['front']['misc_free'].')';
		$out[0]['method'] = $lang['front']['misc_freeShipping'];
		$out[0]['folder'] = $moduleName;
		return $out;
	}
	return false;
}

$shipArray[] = Free_Shipping();
?>