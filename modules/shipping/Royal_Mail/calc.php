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
|	Calculates the Royal Mail postage value based on weight
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
function Royal_Mail() {

	global $totalWeight,$basket;

	$moduleName = 'Royal_Mail';

	## Royal Mail shipping module
	$module = fetchDbConfig('Royal_Mail');
	$taxVal = taxRate($module['tax']);

	if ($module['status']) {

		## get the delivery ISO
		$countryISO = getCountryFormat($basket['delInf']['country'], 'id', 'iso');

		## build array of ISO Codes
		$zones['1'] = explode(',', str_replace(' ', '', strtoupper($module['zone1Countries'])));
		$zones['2'] = explode(',', str_replace(' ', '', strtoupper($module['zone2Countries'])));
		$zones['3'] = explode(',', str_replace(' ', '', strtoupper($module['zone3Countries'])));
		$zones['4'] = explode(',', str_replace(' ', '', strtoupper($module['zone4Countries'])));

		## find the country
		foreach ($zones as $key => $value) {
			foreach($zones[$key] as $no => $iso) {
				if ($iso == $countryISO) $shipZone = $key;
			}
		}

		## work out cost
		$shipBands	= explode(',', str_replace(' ', '', $module['zone'.$shipZone.'Rates']));
		$noBands	= count($shipBands);

		for ($j = 0, $maxj = count($shipBands); $j < $maxj; ++$j) {
			$weightCost = explode(':', str_replace(' ', '', $shipBands[$j]));
			if ($totalWeight <= $weightCost[0]) {
				$sum = $weightCost[1]+$module['zone'.$shipZone.'Handling'];
				break;
			}
		}

		$sum = $sum + $module['handling'];
		if ($taxVal>0) $shippingTax = ($taxVal / 100) * $sum;

		$out = array(0 => array());
		$out[0]['method']	= 'Royal Mail';
		$out[0]['value']	= $sum;
		$out[0]['desc']		= priceFormat($sum,true);
		$out[0]['taxId']	= $module['tax'];
		$out[0]['taxAmount']= $shippingTax;

		return ($out[0]['value'] > 0) ? $out : false;
	}
	return false;
}
$shipArray[] = Royal_Mail();
?>