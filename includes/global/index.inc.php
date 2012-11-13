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
|	index.inc.php
|   ========================================
|	Main pages of the store
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

$body = new XTemplate ('global'.CC_DS.'index.tpl');

## Extra Events
$extraEvents = '';
if (isset($_GET['added']) && !empty($_GET['added'])) {
	if (!$cc_session->ccUserData['customer_id'] && $config['hide_prices'] == 1) {
		## have a break, have a KitKat
	} else {
		$extraEvents = 'flashBasket(6);';
	}
}
$body->assign('EXTRA_EVENTS',$extraEvents);

if (isset($_GET['searchStr'])) {
	$body->assign('SEARCHSTR', sanitizeVar($_GET['searchStr']));
} else {
	$body->assign('SEARCHSTR','');
}

//$body->assign('CURRENCY_VER',$currencyVer);

## Incluse langauge config
include('language'.CC_DS.LANG_FOLDER.CC_DS.'config.php');
$body->assign('VAL_ISO',$charsetIso);

## START CONTENT BOXES
require_once 'includes'.CC_DS.'boxes'.CC_DS.'searchForm.inc.php';
$body->assign('SEARCH_FORM',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'session.inc.php';
$body->assign('SESSION',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'categories.inc.php';
$body->assign('CATEGORIES',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'randomProd.inc.php';
$body->assign('RANDOM_PROD',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'info.inc.php';
$body->assign('INFORMATION',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'language.inc.php';
$body->assign('LANGUAGE',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'currency.inc.php';
$body->assign('CURRENCY',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'shoppingCart.inc.php';
$body->assign('SHOPPING_CART',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'popularProducts.inc.php';
$body->assign('POPULAR_PRODUCTS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'saleItems.inc.php';
$body->assign('SALE_ITEMS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'mailList.inc.php';
$body->assign('MAIL_LIST',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'siteDocs.inc.php';
$body->assign('SITE_DOCS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'skin.inc.php';
$body->assign('SKIN',$box_content);
## END CONTENT BOXES

## START  MAIN CONTENT
if (!empty($_GET['_a'])) {
	#if ($_GET['_a'] == 'search') $_GET['_a'] = 'viewCat';
	if (file_exists('includes'.CC_DS.'content'.CC_DS.sanitizeVar($_GET['_a']).'.inc.php')) {
		require_once('includes'.CC_DS.'content'.CC_DS.sanitizeVar($_GET['_a']).'.inc.php');
	} else {
		require_once('includes'.CC_DS.'content'.CC_DS.'index.inc.php');
	}
} else {
	require_once('includes'.CC_DS.'content'.CC_DS.'index.inc.php');
}

## END MAIN CONTENT



## START META DATA
if (isset($meta)) {
	$meta['title'] = sefMetaTitle();
	$meta['description'] = sefMetaDesc();
	$meta['keywords'] = sefMetaKeywords();

} else {
	$meta['title'] = str_replace("&#39;","'",$config['siteTitle']);
	$meta['description'] = $config['metaDescription'];
	$meta['keywords'] = $config['metaKeyWords'];
}

$body->assign('META_TITLE', stripslashes($meta['title']));
$body->assign('META_DESC', stripslashes($meta['description']));
$body->assign('META_KEYWORDS', stripslashes($meta['keywords']));
?>