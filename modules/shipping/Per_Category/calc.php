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
|	Calculates the cost per category based on predefined line costs
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
// per category shipping module
function Per_Category()
{

	global $lang,$perShipPrice,$lineShip,$shipZone;

	$moduleName = 'Per_Category';

	$module = fetchDbConfig('Per_Category');

	$taxVal = taxRate($module['tax']);

	if($module['status'] && $shipZone) {

		$sum =  $perShipPrice + $lineShip;


		if($module['handling']>0)
		{
			$sum = $module['handling']+$sum;
		}

		if($taxVal>0)
		{

			$shippingTax = ($taxVal / 100) * $sum;

		}

		if($shipZone == 'n')
		{

			$worldZone = $lang['front']['misc_national'];

		}
		elseif($shipZone == 'i')
		{

			$worldZone = $lang['front']['misc_international'];

		}

		$out = array(0 => array());
		$out[0]['value'] = $sum;
		$out[0]['desc'] = priceFormat($sum,true);
		$out[0]['method'] = $lang['front']['misc_byCategory']." (".$worldZone.")";
		$out[0]['taxId'] = $module['tax'];
		$out[0]['taxAmount'] = $shippingTax;

		return $out;

	}
	else
	{
		return FALSE;
	}
}
$shipArray[] = Per_Category();
?>