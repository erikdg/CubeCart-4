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
|	Calculates shipping by weight (Lbs or Kgs)
+--------------------------------------------------------------------------
*/
// per category shipping module
if (!defined('CC_INI_SET')) die("Access Denied");
## Shipping -- By Weight
function By_Weight() {
	global $lang, $subTotal, $totalWeight, $basket;

	$moduleName = 'By_Weight';
	$module		= fetchDbConfig($moduleName);
	$taxVal		= taxRate($module['tax']);

	if ($module['status']) {
		## Get the delivery ISO
		$countryISO = getCountryFormat($basket['delInf']['country'], 'id', 'iso');

		## Build array of ISO Codes
		$zones['1'] = explode(',', str_replace(' ', '', strtoupper($module['zone1Countries'])));
		$zones['2'] = explode(',', str_replace(' ', '', strtoupper($module['zone2Countries'])));
		$zones['3'] = explode(',', str_replace(' ', '', strtoupper($module['zone3Countries'])));
		$zones['4'] = explode(',', str_replace(' ', '', strtoupper($module['zone4Countries'])));

		## Find the country
		foreach ($zones as $key => $value) {
			foreach ($value as $no => $iso) {
				if ($iso == $countryISO) {
					$shipZone = $key;
					break;
				}
			}
		}

		## Work out cost (First Class)
		if (!empty($module['zone'.$shipZone.'RatesClass1'])) {
			$shipBands	= explode(',', str_replace(' ', '', $module['zone'.$shipZone.'RatesClass1']));
			$noBands	= count($shipBands);

			natsort($shipBands);

			if ($noBands>0) {
				for ($n = 0, $maxn = count($shipBands); $n < $maxn; ++$n) {
					$weightCost = explode(':', str_replace(' ', '', $shipBands[$n]));

					if ($totalWeight <= $weightCost[0]) {
						$sumClass1 = $weightCost[1] + $module['zone'.$shipZone.'Handling'];
						break;
					}
				}

				if ($sumClass1>0) {
					$sumClass1 = $sumClass1 + $module['handling'];
					if ($taxVal>0) {
						$shippingTax = ($taxVal / 100) * $sumClass1;
					}
					$out[0]['value'] = $sumClass1;
					$out[0]['desc'] = priceFormat($sumClass1, true).' '.$lang['front']['misc_1stClass'];
					$out[0]['method'] = $lang['front']['misc_byWeight1stClass'];
					$out[0]['taxId'] = $module['tax'];
					$out[0]['taxAmount'] = $shippingTax;
				}
			}
		}

		## Work out cost (Second Class)
		if (!empty($module['zone'.$shipZone.'RatesClass2'])) {
			$shipBands = explode(',', str_replace(' ', '', $module['zone'.$shipZone.'RatesClass2']));
			$noBands = count($shipBands);

			if ($noBands>0) {
				for ($n = 0, $maxn = count($shipBands); $n < $maxn; ++$n) {
					$weightCost = explode(':', str_replace(' ', '', $shipBands[$n]));
					if ($totalWeight <= $weightCost[0]) {
						$sumClass2 = $weightCost[1] + $module['zone'.$shipZone.'Handling'];
						break;
					}
				}
				if ($sumClass2>0) {
					$sumClass2 = $sumClass2 + $module['handling'];

					if ($taxVal>0) {
						$shippingTax = ($taxVal / 100) * $sumClass2;
					}
					$out[1]['value'] = $sumClass2;
					$out[1]['desc'] = priceFormat($sumClass2,true)." ".$lang['front']['misc_2ndClass'];
					$out[1]['method'] = $lang['front']['misc_byWeight2ndClass'];
					$out[1]['taxId'] = $module['tax'];
					$out[1]['taxAmount'] = $shippingTax;
				}

			}
		}
		return (isset($out)) ? $out : false;
	}
	return false;
}

$shipArray[] = By_Weight();
?>