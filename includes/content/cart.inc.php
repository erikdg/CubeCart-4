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
|	cart.inc.php
|   ========================================
|	Core Checkout & Cart Pages
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')) die("Access Denied");

session_start(); // required for PayPal Pro

/* BEGIN Browser back button fix - Thanks Convict */

if (isset($_SESSION['cc_back']) && $_SESSION['cc_back']) { // session variable set in gateway.inc.php around line 290

	$sess_cart_order_id = base64_decode($_SESSION['cc_back']);
	unset($_SESSION['cc_back']);

	require_once('classes'.CC_DS.'cart'.CC_DS.'order.php');
	$order	= new order();
	$sess_sum = $order->getOrderSum($sess_cart_order_id);
	if ($sess_sum['status']!=2 && $sess_sum['status']!=3) {
		$pastBasket = $db->select(sprintf("SELECT `basket` FROM `%sCubeCart_order_sum` WHERE `cart_order_id` = %s",$glob['dbprefix'],$db->MySQLSafe($sess_cart_order_id)));
		if ($pastBasket) {
			$record['basket'] = "'".$pastBasket[0]['basket']."'";
			$db->update($glob['dbprefix']."CubeCart_sessions", $record, "`sessId`= '".$cc_session->ccUserData['sessId']."'");
		}
		$order->orderStatus(6, $sess_cart_order_id, false, true);
		httpredir(currentPage());
	}
}
/* END Browser back button fix - Thanks Convict */

// include lang file

$lang1 = getLang('includes'.CC_DS.'content'.CC_DS.'reg.inc.php');
$lang2 = getLang('includes'.CC_DS.'content'.CC_DS.'cart.inc.php');

$lang = array_merge($lang1, $lang2);

require_once('classes'.CC_DS.'cart'.CC_DS.'shoppingCart.php');
$cart = new cart();

// Dangerous var fixed $view_cart->assign('VAL_BACK_TO', $_GET['_a']);
$allowed_a = array('cart','step1','step2');
$current_a = (in_array($_GET['_a'],$allowed_a)) ? $_GET['_a'] : 'cart';

// check the user is logged on
if($_GET['_a']=='step2' && empty($cc_session->ccUserData['customer_id'])) {
	httpredir('index.php?_g=co&_a=step1');
}
// if user is logged in an act = cart jump ahead to step2
else if($_GET['_a']=='cart' && $cc_session->ccUserData['customer_id']>0) {
#	$basket = $cart->cartContents($cc_session->ccUserData['basket']);
#	if (!empty($basket)) {
		httpredir('index.php?_g=co&_a=step2');
#	}
}

$basket = $cart->cartContents($cc_session->ccUserData['basket']);

// Flexible Taxes, by Estelle Winterflood
$config_tax_mod = fetchDbConfig('Multiple_Tax_Mod');
$refresh = false;

if(isset($_GET['remlast'])) {
	$cart->unsetVar('invArray');
	$cart->removeLastItem();
	$refresh = true;
}
if (isset($_GET['remCode'])) {
	$cart->removeCoupon($_GET['remCode']);
	// lose the post vars
	$refresh = true;

}

if (isset($_POST['coupon']) && !empty($_POST['coupon']) && !isset($basket['code'])){

	$cart->addCoupon($_POST['coupon']);
	// lose post vars
	$refresh = true;

}
if(isset($_POST['shipKey']) && (int)$_POST['shipKey']>0) {

	$cart->setVar((int)$_POST['shipKey'],'shipKey');
	// lose post vars
	$refresh = true;

}
if(isset($_POST['delInf'])) {

	// start: Flexible Taxes, by Estelle Winterflood
	if (isset($_POST['which_field'])){
		$delivery = $_POST['delInf'];
		if ($_POST['which_field']=='T'){
			unset($delivery['county_sel']);
		} elseif ($_POST['which_field']=='S') {
			$delivery['county'] = $delivery['county_sel'];
			unset($delivery['county_sel']);
		}
		$_POST['delInf'] = $delivery;
	}

	$cart->setVar($_POST['delInf'],'delInf');
	$refresh = true;

}

if(isset($_GET['remove'])) {
	$cart->unsetVar('invArray');
	$cart->remove($_GET['remove']);
	$refresh = true;
}

if (isset($_POST['quan'])) {
	$cart->unsetVar('invArray');
	foreach ($_POST['quan'] as $key => $value) {
		$cart->update($key, $value);
	}
	$refresh = true;
}
if(isset($_GET['mode']) && $_GET['mode'] == 'emptyCart') {
	## Empty the cart
	$cart->emptyCart();
	$refresh = true;
}

if(isset($_POST['productCode']) && !empty($_POST['productCode'])) {
	$cart->addByCode($_POST['productCode']);

	$refresh = true;
}

if ($refresh) {
	$excludeGetVars = array('editDel' => 1, 'remCode' => 1, 'mode' => 1, 'remove' => 1, 'remlast' => 1);
	httpredir(currentpage($excludeGetVars));
}

$view_cart = new XTemplate ('content'.CC_DS.'cart.tpl');
$view_cart->assign('LANG_CART',$lang['cart']['cart']);
$view_cart->assign('LANG_CHECKOUT', $lang['cart']['checkout']);
$view_cart->assign('LANG_PAYMENT', $lang['cart']['payment']);
$view_cart->assign('LANG_COMPLETE', $lang['cart']['complete']);
$view_cart->assign('LANG_ADD_PRODCODE',$lang['cart']['add_more']);
$view_cart->assign('LANG_ADD', $lang['cart']['add']);
$view_cart->assign('LANG_QTY',$lang['cart']['qty']);
$view_cart->assign('LANG_PRODUCT',$lang['cart']['product']);
$view_cart->assign('LANG_CODE',$lang['cart']['code']);
$view_cart->assign('LANG_STOCK',$lang['cart']['stock']);
$view_cart->assign('LANG_PRICE',$lang['cart']['price']);
$view_cart->assign('LANG_LINE_PRICE',$lang['cart']['line_price']);
$view_cart->assign('LANG_DELETE',$lang['cart']['delete']);
$view_cart->assign('LANG_REMOVE_ITEM',$lang['cart']['remove']);

if ($_GET['_a'] == 'cart') {

	$view_cart->assign('CONT_VAL','index.php?_g=co&amp;_a=step1');
	$view_cart->assign('LANG_CHECKOUT_BTN',$lang['cart']['checkout_btn']);
	$view_cart->assign('LANG_VIEW_CART',$lang['cart']['view_cart']);
	$view_cart->assign('CLASS_CART','class="txtcartProgressCurrent"');
	$view_cart->assign('CLASS_STEP2','');

} else if ($_GET['_a'] == 'step2' && !empty($basket['conts'])) {

#	if (empty($basket)) httpredir('?_g=co&_a=cart');

	// Place Order Link
	$view_cart->assign('CONT_VAL', 'index.php?_g=co&amp;_a=step3');
	$view_cart->assign('CONT_VAL', (isset($_GET['editDel']) && $_GET['editDel'] == 1) ? 'javascript:submitDoc(\'cart\');' : 'index.php?_g=co&amp;_a=step3');

	$view_cart->assign('CLASS_CART','');
	$view_cart->assign('CLASS_STEP2','class="txtcartProgressCurrent"');
	$view_cart->assign('LANG_VIEW_CART',$lang['cart']['place_order_title']);

	$view_cart->assign('LANG_INVOICE_ADDRESS',$lang['cart']['invoice_address']);
	$view_cart->assign('LANG_DELIVERY_ADDRESS',$lang['cart']['delivery_address']);

	$view_cart->assign('TXT_TITLE',$lang['cart']['title']);
	$view_cart->assign('LANG_TITLE_DESC',$lang['reg']['title_desc']);

	$view_cart->assign('TXT_FIRST_NAME',$lang['cart']['first_name']);
	$view_cart->assign('TXT_LAST_NAME',$lang['cart']['last_name']);
	$view_cart->assign('TXT_COMPANY_NAME',$lang['cart']['company_name']);
	$view_cart->assign('TXT_ADD_1',$lang['cart']['address2']);
	$view_cart->assign('TXT_ADD_2','');
	$view_cart->assign('TXT_TOWN',$lang['cart']['town']);
	$view_cart->assign('TXT_COUNTY',$lang['cart']['county']);
	$view_cart->assign('TXT_POSTCODE',$lang['cart']['postcode']);
	$view_cart->assign('TXT_COUNTRY',$lang['cart']['country']);

	// PayPal EC make payment button
	if (isset($_SESSION['token']) && isset($_SESSION['payer_id'])) {
		$lang = getLang('includes'.CC_DS.'content'.CC_DS.'gateway.inc.php');
		$view_cart->assign('LANG_CHECKOUT_BTN',$lang['gateway']['continue']);
	} else {
		$view_cart->assign('LANG_CHECKOUT_BTN',$lang['cart']['place_order']);
	}

	// stick in delivery details
	if (!isset($basket['delInf']) || $config['shipAddressLock']) {
		$iniDeliv['title']			= stripslashes(html_entity_decode($cc_session->ccUserData['title'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['firstName']		= stripslashes(html_entity_decode($cc_session->ccUserData['firstName'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['lastName']		= stripslashes(html_entity_decode($cc_session->ccUserData['lastName'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['companyName']	= stripslashes(html_entity_decode($cc_session->ccUserData['companyName'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['add_1']			= stripslashes(html_entity_decode($cc_session->ccUserData['add_1'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['add_2']			= stripslashes(html_entity_decode($cc_session->ccUserData['add_2'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['town']			= stripslashes(html_entity_decode($cc_session->ccUserData['town'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['county']			= stripslashes(html_entity_decode($cc_session->ccUserData['county'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['postcode']		= stripslashes(html_entity_decode($cc_session->ccUserData['postcode'],ENT_NOQUOTES,"UTF-8"));
		$iniDeliv['country']		= stripslashes(html_entity_decode($cc_session->ccUserData['country'],ENT_NOQUOTES,"UTF-8"));
		$basket = $cart->setVar($iniDeliv, 'delInf');
	}

	// stick in delivery details
	$view_cart->assign('VAL_DEL_TITLE', stripslashes(html_entity_decode($basket['delInf']['title'])));
	$view_cart->assign('VAL_DEL_FIRST_NAME',stripslashes(html_entity_decode($basket['delInf']['firstName'])));
	$view_cart->assign('VAL_DEL_LAST_NAME',stripslashes(html_entity_decode($basket['delInf']['lastName'])));
	$view_cart->assign('VAL_DEL_COMPANY_NAME',stripslashes(html_entity_decode($basket['delInf']['companyName'])));
	$view_cart->assign('VAL_DEL_ADD_1',stripslashes(html_entity_decode($basket['delInf']['add_1'])));
	$view_cart->assign('VAL_DEL_ADD_2',stripslashes(html_entity_decode($basket['delInf']['add_2'])));
	$view_cart->assign('VAL_DEL_TOWN',stripslashes(html_entity_decode($basket['delInf']['town'])));
	$view_cart->assign('VAL_DEL_COUNTY',stripslashes(html_entity_decode($basket['delInf']['county'])));
	$view_cart->assign('VAL_DEL_POSTCODE',stripslashes(html_entity_decode($basket['delInf']['postcode'])));
	$view_cart->assign('VAL_DEL_COUNTRY',getCountryFormat($basket['delInf']['country'],'id','printable_name'));

	// stick in invoice details
	$view_cart->assign('VAL_TITLE',stripslashes($cc_session->ccUserData['title']));
	$view_cart->assign('VAL_FIRST_NAME',stripslashes($cc_session->ccUserData['firstName']));
	$view_cart->assign('VAL_LAST_NAME',stripslashes($cc_session->ccUserData['lastName']));
	$view_cart->assign('VAL_COMPANY_NAME',stripslashes($cc_session->ccUserData['companyName']));
	$view_cart->assign('VAL_ADD_1',stripslashes($cc_session->ccUserData['add_1']));
	$view_cart->assign('VAL_ADD_2',stripslashes($cc_session->ccUserData['add_2']));
	$view_cart->assign('VAL_TOWN',stripslashes($cc_session->ccUserData['town']));
	$view_cart->assign('VAL_COUNTY',stripslashes($cc_session->ccUserData['county']));
	$view_cart->assign('VAL_POSTCODE',stripslashes($cc_session->ccUserData['postcode']));
	$view_cart->assign('VAL_COUNTRY',getCountryFormat($cc_session->ccUserData['country'],'id','printable_name'));

	$view_cart->assign('LANG_CHANGE_INV_ADD',$lang['cart']['edit_invoice_address']);
	$view_cart->assign('VAL_BACK_TO', $current_a);

	// start: Flexible Taxes, by Estelle Winterflood
	// counties selector

	if (isset($_GET['editDel']) && $_GET['editDel'] && !$config['shipAddressLock']) {
		$jsScript = jsGeoLocationExtended('country', 'county_sel', $lang['cart']['na'], 'divCountySelect', 'divCountyText', 'county', 'which_field');
		$counties = $db->select("SELECT `name` FROM  ".$glob['dbprefix']."CubeCart_iso_counties WHERE countryId = '".$basket['delInf']['country']."';");

		if (is_array($counties)){
			$view_cart->assign('VAL_COUNTY_SEL_STYLE', 'style="display:block;"');
			$view_cart->assign('VAL_COUNTY_TXT_STYLE', 'style="display:none;"');
			$view_cart->assign('VAL_COUNTY_WHICH_FIELD', 'S');
		} else {
			$view_cart->assign('VAL_COUNTY_SEL_STYLE', 'style="display:none;"');
			$view_cart->assign('VAL_COUNTY_TXT_STYLE', 'style="display:block;"');
			$view_cart->assign('VAL_COUNTY_WHICH_FIELD', 'T');
		}
		$view_cart->assign('JS_COUNTY_OPTIONS', '<script type="text/javascript">'.$jsScript.'</script>');

		for ($i = 0, $maxi = count($counties); $i < $maxi; ++$i) {
			if ($counties[$i]['name'] == $basket['delInf']['county']){
				$view_cart->assign('COUNTY_SELECTED', 'selected="selected"');
			} else {
				$view_cart->assign('COUNTY_SELECTED', '');
			}

			$countyName = $counties[$i]['name'];
			if (strlen($countyName)>20) $countyName = substr($countyName ,0, 20).'&hellip;';

			$view_cart->assign('VAL_DEL_COUNTY_NAME', $countyName);
			$view_cart->parse('view_cart.cart_true.edit_delivery.county_opts');
		}

		// end: Flexible Taxes
		$cache		= new cache('glob.countries');
		$countries	= $cache->readCache();

		if (!$cache->cacheStatus) {
			$countries = $db->select('SELECT `id`, `printable_name` FROM '.$glob['dbprefix'].'CubeCart_iso_countries ORDER BY `printable_name`');
			$cache->writeCache($countries);
		}

		for ($i = 0, $maxi = count($countries); $i < $maxi; ++$i) {
			if ($countries[$i]['id'] == $basket['delInf']['country']) {
				$view_cart->assign('COUNTRY_SELECTED', 'selected="selected"');
			} else {
				$view_cart->assign('COUNTRY_SELECTED', '');
			}
			$view_cart->assign('VAL_DEL_COUNTRY_ID',$countries[$i]['id']);
			$countryName = $countries[$i]['printable_name'];

			if (strlen($countryName)>20) {
				$countryName = substr($countryName,0,20).'&hellip;';
			}

			$view_cart->assign('VAL_DEL_COUNTRY_NAME', $countryName);
			$view_cart->parse('view_cart.cart_true.edit_delivery.country_opts');
		}
		$view_cart->parse('view_cart.cart_true.edit_delivery');
	} else {
		if (!$config['shipAddressLock']) {
			$view_cart->assign('LANG_CHANGE_DEL_ADD', $lang['cart']['edit_delivery_address']);
			$view_cart->parse('view_cart.cart_true.fixed_delivery.edit_btn');
		}
		$view_cart->parse('view_cart.cart_true.fixed_delivery');
	}
}

## See if there are contents in the basket array
if ($basket['conts']) {

	$tax = 0;
	$taxCustomer = 0;
	$taxZone = array();
	$shippingTaxAmount = 0;

	// $config['priceTaxDelInv'] 0 = devivery address
	// $config['priceTaxDelInv'] 1 = invoice address
	//$countyDel = $db->select("SELECT `id` FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE `abbrev` = ".$db->MySQLSafe($basket['delInf']['county'])." AND `countryId` = ".$db->MySQLSafe($basket['delInf']['country']));

	/* START OLD CODE TO WORK OUR TAX ON CUSTOMER INFO
	$countyDel = $db->select("SELECT `id` FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE (`abbrev` = ".$db->MySQLSafe($basket['delInf']['county'])." OR `name` = ".$db->MySQLSafe($basket['delInf']['county']).") AND `countryId` = ".$db->MySQLSafe($basket['delInf']['country']));

	$countyInv = $db->select("SELECT `id` FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE `name` = ".$db->mySQLSafe($cc_session->ccUserData['county'])." AND `countryId` = ".$db->MySQLSafe($cc_session->ccUserData['country']));
	END OLD CODE TO WORK OUR TAX ON CUSTOMER INFO */

	/* START IMPROVED FUNCTIONALITY TO CALC TAX ON STORE LOCALE IF CUSTOMER IS UNKNOWN */
	$configCounty = $db->select('SELECT `name`, `abbrev` FROM '.$glob['dbprefix'].'CubeCart_iso_counties WHERE `id` = '.$db->mySQLSafe($config['siteCounty']));

	$countyInvName = !empty($cc_session->ccUserData['county']) ? $cc_session->ccUserData['county'] : $configCounty[0]['name'];
	$countryInvId 	= !empty($cc_session->ccUserData['country']) ? $cc_session->ccUserData['country'] : $config['siteCountry'];

	$query = 'SELECT `id` FROM '.$glob['dbprefix'].'CubeCart_iso_counties WHERE `name` = '.$db->mySQLSafe($countyInvName).' AND `countryId` = '.$db->MySQLSafe($countryInvId);
	$countyInv = $db->select($query);

	$countyDelName = !empty($basket['delInf']['county']) ? $basket['delInf']['county'] : $configCounty[0]['name'];
	$countyDelAbbr = !empty($basket['delInf']['county']) ? $basket['delInf']['county'] : $configCounty[0]['abbrev'];
	$countryDelId  = !empty($basket['delInf']['country']) ? $basket['delInf']['country'] : $config['siteCountry'];

	$query = 'SELECT `id` FROM '.$glob['dbprefix'].'CubeCart_iso_counties WHERE (`abbrev` = '.$db->MySQLSafe($countyDelAbbr).' OR `name` = '.$db->MySQLSafe($countyDelName).') AND `countryId` = '.$db->MySQLSafe($countryDelId);
	$countyDel = $db->select($query);
	/* END IMPROVED FUNCTIONALITY TO CALC TAX ON STORE LOCALE IF CUSTOMER IS UNKNOWN */

	if(!$config['priceIncTax']) { // Only tax customers if tax is enabled

		if($config['priceTaxDelInv']==0 || $config['shipAddressLock'] == 1) {
			// calculate tax on delivery address
			$taxZone['countyId'] = $countyDel[0]['id'];
			$taxZone['countryId'] = !empty($basket['delInf']['country']) ? $basket['delInf']['country'] : $countryDelId;

		} elseif($config['priceTaxDelInv']==1) {
			// calculate tax on invoice address
			$taxZone['countyId'] = $countyInv[0]['id'];
			$taxZone['countryId'] = !empty($cc_session->ccUserData['country']) ? $cc_session->ccUserData['country'] : $countryInvId;
		}

		if ($taxZone['countryId']==$config['taxCountry']) {
			if ($config['taxCounty']==0) {
				// tax customer
				$taxCustomer = 1;
			} else if ($taxZone['countyId']==$config['taxCounty']) {
				// tax customer
				$taxCustomer = 1;
			}
		}
	}

	// start: Flexible Taxes, by Estelle Winterflood
	if (!$config['priceIncTax'] && $config_tax_mod['status']) {
		// get specific entries for this state, and also entries for this whole country (ie. county_id=0)
		$query = "SELECT d.name AS name, taxName AS type_name, type_id, country_id, county_id, abbrev, tax_percent, goods, shipping, display FROM ".$glob['dbprefix']."CubeCart_taxes AS t, ".$glob['dbprefix']."CubeCart_tax_rates AS r LEFT JOIN ".$glob['dbprefix']."CubeCart_tax_details AS d ON r.details_id=d.id LEFT JOIN ".$glob['dbprefix']."CubeCart_iso_counties AS c ON c.id=county_id WHERE r.type_id=t.id AND d.status='1' AND r.active='1' AND country_id='".$taxZone['countryId']."' AND (county_id='0' OR (county_id = '".$taxZone['countyId']."'))";

		// "Testing" mode info display
		if ($config_tax_mod['debug']) {
			$tax_debug[] =  '<div style=\'border: 1px solid grey; background: white; text: black; margin-bottom: 1em; padding: 0.5em 1em; font-family: verdana; font-size: 11px;\'>';
			$tax_debug[] =  '<p>This information is being printed because the taxes is in &quot;Testing Configuration&quot; mode.<br/>Please use this information to ensure you have your taxes configured correctly, then set the Flexible Taxes mod to &quot;Live Store&quot; mode.</p>';
			$tax_debug[] =  '<p><strong>Customer Delivery Location:</strong><br/>';
			if (empty($basket['delInf']['county'])) {
				$state_id = 'n/a';
			} else {
				$state_id = $taxZone['countyId'];
			}
			$tax_debug[] =  'Country id ['.$taxZone['countryId'].'] State/County/Zone ID ['.$state_id.']</p>';
		}

		$taxes_config = $db->select($query);

		$taxes = array();

		// is there any possibility of taxing this delivery address?
		if (is_array($taxes_config)){
			if ($config_tax_mod['debug'])
				$tax_debug[] =  '<p><strong>Taxes which affect this customer:</strong><br/>';

			for ($i = 0, $maxi = count($taxes_config); $i < $maxi; ++$i){
				$tax_config = $taxes_config[$i];

				// 'Testing' mode info display
				if ($config_tax_mod['debug']) {
					if (empty($tax_config['abbrev'])) {
						$state_abbrev = '--all--';
					} else {
						$state_abbrev = $tax_config['abbrev'];
					}
					$tax_debug[] =  'Tax ['.$tax_config['name'].'] Class ['.$tax_config['type_name'].'] Country ID ['.$tax_config['country_id'].'] State/County/Zone ['.$state_abbrev.'] Rate ['.number_format($tax_config['tax_percent'],2).' %]<br/>';
				}

				// Prepare array to hold tax name/display/amount
				// The tax_config array may contain multiple occurances
				// of the same tax if there are multiple tax classes
				$setup = false;
				for ($j = 0, $maxj = count($taxes); $j< $maxj; ++$j){
					if ($taxes[$j]['name'] == $tax_config['name'])
						$setup = true;
				}

				if (!$setup){
					$idx = count($taxes);
					$taxes[$idx]['name'] = $tax_config['name'];
					$taxes[$idx]['display'] = $tax_config['display'].':';
					$taxes[$idx]['amount'] = 0;
				}
			}

			$taxCustomer = 1;

			if ($config_tax_mod['debug'])
				$tax_debug[] =  '</p>';
		}
		// 'Testing' mode info display
		elseif ($config_tax_mod['debug']) {
			$tax_debug[] =  '<p><strong>No taxes active/enabled for this location.</strong></p>';
		}
	}

	// end: Flexible Taxes

	$totalWeight	= '';
	$totalDiscount	= '';
	$i				= 0;
	$subTotal		= 0;
	$shipCost		= 0;
	$grandTotal		= 0;
	$discount		= 0;

	##########################
	## START PRODUCTS LOOP  ##
	##########################

	## Start discounts
	if (isset($basket['discount_percent']) && $basket['discount_percent']>0) {
		$discount_percent	= $basket['discount_percent'];

	} else if (isset($basket['discount_price']) && $basket['discount_price']>0) {
		$discount_remainder = $basket['discount_price'];
	}

	foreach($basket['conts'] as $key => $value){

		++$i;
		$linePrice		= 0;	// line price for basket
		$optionsCost	= 0;	// product options cost
		$plainOpts		= '';	// options as plain text


		// fetch shipping by category module
		$module = fetchDbConfig('Per_Category');
		$shipByCat = $module['status'];

		$extraJoin = '';

		if ($shipByCat==1) {
			$extraJoin = 'INNER JOIN '.$glob['dbprefix'].'CubeCart_category AS C ON I.cat_id = C.cat_id';
		}

		## Check for custom basket data e.g Gift Certificates
		if (isset($basket['conts'][$key]['custom']) && $basket['conts'][$key]['custom'] == 1) {

			$gc = fetchDbConfig('gift_certs');

			$product[0]['productId']	= 0;
			$product[0]['productCode']	= $gc['productCode'];
			$product[0]['quantity']		= $basket['conts'][$key]['quantity'];
			$product[0]['price']		= $basket['conts'][$key]['gcInfo']['amount'];
			$product[0]['name']			= $lang['cart']['gift_cert'];
			$product[0]['image']		= $GLOBALS['rootRel'].'images/general/giftcert.gif';
			$product[0]['cat_id']		= 0;
			$product[0]['sale_price']	= 0;
			$product[0]['stock_level']	= 0;
			$product[0]['useStockLevel']= 0;
			$product[0]['digital']		= (strtolower($basket['conts'][$key]['gcInfo']['delivery']) == 'm') ? false : true;
			$product[0]['prodWeight']	= ($gc['delivery']=='e') ? 0 : $gc['weight'];
			$product[0]['taxType']		= $gc['taxType'];
			$product[0]['tax_inclusive']= ($gc['tax']) ? true : false;
			$product[0]['giftCert']		= true;

			// START PSEUDO PRODUCT OPTS
			$view_cart->assign('VAL_OPT_NAME',$lang['cart']['gift_cert_recip_name']);
			$view_cart->assign('VAL_OPT_VALUE',$basket['conts'][$key]['gcInfo']['recipName']);

			$plainOpts .= $lang['cart']['gift_cert_recip_name']." - ".$basket['conts'][$key]['gcInfo']['recipName']."\r\n";
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.options');

			$view_cart->assign('VAL_OPT_NAME',$lang['cart']['gift_cert_recip_email']);
			$view_cart->assign('VAL_OPT_VALUE',$basket['conts'][$key]['gcInfo']['recipEmail']);

			$plainOpts .= $lang['cart']['gift_cert_recip_email'].' - '.$basket['conts'][$key]['gcInfo']['recipEmail']."\r\n";
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.options');

			$view_cart->assign('VAL_OPT_NAME',$lang['cart']['gift_cert_recip_message']);

			$gcMessage = $basket['conts'][$key]['gcInfo']['message'];

			if (strlen($gcMessage) > 30) $gcMessage = substr($gcMessage, 0, 30).'&hellip;';

			$view_cart->assign('VAL_OPT_VALUE', $gcMessage);

			$plainOpts .= $lang['cart']['gift_cert_recip_message'].' - '.$basket['conts'][$key]['gcInfo']['message']."\r\n";
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.options');

			$view_cart->assign('VAL_OPT_NAME',$lang['cart']['gift_cert_delivery']);
			$view_cart->assign('VAL_OPT_VALUE',$lang['cart']['delivery_method_'.$basket['conts'][$key]['gcInfo']['delivery']]);

			$plainOpts .= $lang['cart']['gift_cert_delivery'].' - '.$lang['cart']['delivery_method_'.$basket['conts'][$key]['gcInfo']['delivery']]."\r\n";
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.options');

			// END PSEUDO PRODUCT OPTS
		} else {

			$productId	= $cart->getProductId($key);
			$product	= $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory AS I INNER JOIN '.$glob['dbprefix'].'CubeCart_taxes AS T ON T.id = taxType '.$extraJoin.' WHERE I.productId='.$db->mySQLSafe($productId));

			// FIX FOR DELETED TAX BANDS PRE 3.0.5
			if (!$product) {
				$product = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE productId='.$db->mySQLSafe($productId));
				$product[0]['percent'] = 0;
			}
			if (($val = prodAltLang($product[0]['productId']))) {
				$product[0]['name'] = $val['name'];
			}
		}

		$view_cart->assign('TD_CART_CLASS',cellColor($i, 'tdcartEven', 'tdcartOdd'));
		$view_cart->assign('VAL_PRODUCT_ID',$productId);
		$view_cart->assign('VAL_CURRENT_STEP',$current_a);
		$view_cart->assign('VAL_PRODUCT_KEY', md5($key));

		if (isset($product[0]['giftCert']) && $product[0]['giftCert']) {
			$view_cart->assign('VAL_IMG_SRC', imgPath($product[0]['image'], false, ''));
		} else {
			if (file_exists(imgPath($product[0]['image'], true, 'root')) && !empty($product[0]['image'])) {
				$view_cart->assign('VAL_IMG_SRC', imgPath($product[0]['image'], true, 'rel'));
			} else {
				$view_cart->assign('VAL_IMG_SRC', 'skins/'. SKIN_FOLDER . '/styleImages/thumb_nophoto.gif');
			}
		}

		## Only calculate shipping IF the product is tangible
		if (!$product[0]['digital']) {
			$orderTangible = true;
		}

		$view_cart->assign('VAL_PRODUCT_NAME', validHTML($product[0]['name']));
		$view_cart->assign('VAL_PRODUCT_CODE', $product[0]['productCode']);

		## Build the product options
		$optionKeys = $cart->getOptions($key);
		if (!empty($optionKeys)) {
			$options = explode('{|}', $optionKeys);
			foreach ($options as $value) {
				## Split on separator
				$value_data		= explode('{@}', $value);
				## Get Option Data
				unset($option_name, $option_value);
				$option_top		= $db->select(sprintf("SELECT T.* FROM %1\$sCubeCart_options_top AS T WHERE T.option_id = %2\$s", $glob['dbprefix'], $value_data[0]));
				if ($option_top) {
					$option_name	= $option_top[0]['option_name'];
					if ($option_top[0]['option_type'] == 0 && is_numeric($value_data[1])) {
						$option		= $db->select(sprintf("SELECT M.*, B.* FROM %1\$sCubeCart_options_mid AS M, %1\$sCubeCart_options_bot AS B WHERE M.value_id = B.value_id AND B.assign_id = %2\$d", $glob['dbprefix'], $value_data[1]));
						if ($option) {
							$option_price	= $option[0]['option_price'];
							$option_symbol	= $option[0]['option_symbol'];
							$option_value	= $option[0]['value_name'];
						}
					} else {
						$option			= $db->select(sprintf("SELECT B.* FROM %1\$sCubeCart_options_bot AS B WHERE B.option_id = %2\$d AND B.product = %3\$d LIMIT 1", $glob['dbprefix'], $value_data[0], $productId));
						if ($option) {
							$option_price	= $option[0]['option_price'];
							$option_symbol	= $option[0]['option_symbol'];
							$option_value	= $value_data[1];
						}
					}
				}
				if (isset($option_value) && isset($option_name)) {
					## Assign values
					$view_cart->assign('VAL_OPT_NAME', validHTML($option_name));
					$view_cart->assign('VAL_OPT_VALUE', htmlentities(strip_tags($option_value), ENT_QUOTES, 'UTF-8'));

					$plainOpts .= $option_name.' - '.$option_value."\r\n";

					if ($option_price > 0) {
						if ($option_symbol == '+') {
							$optionsCost = $optionsCost + $option_price;
						} else if ($option_symbol == '-') {
							$optionsCost = $optionsCost - $option_price;
						} else if ($option_symbol == '~') {
							$optionsCost = 0;
						}
					}
					$view_cart->parse('view_cart.cart_true.repeat_cart_contents.options');
				}
			}
		}

		if ($product[0]['useStockLevel'] && $config['stockLevel']){
			$view_cart->assign('VAL_INSTOCK', $product[0]['stock_level']);
		} else {
			$view_cart->assign('VAL_INSTOCK', '&infin;');
		}

		if (($config['outofstockPurchase']) && ($product[0]['stock_level']<$cart->cartArray['conts'][$key]['quantity']) && ($product[0]['useStockLevel'])) {
			$view_cart->assign('VAL_STOCK_WARN',$lang['cart']['stock_warn']);

			$quantity = $cart->cartArray['conts'][$key]['quantity'];
			$view_cart->parse('view_cart.repeat_cart_contents.stock_warn');

		} else if ((!$config['outofstockPurchase']) && ($product[0]['stock_level']<$cart->cartArray['conts'][$key]['quantity']) && ($product[0]['useStockLevel'])) {

			$view_cart->assign('VAL_STOCK_WARN',$lang['cart']['amount_capped'].' '.$product[0]['stock_level'].'.');
			$quantity = $product[0]['stock_level'];
			$basket = $cart->update(md5($key), $quantity);
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.stock_warn');
		} else {
			$quantity = $cart->cartArray['conts'][$key]['quantity'];
		}
		$view_cart->assign('VAL_QUANTITY', $quantity);

		if ((isset($basket['conts'][$key]['custom']) && $basket['conts'][$key]['custom']==1) || !salePrice($product[0]['price'], $product[0]['sale_price'])) {
			$price = $product[0]['price'];
		} else {
			$price = salePrice($product[0]['price'], $product[0]['sale_price']);
		}

		$price = ($price+$optionsCost < 0) ? 0 : $price+($optionsCost);

		if (!isset($basket['conts'][$key]['custom']) || !$basket['conts'][$key]['custom']) {
			$altCheckoutInv[$i]['taxType']	= $product[0]['taxType'];
			$altCheckoutInv[$i]['name']		= $product[0]['name'];
			$altCheckoutInv[$i]['options']	= $plainOpts;
			$altCheckoutInv[$i]['quantity'] = $quantity;

			if($product[0]['tax_inclusive'] == true) {
				$altCheckoutInv[$i]['price'] = sprintf('%.2f',$price / (($product[0]['percent'] / 100) +1));
				$altCheckoutInv[$i]['priceIncTax']	= sprintf('%.2f',$price);
			} else {
				$altCheckoutInv[$i]['price']	= sprintf('%.2f',$price);
			}

			## Private data
			$altCheckoutInv[$i]['private_data']['digital']		= $product[0]['digital'];
			$altCheckoutInv[$i]['private_data']['productcode']	= $product[0]['productCode'];
			$altCheckoutInv[$i]['private_data']['productid']	= $productId;
		} else {
			## Alternative checkout can only be used for tangible goods right now
			$customWarn = true;
		}

		$linePrice = $price * $quantity;

		// set live vars for order inv and its the last step
		$basket = $cart->setVar($productId,'productId','invArray',$i);
		$basket = $cart->setVar($product[0]['name'],'name','invArray',$i);
		$basket = $cart->setVar($product[0]['productCode'],'productCode','invArray',$i);
		$basket = $cart->setVar(htmlentities(stripslashes($plainOpts),ENT_QUOTES,'UTF-8'), 'prodOptions', 'invArray', $i);
		$basket = $cart->setVar(sprintf('%.2f',$linePrice),'price','invArray',$i);
		$basket = $cart->setVar($quantity,'quantity','invArray',$i);
		$basket = $cart->setVar($product[0]['digital'],'digital','invArray',$i);

		if (isset($basket['conts'][$key]['custom']) && $basket['conts'][$key]['custom']) {
			$basket = $cart->setVar(serialize($basket['conts'][$key]['gcInfo']),'custom','invArray',$i);
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.quanDisabled');
		} else {
			$view_cart->parse('view_cart.cart_true.repeat_cart_contents.quanEnabled');
		}

		$view_cart->assign('VAL_IND_PRICE', priceFormat($price, true));
		$view_cart->assign('VAL_LINE_PRICE', priceFormat($linePrice, true));

		if ($shipByCat) {
			## Calculate the line category shipping price
			require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'shipping'.CC_DS.'Per_Category'.CC_DS.'line.inc.php';
		}

		## Apply discounts
		$itemDiscount = 0;
		if (isset($discount_percent) && $discount_percent > 0 && !$product[0]['giftCert']) {
			## Percentile discounts
			$itemDiscount	= $linePrice*($discount_percent/100);
			$totalDiscount	+= $linePrice*($discount_percent/100);

		} else if (isset($discount_remainder) && $discount_remainder > 0 && !$product[0]['giftCert']) {
			## Fixed value discounts
			if ($discount_remainder <= $linePrice) {
				$discount			= $discount_remainder;
				$discount_remainder	= 0;
			} else if ($discount_remainder > $linePrice) {
				$discount			= $linePrice;
				$discount_remainder = $discount_remainder - $discount;
			}

			if ($discount > 0) {
				$totalDiscount	+= $discount;
				$itemDiscount	= $discount;
				$discount		= 0;
			}
		}
		$linePrice -= $itemDiscount;

		## Calculate weight
		//if ($product[0]['prodWeight']>0 && !$product[0]['digital']) {
		if ($product[0]['prodWeight']>0) {
			$totalWeight = ($product[0]['prodWeight'] * $quantity) + $totalWeight;
		}

		## Calculate tax
		if ($taxCustomer) {
			// start: Flexible Taxes, by Estelle Winterflood
			// calculate Tax on Goods
			if ($config_tax_mod['status']) {
				if (is_array($taxes_config)) {
					for ($j = 0, $maxj = count($taxes_config);  $j< $maxj; ++$j) {
						$tax_config = $taxes_config[$j];

						if ($tax_config['type_id'] == $product[0]['taxType']) {
							// tax on goods
							if ($tax_config['goods']) {

								$lineTax = getTax($linePrice, ($product[0]['tax_inclusive']) ? false : true, $tax_config['tax_percent']);

								for ($k = 0, $maxk = count($taxes); $k < $maxk; ++$k){
									if ($taxes[$k]['name'] == $tax_config['name']){
										// "Testing" mode info display
										if ($config_tax_mod['debug']) {
											if (!isset($debug)) $debug = '';
											$debug .= "Item ".$i." [".$product[0]['name']."] [".$product[0]['taxName']."] [".$quantity." x ".priceFormat($price, true)."] --- Tax [".$taxes[$k]['name']."] [".$tax_config['type_name']."] [".number_format($tax_config['tax_percent'],2)." %] --- ".priceFormat($lineTax,true)."<br/>";
										}
										$taxes[$k]['amount'] += $lineTax;
									}
								}
							}
						}
					}
				}
				## end: Flexible Taxes
			} else if (!$config['priceIncTax']) {
				$tax += getTax($linePrice, ($product[0]['tax_inclusive']) ? false : true, $product[0]['percent']);
			}
		}
		$subTotal += $linePrice;
		$view_cart->parse("view_cart.cart_true.repeat_cart_contents");
	}

	## Deduct the total discount from the subtotal - Fixes bug report #705
	//$subTotal -= $totalDiscount;

	## Work out discount on price if any - OLD CODE, left for reference
	/*
	if ($basket['discount_percent']>0) {
		if ($basket['discount_percent'] > 100) $basket['discount_percent'] = 100;
		$totalDiscount	= ($basket['discount_percent']/100) * $subTotal;
		$subTotal		= $subTotal - $totalDiscount;

	} else if ($basket['discount_price']>0) {
		if (isset($discountRemainder) && $discountRemainder>0) {
			$lineDiscount = $discountRemainder;
		} else if (!isset($discountRemainder)) {
			$lineDiscount = $basket['discount_price'];
		}
		if ($lineDiscount<$linePrice) {
			$discount = $lineDiscount;
		} else {
			$discount = $linePrice;
			$discountRemainder = $lineDiscount - $discount;
		}

		$totalDiscount	= $discount;
		$subTotal -= $totalDiscount;

	} else {
		$discount = 0;
	}
	*/

	// start: Flexible Taxes, by Estelle Winterflood
	if ($config_tax_mod['status'] && is_array($taxes_config) && $config_tax_mod['debug']){
			$tax_debug[] =  "<p><strong>Tax applied to goods:</strong><br/>".$debug."</p>";
	}
	// end: Flexible Taxes

	// calculate shipping when we have reached step2
	if ($_GET['_a'] == 'step2' && $orderTangible) {

		$shippingModules = $db->select("SELECT DISTINCT `folder` FROM ".$glob['dbprefix']."CubeCart_Modules WHERE `module`='shipping' AND `status` = 1");

		$noItems 	= $cart->noItems();
		$sum		= 0;

		if (is_array($shippingModules) && !empty($shippingModules)) {
			// if selected key has not been set, set it
			if (!isset($basket['shipKey']) || empty($basket['shipKey'])) $basket = $cart->setVar(1, 'shipKey');

			foreach ($shippingModules as $shippingModule) {
				$shippingCalcPath = 'modules'.CC_DS.'shipping'.CC_DS.$shippingModule['folder'].CC_DS.'calc.php';
				if (file_exists($shippingCalcPath)) {
					include($shippingCalcPath);
				}
			}

			#:convict:# Shipping cost from lowest to highest >>
			$Shipping = array();
			if(is_array($shipArray)) {
				foreach ($shipArray as $shipMethod) {
					if (!empty($shipMethod) && is_array($shipMethod)) {
						foreach ($shipMethod as $shipDetails) {
							$Shipping[] = $shipDetails;
						}
					}
				}
			}
			function cmp($a, $b) { $b=floatval($b['value']); $a=floatval($a['value']); return $a<$b ? -1 : ($a>$b ? 1 : 0); }
			usort($Shipping, 'cmp');
			$shipKey = 0;
			foreach ($Shipping as $shipDetails) {
				++$shipKey;
				if ($shipKey == $basket['shipKey']) {
					$selected = ' selected="selected"';

					$basket = $cart->setVar($shipDetails['method'], 'shipMethod');
					$basket = $cart->setVar(sprintf("%.2f", $shipDetails['value']), 'shipCost');

					$shippingTaxId		= (isset($shipDetails['taxId'])) ? $shipDetails['taxId'] : '';
					$shippingTaxAmount	= (isset($shipDetails['taxAmount'])) ? $shipDetails['taxAmount'] : '';
				} else {
					$selected = '';
				}
				$shippingPrice[] = '<option value="'.$shipKey.'"'.$selected.'>'.$shipDetails['desc'].'</option>';
			}

			if (is_array($shippingPrice)) {
				$shippingPrice = '<select class="textbox" style="width: 150px" id="shipping-select" name="shipKey" onchange="submitDoc(\'cart\');">'.implode("\n", $shippingPrice).'</select>';
			}

			## if no shipping method is available go to error page
			if (!$shipKey) httpredir('index.php?_g=co&_a=error&code=10001');

			## if shipping key is greater than those available set to 1, and redirect
			if ($basket['shipKey']>$shipKey) {
				$cart->setVar(1, 'shipKey');
				$basket = $cart->setVar(0.00, 'shipCost');
				httpredir('index.php?_g=co&_a=step2') or die('help');
			}

		} else {
			$shippingPrice .= priceFormat(0, true);
			$basket = $cart->setVar($lang['cart']['free_shipping'], 'shipMethod');
			$basket = $cart->setVar(0.00, 'shipCost');
		}
	} else {
		$shippingPrice = $lang['cart']['na'];
		## set shipping cost to 0.00 just incase
 		$basket = $cart->setVar(0.00, 'shipCost');
	}



	## If the voucher is a gift certificate, we'll let them use it to discount shipping
    if ((isset($basket['code_is_purchased']) && $basket['code_is_purchased']) && $totalDiscount < $basket['discount_price']) {
        $shippingCost = (is_numeric($shippingPrice)) ? $shippingPrice : $basket['shipCost'];

        if ($discount_remainder <= $basket['shipCost']) {
            $shippingCost	-= $discount_remainder;
            $totalDiscount	= $basket['discount_price'];
            $basket	 = $cart->setVar(sprintf('%.2f', $shippingCost), 'shipCost');
             #:convict:# Fix tax
            $taxVal = taxRate($shippingTaxId);
            $shippingTaxAmount = ($taxVal / 100) * $shippingCost;

        } else if ($discount_remainder > $basket['shipCost']) {
            $totalDiscount += $basket['shipCost'];
            $basket	 = $cart->setVar(0.00, 'shipCost');
            $shippingTaxAmount = 0; #:convict:# Fix tax
        }
    }

	// start: Flexible Taxes, by Estelle Winterflood
	// calculate tax on shipping
	if ($taxCustomer && $config_tax_mod['status']) {

		if ($config_tax_mod['debug'] && is_array($taxes_config))
			$tax_debug[] =  "<p><strong>Tax applied to shipping &amp; handling:</strong><br/>";

		if (is_array($taxes_config)) {
			for ($i = 0, $maxi = count($taxes_config); $i< $maxi; ++$i){
				$tax_config = $taxes_config[$i];
				if ($tax_config['type_id'] == $shippingTaxId){
					// tax on shipping

					if ($tax_config['shipping']) {
						$lineTax = getTax($basket['shipCost'], true, $tax_config['tax_percent']);
						for ($j = 0, $maxj = count($taxes); $j < $maxj; ++$j){
							if ($taxes[$j]['name'] == $tax_config['name']){
								// "Testing" mode info display
								if ($config_tax_mod['debug']) {
									$tax_debug[] =  "Method [".$folder."] [".$tax_config['type_name']."] [".priceFormat($basket['shipCost'], true)."] --- Tax [".$taxes[$j]['name']."] [".$tax_config['type_name']."] [".number_format($tax_config['tax_percent'],2)." %] --- ".priceFormat($lineTax,true)."<br />";
								}
								$taxes[$j]['amount'] += $lineTax;
							}
						}
					}
				}
			}
		}
		if ($config_tax_mod['debug'] && is_array($taxes_config))

		$tax_debug[] =  '</p>';
	} elseif($taxCustomer) { ## claculate basic tax on shipping
		$tax += $shippingTaxAmount;
	}
	if ($config_tax_mod['status'] && $config_tax_mod['debug']){
		## 'Testing' mode info display
			$tax_debug[] = '</div>';
	}
	## end: Flexible Taxes

	$view_cart->assign('LANG_SHIPPING', $lang['cart']['shipping']);

	$view_cart->assign('VAL_SHIPPING', $shippingPrice);

	// start: Flexible Taxes, by Estelle Winterflood
	// display taxes
	if ($config_tax_mod['status']){

		// Unset remnant taxes from prempted values
		$basket = $cart->unsetVar('tax1_disp');
		$basket = $cart->unsetVar('tax2_disp');
		$basket = $cart->unsetVar('tax3_disp');
		$basket = $cart->unsetVar('tax1_amt');
		$basket = $cart->unsetVar('tax2_amt');
		$basket = $cart->unsetVar('tax3_amt');

		if (count($taxes)==0){
			$view_cart->assign('LANG_TAX',$lang['cart']['tax']);
			$view_cart->assign('VAL_TAX',priceFormat(0,true));
		} else {
			// any additional taxes
			for ($i = 1, $maxi = count($taxes); $i < $maxi; ++$i) {
				$view_cart->assign('LANG_TAX',$taxes[$i]['display']);
				$view_cart->assign('VAL_TAX',priceFormat($taxes[$i]['amount'],true));
				$view_cart->parse('view_cart.cart_true.repeat_more_taxes');
			}

			// first tax
			$view_cart->assign('LANG_TAX',$taxes[0]['display']);
			$view_cart->assign('VAL_TAX',priceFormat($taxes[0]['amount'],true));
		}

		// tax registration number(s)
		$reg_number = $db->select('SELECT reg_number FROM '.$glob['dbprefix'].'CubeCart_tax_details;');
		$reg_string = '';
		if (is_array($reg_number)) {
			for ($i = 0, $maxi = count($reg_number); $i < $maxi; ++$i) {
				if (strlen($reg_string) && $reg_number[$i]['reg_number']!='')
				{
					$reg_string .= '<br/>';
				}
				$reg_string .= $reg_number[$i]['reg_number'];
			}
		}
		$view_cart->assign('VAL_TAX_REG',$reg_string);
		// end: Flexible Taxes
	} else {
		$view_cart->assign('LANG_TAX',$lang['cart']['tax']);
		$view_cart->assign('VAL_TAX',priceFormat($tax,true));
	}

	## Work out if the Gift Certificate has any discount left
	if ((isset($basket['code_is_purchased']) && $basket['code_is_purchased']) && $totalDiscount <= $basket['discount_price']) {
		$couponRemainder = $basket['discount_price'] - $totalDiscount;
		$basket	= $cart->setVar(sprintf('%.2f', $couponRemainder),'code_remainder');

		## Update coupon so that it can be used again with remainder, if there is any
		if ($couponRemainder > 0) {
			$giftRecord['discount_price'] = $db->mySQLSafe(sprintf('%.2f', $couponRemainder));
		} else {
			$giftRecord = array(
				'discount_price'	=> 0,
				'allowed_uses'		=> 0,
				'count'				=> 1,
			);
		}
		$basket	= $cart->setVar(sprintf('%.2f',$couponRemainder),'code_remainder');

		$where	= 'code = '.$db->mySQLSafe($basket['code']);
		$update	= $db->update($glob['dbprefix'].'CubeCart_Coupons', $giftRecord, $where);
	}

	$basket = $cart->setVar($totalDiscount, 'discount');
	$view_cart->assign('LANG_DISCOUNT',$lang['cart']['discount']);
	// set discount to return value if null
	if(empty($basket['discount']) || !isset($basket['discount'])) {
		$basket['discount'] = 0;
	}
	$view_cart->assign('VAL_DISCOUNT', priceFormat($basket['discount'], true));

	$view_cart->assign('LANG_SUBTOTAL', $lang['cart']['subtotal']);
	$view_cart->assign('VAL_SUBTOTAL', priceFormat($subTotal, true));

	if($totalWeight>0){
		$view_cart->assign('LANG_BASKET_WEIGHT',$lang['cart']['basket_weight']);
		$view_cart->assign('VAL_BASKET_WEIGHT',$totalWeight.$config['weightUnit']);
	}

	// start: Flexible Taxes, by Estelle Winterflood
	// grand total
	if ($config_tax_mod['status']) {
		$tax = 0;
		if (is_array($taxes)) {
			for ($i = 0, $maxi = count($taxes); $i < $maxi; ++$i) {
				$tax += $taxes[$i]['amount'];
			}
		}
		$grandTotal = $subTotal + $tax + $basket['shipCost'];
	}
	// end: Flexible Taxes
	else {
		$grandTotal = $subTotal + $tax + $basket['shipCost'];
	}


	$view_cart->assign('LANG_CART_TOTAL',$lang['cart']['cart_total']);
	$view_cart->assign('VAL_CART_TOTAL',priceFormat($grandTotal, true));

	if (isset($basket['codeResult'])) {
		if (!$basket['codeResult']) {
			# add remove link
			$view_cart->assign('LANG_CODE_REMOVE',$lang['cart']['coupon_remove']);
			$view_cart->assign('VAL_OLD_CODE',base64_encode($basket['code']));
			$view_cart->assign('VAL_CURRENT_PAGE',currentPage());
			$view_cart->parse('view_cart.cart_true.coupon_code_result.remove');
		}
		$view_cart->assign('LANG_CODE_RESULT',$lang['cart']['coupon_result_'.$basket['codeResult']]);
		$view_cart->parse('view_cart.cart_true.coupon_code_result');

	}

	if (!isset($basket['codeResult']) || $basket['codeResult']>0) {
		$view_cart->assign('LANG_GOT_CODE', $lang['cart']['got_code']);
		$view_cart->assign('LANG_ENTER_CODE', $lang['cart']['enter_code']);
		$view_cart->parse('view_cart.cart_true.enter_coupon_code');
		$basket = $cart->unsetVar('codeResult');
	}

	// build array of price vars in session data
	$basket = $cart->setVar(sprintf('%.2f',$subTotal),'subTotal');
	$basket = $cart->setVar(sprintf('%.2f',$tax),'tax');
	$basket = $cart->setVar(sprintf('%.2f',$grandTotal),'grandTotal');
	// start: Flexible Taxes, by Estelle Winterflood
	// leave "tax" in session data as the total tax
	// define a new array to hold the individual tax names/amounts
	if ($config_tax_mod['status']) {
		for ($i = 1, $maxi = count($taxes); $i <= $maxi; ++$i) {
			$basket = $cart->setVar($taxes[$i-1]['display'],'tax'.$i.'_disp');
			$basket = $cart->setVar(sprintf('%.2f',$taxes[$i-1]['amount']),'tax'.$i.'_amt');
		}

	}
	// end: Flexible Taxes

	$view_cart->assign('LANG_UPDATE_CART_DESC',$lang['cart']['if_changed_quan']);

	$view_cart->assign('LANG_UPDATE_CART',$lang['cart']['update_cart']);

	$view_cart->assign('LANG_CHECKOUT',$lang['cart']['checkout']);
	// see if passports are enabled
	$altCheckout = $db->select('SELECT `folder`, `default` FROM `'.$glob['dbprefix'].'CubeCart_Modules` WHERE `module`=\'altCheckout\' AND `status` = 1');

	// stop alt checkout if they have aleady chosen PayPal... mission
	if($grandTotal>0 && !isset($_SESSION['token']) && !isset($_SESSION['payer_id']) && $altCheckout) {

		$noAltCheckouts = count($altCheckout);

		if($noAltCheckouts == 1 && $altCheckout[0]['folder']=='PayPal_Pro') { unset($customWarn); }

		if ($customWarn) {
			$view_cart->assign('LANG_CUSTOM_WARN',$lang['cart']['custom_warn']);
			$view_cart->parse('view_cart.cart_true.alt_checkout.custom_warn');
		}
		$view_cart->assign('LANG_ALTERNATIVE_CHECKOUT',$lang['cart']['alternative']);

		## Get alt checkout shipping
		$query = "SELECT `name`, `byprice`, `low`, `high`, `price` FROM ".$glob['dbprefix']."CubeCart_alt_shipping a LEFT JOIN ".$glob['dbprefix']."CubeCart_alt_shipping_prices b ON a.id = b.alt_ship_id ORDER BY `order`, `low`, `high` ASC";
		$altShipping = $db->select($query);

		if ($altShipping) {
			$n=0;
			for($i = 0, $maxi = count($altShipping); $i < $maxi; ++$i) {
				if($altShipping[$i]['byprice']==1 && $subTotal>=$altShipping[$i]['low'] && $subTotal<=$altShipping[$i]['high']){
					$altShippingPrices[$n]['name'] = $altShipping[$i]['name'];
					$altShippingPrices[$n]['price'] = $altShipping[$i]['price'];
					++$n;
				} elseif($altShipping[$i]['byprice']==0 && $totalWeight>=$altShipping[$i]['low'] && $totalWeight<=$altShipping[$i]['high']) {
					$altShippingPrices[$n]['name'] = $altShipping[$i]['name'];
					$altShippingPrices[$n]['price'] = $altShipping[$i]['price'];
					++$n;
				}
			}
		}

		if (!isset($customWarn) || !$customWarn) {	
			## build checkout buttons
			for ($i = 0, $maxi = count($altCheckout); $i < $maxi; ++$i) {
				require('modules'.CC_DS.'altCheckout'.CC_DS.$altCheckout[$i]['folder'].CC_DS.'button.php');
				## get module config
				$altCheckoutConf = fetchDbConfig($altCheckout[$i]['folder']);
				if($altCheckoutConf['mode']!=='USDPO') {
					## Run class and functions
					$altCheckoutButton = new $altCheckout[$i]['folder']();
					$buttonCode = $altCheckoutButton->buildIt($altCheckout[$i]['folder']);
					$view_cart->assign('IMG_CHECKOUT_ALT',$buttonCode);
					$view_cart->parse('view_cart.cart_true.alt_checkout.loop_button');
					## set include path right again - this fixes things, for some reason
					ini_set('include_path', ini_get('include_path').CC_PS.CC_ROOT_DIR);
				}
			}
		}
		$view_cart->parse('view_cart.cart_true.alt_checkout');
	}
	$view_cart->assign('VAL_FORM_ACTION', currentPage());
	$view_cart->parse('view_cart.cart_true');
} else {

	$view_cart->assign('CONT_VAL','index.php?_g=co&amp;_a=step1');
	$view_cart->assign('LANG_CHECKOUT_BTN',$lang['cart']['checkout_btn']);
	$view_cart->assign('LANG_VIEW_CART',$lang['cart']['view_cart']);
	$view_cart->assign('CLASS_CART','class="txtcartProgressCurrent"');
	$view_cart->assign('CLASS_STEP2','');

	$view_cart->assign('LANG_CART_EMPTY', $lang['cart']['cart_empty']);
	$view_cart->parse('view_cart.cart_false');

}
$view_cart->parse('view_cart');
$page_content = $view_cart->text('view_cart');
?>