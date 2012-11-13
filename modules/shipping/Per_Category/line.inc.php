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
|	line.php
|   ========================================
|	Calculates Category Line Shipping Cost
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// per category shipping module
$module = fetchDbConfig('Per_Category');

// get the delivery ISO
$countryISO = getCountryFormat($basket['delInf']['country'],'id','iso');

// build array of ISO Codes
$zones['n'] = explode(',',str_replace(' ','',strtoupper($module['national'])));
$zones['i'] = explode(',',str_replace(' ','',strtoupper($module['international'])));

// find the country
foreach ($zones as $key => $value) {
	foreach ($zones[$key] as $no => $iso) {
		if ($iso == $countryISO) {
			$shipZone = $key;
		}
	}
}

if($shipZone == 'n')
{

	$lineShip = ($product[0]['item_ship'] * $quantity) + $lineShip;

	if(!isset($perShipPrice) OR $perShipPrice<$product[0]['per_ship'])
	{
		$perShipPrice = $product[0]['per_ship'];
	}

}
elseif($shipZone == 'i')
{

	$lineShip = ($product[0]['item_int_ship'] * $quantity) + $lineShip;

	if(!isset($perShipPrice) OR $perShipPrice<$product[0]['per_int_ship'])
	{
		$perShipPrice = $product[0]['per_int_ship'];
	}

}
?>