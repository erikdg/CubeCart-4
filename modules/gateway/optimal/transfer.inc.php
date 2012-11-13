<?php

function repeatVars() {
	return false;
}

function merchantRefNum() {
	return time().rand(0,99999);
}
function getLanguage($country_iso) {
	return ($country_iso=='GB') ? 'GB' : 'US'; 
}
function previousCustomer($customer_id) {
	global $db,$glob;
	## Look for past completed orders
	return $db->select(sprintf('SELECT `cart_order_id` FROM `%sCubeCart_order_sum` WHERE `customer_id` = %s AND `status` = 3',$glob['dbprefix'],$customer_id)) ? 'true' : 'false';
}
function productType() {
	global $orderSum;
	
	$digital = false;
	$tangible = false;
	
	$basket = unserialize($orderSum['basket']);
	
	foreach ($basket['invArray'] as $key) {
		if($key['digital']=="1") {
			$digital = true;
		} else {
			$tangible = true;
		}
	}
	/*
	¥ P = Physical Goods 
	¥ D = Digital Goods/Subscription Registra-tion
	¥ C = Digital Content 
	¥ G = Gift Certificate/Digital Cash 
	¥ S = Shareware 
	¥ M = Digital & Physical 
	¥ R = Subscription Renewal
	*/
	if($digital && $tangible) {
		// Digital & Tangible Mix
		return "M";
	} elseif($digital && !$tangible) {
		// Digital Only
		return "C";
	} else {
		// Tangible
		return "P";
	}
	// true false
}
function formatState($country_id,$state) {
	global $db,$glob;
	if($country_id!=="US" && $country_id!=="CA") {
		return $state;
	} else {
		$state_abbrev = $db->select(sprintf('SELECT `abbrev` FROM `%sCubeCart_iso_counties` WHERE `name` = %s',$glob['dbprefix'],$state));
		if($state_abbrev) {
			return $state_abbrev[0]['abbrev'];
		} else {
			return $state;
		}
	}
}

function fixedVars($output_format = 'html') {
	global $module, $orderSum, $config, $glob, $db;

	$billingName 	= makeName($orderSum['name']);
	$country_iso 	= getCountryFormat($orderSum['country'],"id","iso");
	$merchantRefNum = merchantRefNum();
	
	$record['sec_order_id'] = $merchantRefNum;
	// We need to reference the order by the transaction id
	$db->update($glob['dbprefix']."CubeCart_order_sum", $record, "`cart_order_id`= '".$orderSum['cart_order_id']."'");
	
	if($country_iso == "US" || $country_iso == "CA") {
		$state_region = "state";
	} else {
		$state_region = "region";
	}
	if($module['API_method']=='tradegard') {
	$xml_request = '<?xml version="1.0" encoding="UTF-8"?>
<checkoutRequest xmlns="www.optimalpayments.com/checkout">
	<merchantRefNum>'.$merchantRefNum.'</merchantRefNum>
	<returnUrl page="'.$GLOBALS['storeURL'].'/index.php">
		<param>
			<key>_g</key>
			<value>rm</value>
		</param>
		<param>
			<key>type</key>
			<value>gateway</value>
		</param>
		<param>
			<key>cmd</key>
			<value>process</value>
		</param>
		<param>
			<key>module</key>
			<value>optimal</value>
		</param>
		<param>
			<key>cart_order_id</key>
			<value>'.$orderSum['cart_order_id'].'</value>
		</param> 
	</returnUrl> 
	<cancelUrl page="'.$GLOBALS['storeURL'].'/index.php">
		<param>
			<key>_g</key>
			<value>co</value>
		</param>
		<param>
			<key>_a</key>
			<value>step3</value>
		</param>
		<param>
			<key>contShop</key>
			<value>1</value>
		</param>
		<param>
			<key>cart_order_id</key>
			<value>'.$orderSum['cart_order_id'].'</value>
		</param>
	</cancelUrl>';
	//$xml_request .= '<accountNum>'.$module['accountNum'].'</accountNum> 
	$xml_request .= '<currencyCode>'.$config['defaultCurrency'].'</currencyCode>
	<shoppingCart>
		<description>Order #'.$orderSum['cart_order_id'].'</description>
		<quantity>1</quantity> 
		<amount>'.$orderSum['prod_total'].'</amount>
	</shoppingCart> 
	<totalAmount>'.$orderSum['prod_total'].'</totalAmount> 
	<locale>
		<language>en</language>
		<country>'.getLanguage($country_iso).'</country>
	</locale>
	<billingDetails>
		<firstName>'.$billingName[2].'</firstName>
		<lastName>'.$billingName[3].'</lastName>
		<street>'.$orderSum['add_1'].'</street>
		<street2>'.$orderSum['add_2'].'</street2>
		<city>'.$orderSum['town'].'</city>
		<'.$state_region.'>'.formatState($country_iso,$orderSum['county']).'</'.$state_region.'>
		<country>'.$country_iso.'</country>
		<zip>'.$orderSum['postcode'].'</zip>
		<phone>'.$orderSum['phone'].'</phone>
		<email>'.$orderSum['email'].'</email>
	</billingDetails> 
	<previousCustomer>'.previousCustomer($orderSum['customer_id']).'</previousCustomer>
	<productType>'.productType().'</productType>
</checkoutRequest>';
	
	} else {
		
		$xml_request = '<?xml version="1.0" encoding="UTF-8"?>
<profileCheckoutRequest xmlns="www.optimalpayments.com/checkout">
	<merchantRefNum>'.$merchantRefNum.'</merchantRefNum>
	<returnUrl page="'.$GLOBALS['storeURL'].'/index.php">
		<param>
			<key>_g</key>
			<value>rm</value>
		</param>
		<param>
			<key>type</key>
			<value>gateway</value>
		</param>
		<param>
			<key>cmd</key>
			<value>process</value>
		</param>
		<param>
			<key>module</key>
			<value>optimal</value>
		</param>
		<param>
			<key>cart_order_id</key>
			<value>'.$orderSum['cart_order_id'].'</value>
		</param> 
	</returnUrl>
	<cancelUrl page="'.$GLOBALS['storeURL'].'/index.php">
		<param>
			<key>_g</key>
			<value>co</value>
		</param>
		<param>
			<key>_a</key>
			<value>step3</value>
		</param>
		<param>
			<key>contShop</key>
			<value>1</value>
		</param>
		<param>
			<key>cart_order_id</key>
			<value>'.$orderSum['cart_order_id'].'</value>
		</param>
	</cancelUrl>
		<paymentMethod>CC</paymentMethod>
		<currencyCode>'.$config['defaultCurrency'].'</currencyCode> 
		<shoppingCart>
			<description>Order #'.$orderSum['cart_order_id'].'</description>
			<quantity>1</quantity>
			<amount>'.$orderSum['prod_total'].'</amount>
		</shoppingCart> 
	<totalAmount>'.$orderSum['prod_total'].'</totalAmount>
	<customerProfile>
		<merchantCustomerId>'.$orderSum['email'].'</merchantCustomerId>';
//$xml_request .= '				<customerTokenId>12312211</customerTokenId>';
//$xml_request .= '		<isNewCustomer>'.previousCustomer($orderSum['customer_id']).'</isNewCustomer>';
$xml_request .= '		<isNewCustomer>false</isNewCustomer>';
$xml_request .= '	</customerProfile> 
	<locale>
		<language>en</language>
		<country>'.getLanguage($country_iso).'</country>
	</locale>
</profileCheckoutRequest>';

	}
	$encodedMessage = base64_encode($xml_request);
	$signature 		= base64_encode(hash_hmac("sha1", $xml_request, $module['sharedKey'], true));
	if($output_format == 'html') {
		$vars_out = '<input type="hidden" name="shopId" value="'.$module['shopId'].'" />
	<input type="hidden" name="encodedMessage" value="'.$encodedMessage.'" />
	<input type="hidden" name="signature" value="'.$signature.'" />';
	} else {
		$vars_out = 'shopId='.urlencode($module['shopId']).'&encodedMessage='.urlencode($encodedMessage).'&signature='.urlencode($signature);
	}
	return $vars_out;
}

///////////////////////////
// Other Vars
////////
$formAction = ($module['test_mode']) ? 'https://checkout.test.tradegard.com/securePayment/tradegard/checkoutRequest.htm' : 'https://checkout.tradegard.com/securePayment/tradegard/checkoutRequest.htm';
$formMethod = 'post';
$formTarget = '_self';
$transfer	= ($module['API_method']=='tradegard') ? 'auto' : 'manual';
$stateUpdate = true;

?>