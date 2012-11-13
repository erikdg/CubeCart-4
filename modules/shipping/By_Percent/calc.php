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
|	Calculates shipping based on Percentage of Subtotal
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
// By Percent
function By_Precent() {
	global $subTotal;

	$moduleName = 'By_Percent';
	$module = fetchDbConfig($moduleName);
	$taxVal = taxRate($module['tax']);

	if ($module['status']) {

		$sum = sprintf('%.2f', $subTotal * (($module['percent'])/100));

		if ($module['handling']>0) $sum = $sum + $module['handling'];
		if ($taxVal>0) $shippingTax = ($taxVal / 100) * $sum;

		$out = array(0 => array());
		$out[0]['value'] = $sum;
		$out[0]['desc'] = priceFormat($sum, true);
		$out[0]['method'] = $moduleName;
		$out[0]['taxId'] = $module['tax'];
		$out[0]['taxAmount'] = $shippingTax;
		return $out;
	}
	return false;
}
$shipArray[] = By_Precent();
?>