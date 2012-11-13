<?php

function repeatVars() {
	return false;
}

function fixedVars() {
	global $module, $orderSum, $config;
	
	$params	= array(
		'clientid'	=> $module['clientid'],
		'oid'		=> $orderSum['cart_order_id'],
		'password'	=> $module['passphrase'],
		'total'		=> $orderSum['prod_total'],
		'chargetype'	=> $module['chargetype'],
		'currencycode'	=> '826',
	);
	
	$server	= ((bool)$module['test_mode']) ? 'secure2.mde.epdq.co.uk' : 'secure2.epdq.co.uk';
	
	$ch		= curl_init();
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
	curl_setopt($ch, CURLOPT_URL, 'https://'.$server.'/cgi-bin/CcxBarclaysEpdqEncTool.e');
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$result	= curl_exec($ch);
	if ((bool)curl_errno($ch) == false) {
		curl_close($ch);
		# Parse their dirty html response
		if (preg_match('#value="([A-Z0-9]+)"#iu', $result, $match)) {
			$hidden	= array(
				'server'				=> $server,
				'epdqdata'				=> $match[1],
				'returnurl'				=> $GLOBALS['storeURL'].'/modules/gateway/ePDQ/jump.php',
				'merchantdisplayname'	=> $GLOBALS['config']['storeName'],
				
				'email'			=> $orderSum['email'],
				## Billing data
			#	'bfullname'		=> $orderSum['name'],
				'baddr1'		=> $orderSum['add_1'],
				'baddr2'		=> $orderSum['add_2'],
				'bcity'			=> $orderSum['town'],
				'bpostalcode'	=> $orderSum['postcode'],
				'bcountry'		=> getCountryFormat($orderSum['country'], (is_numeric($orderSum['country'])) ? 'id' : 'printable_name', 'numcode'),
				'btelephonenumber'	=> $orderSum['phone'],
				## Shipping Data
			#	'sfullname'		=> $orderSum['name_d'],
				'saddr1'		=> $orderSum['add_1_d'],
				'saddr2'		=> $orderSum['add_2_d'],
				'scity'			=> $orderSum['town_d'],
				'spostalcode'	=> $orderSum['postcode_d'],
				'scountry'		=> getCountryFormat($orderSum['country_d'], (is_numeric($orderSum['country_d'])) ? 'id' : 'printable_name', 'numcode'),
			);
			if ($hidden['bcountry'] == 'US') {
				$hidden['bstate']	= $orderSum['county'];
			} else {
				$hidden['bcountyprovince'] = $orderSum['county'];
			}
			if ($hidden['scountry'] == 'US') {
				$hidden['sstate']	= $orderSum['county_d'];
			} else {
				$hidden['scountyprovince'] = $orderSum['county_d'];
			}
			ksort($hidden);
			$_SESSION['epdq']	= $hidden;
		}
	}
	return false;
}

$formAction = $GLOBALS['storeURL'].'/modules/gateway/ePDQ/jump.php';
$formMethod = 'post';
$formTarget = '_self';
