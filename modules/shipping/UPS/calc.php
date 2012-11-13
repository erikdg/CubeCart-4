<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	calc.inc.php
|   ========================================
|	Calculate UPS Quotes
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
function UPS()
{
	global $totalWeight,$basket,$config,$lang;

	$moduleName = 'UPS';

	$module = fetchDbConfig($moduleName);

	$taxVal = taxRate($module['tax']);

	if ($module['status']) {
		require 'ups.php';
		$i=0;
		foreach ($module as $key => $value) {
			if (preg_match('#product#', $key) && $value) {
				$productCode = substr($key,7,3);

				$rate = new Ups;
				$rate->upsProduct($productCode);					# See upsProduct() function for codes
				$rate->origin($module['postcode'], getCountryFormat($config['siteCountry'],'id','iso'));				# Use ISO country codes!

				$dest_country	= getCountryFormat($basket['delInf']['country'],'id','iso');
				$dest_postcode	= ($dest_country == 'US') ? substr($basket['delInf']['postcode'], 0, 5) : $basket['delInf']['postcode'];
				$rate->dest($dest_postcode, $dest_country);	# Use ISO country codes!

				if (!isset($module['rate'])) $module['rate']='CC';

				$rate->rate(strtoupper($module['rate']));			# See the rate() function for codes
				$rate->container(strtoupper($module['container']));	# See the container() function for codes
				$rate->weight($totalWeight);

				if (!isset($module['rescom'])) $module['rescom']='RES';
				$rate->rescom(strtoupper($module['rescom']));		# See the rescom() function for codes

				switch ($productCode) {
					case '1DM':
						$desc = $lang['front']['misc_nextDayEarlyAm'];
						break;
					case '1DA':
						$desc = $lang['front']['misc_nextDayAir'];
						break;
					case '1DP':
						$desc = $lang['front']['misc_nextDayAirSaver'];
						break;
					case '2DM':
						$desc = $lang['front']['misc_2ndDayEarlyAm'];
						break;
					case '2DA':
						$desc = $lang['front']['misc_2ndDayAir'];
						break;
					case '3DS':
						$desc = $lang['front']['misc_3daySelect'];
						break;
					case 'GND':
						$desc = $lang['front']['misc_ground'];
						break;
					case 'STD':
						$desc = $lang['front']['misc_canadaStandard'];
						break;
					case 'XPR':
						$desc = $lang['front']['misc_worldwideExpress'];
						break;
					case 'XDM':
						$desc = $lang['front']['misc_worldwideExpressPlus'];
						break;
					case 'XPD':
						$desc = $lang['front']['misc_worldwideExpedited'];
						break;
				}

				$quote = $rate->getQuote();

				if($quote>0)
				{
					$quote = $module['handling'] + $quote;

					if($taxVal>0)
					{

						$shippingTax = ($taxVal / 100) * $quote;

					}

					$out[$i]['method'] = 'UPS '.$desc;
					$out[$i]['desc'] = priceFormat($quote,true).' (UPS '.$desc.')';
					$out[$i]['value'] = $quote;
					$out[$i]['taxId'] = $module['tax'];
					$out[$i]['taxAmount'] = $shippingTax;

					$i++;
				}

			}

		}

	}

	return $out;
}
$shipArray[] = UPS();
?>