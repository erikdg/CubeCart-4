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
|	Calculates shipping based on a flat rate
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
// flat rate
function Flat_Rate()
{
	global $lang;

	$moduleName = 'Flat_Rate';

	$module = fetchDbConfig($moduleName);

	$taxVal = taxRate($module['tax']);

	if($module['status']==1)
	{

		$sum = $module['cost'];

		if($module['handling']>0)
		{
			$sum = $sum + $module['handling'];
		}

		if($taxVal>0)
		{

			$shippingTax = ($taxVal / 100) * $sum;

		}

		$out = array(0 => array());
		$out[0]['value'] = $sum;
		$out[0]['desc'] = priceFormat($sum,true).' ('.$lang['front']['misc_flatRate'].')';
		$out[0]['method'] = $lang['front']['misc_flatRate'];
		$out[0]['taxId'] = $module['tax'];
		$out[0]['taxAmount'] = $shippingTax;

		return $out;
	}
	else
	{
		return false;
	}
}
$shipArray[] = Flat_Rate();
?>