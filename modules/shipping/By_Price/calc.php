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
|   Date: Friday, 15 April 2005
|   Email: info@cubecart.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	calc.php
|   ========================================
|	Calculates free shipping based on order amount
+--------------------------------------------------------------------------
*/
// By Price
if (!defined('CC_INI_SET')) die("Access Denied");
function By_Price() {
	global $lang, $subTotal;

	$moduleName = 'By_Price';
	$module = fetchDbConfig($moduleName);
	$taxVal = taxRate($module['tax']);

	if ($module['status']) {

		$out = array(0 => array());
		if ($subTotal >= $module['level']) {
			$out[0]['value'] = 0;
			$out[0]['desc'] = priceFormat(0,true);
			$out[0]['method'] = $lang['front']['misc_freeForOrdOver'].' '.priceFormat($module['level'],TRUE);
			$out[0]['taxId'] = $module['tax'];
			$out[0]['taxAmount'] = 0;
		} else {

			$sum = $module['amount'];

			if ($module['handling']>0) $sum = $sum + $module['handling'];
			if ($taxVal>0) $shippingTax = ($taxVal / 100) * $sum;

			$out[0]['value'] = $sum;
			$out[0]['desc'] = priceFormat($sum, true);
			$out[0]['method'] = $lang['front']['misc_freeForOrdOver'].' '.priceFormat($module['level'],TRUE);
			$out[0]['taxId'] = $module['tax'];
			$out[0]['taxAmount'] = $shippingTax;
		}
		return $out;
	}
	return false;
}
$shipArray[] = By_Price();
?>