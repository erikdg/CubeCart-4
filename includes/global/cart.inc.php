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
|	Controls Cart Actions
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

$body = new XTemplate('global'.CC_DS.'cart.tpl');

if (isset($_GET['searchStr'])) {
	$body->assign('SEARCHSTR', sanitizeVar($_GET['searchStr']));
} else {
	$body->assign('SEARCHSTR', '');
}
//$body->assign('CURRENCY_VER',$currencyVer);

## Incluse langauge config
include('language'.CC_DS.LANG_FOLDER.CC_DS.'config.php');
$body->assign('VAL_ISO',$charsetIso);

## START META DATA
$body->assign('META_TITLE', stripslashes(str_replace('&#39;', "'", $config['siteTitle'])));
$body->assign('META_DESC', stripslashes($config['metaDescription']));
$body->assign('META_KEYWORDS', stripslashes($config['metaKeyWords']));

$returnPage = urlencode(currentPage());

## START  MAIN CONTENT
switch (sanitizeVar($_GET['_a'])) {
	case 'step1':
		require_once 'includes'.CC_DS.'content'.CC_DS.'step1.inc.php';
		break;
	case 'cart':
	case 'step2':
		require_once 'includes'.CC_DS.'content'.CC_DS.'cart.inc.php';
		break;
	case 'step3':
		require_once 'includes'.CC_DS.'content'.CC_DS.'gateway.inc.php';
		break;
	case 'reg':
		require_once 'includes'.CC_DS.'content'.CC_DS.'reg.inc.php';
		break;
	case 'viewOrders':
		require_once 'includes'.CC_DS.'content'.CC_DS.'viewOrders.inc.php';
		break;
	case 'viewOrder':
		require_once 'includes'.CC_DS.'content'.CC_DS.'viewOrder.inc.php';
		break;
	case 'error':
		require_once 'includes'.CC_DS.'content'.CC_DS.'error.inc.php';
		break;
	case 'confirmed':
		require_once 'includes'.CC_DS.'content'.CC_DS.'confirmed.inc.php';
		break;
	default:
		httpredir('index.php');
}

## START CONTENT BOXES
require_once 'includes'.CC_DS.'boxes'.CC_DS.'searchForm.inc.php';
$body->assign('SEARCH_FORM', $box_content);

require_once 'includes'.CC_DS.'boxes'.CC_DS.'session.inc.php';
$body->assign('SESSION', $box_content);

require_once 'includes'.CC_DS.'boxes'.CC_DS.'siteDocs.inc.php';
$body->assign('SITE_DOCS', $box_content);

require_once 'includes'.CC_DS.'boxes'.CC_DS.'cartNavi.inc.php';
$body->assign('CART_NAVI', $box_content);

## added in 4.0.3 - not part of templates, but designers can use them if they want
require_once'includes'.CC_DS.'boxes'.CC_DS.'currency.inc.php';
$body->assign('CURRENCY',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'categories.inc.php';
$body->assign('CATEGORIES',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'mailList.inc.php';
$body->assign('MAIL_LIST',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'shoppingCart.inc.php';
$body->assign('SHOPPING_CART',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'randomProd.inc.php';
$body->assign('RANDOM_PROD',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'info.inc.php';
$body->assign('INFORMATION',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'language.inc.php';
$body->assign('LANGUAGE',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'popularProducts.inc.php';
$body->assign('POPULAR_PRODUCTS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'saleItems.inc.php';
$body->assign('SALE_ITEMS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'skin.inc.php';
$body->assign('SKIN',$box_content);
## END CONTENT BOXES

?>