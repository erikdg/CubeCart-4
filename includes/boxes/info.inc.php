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
|	info.inc.php
|   ========================================
|	Info & Stats Box
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

// include lang file
$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'info.inc.php');

// query database
$cache = new cache('boxes.info.noProducts');
$noProducts = $cache->readCache();
if (!$cache->cacheStatus) {
	$noProducts = $db->select("SELECT COUNT(`productId`) as no FROM ".$glob['dbprefix']."CubeCart_inventory AS I, ".$glob['dbprefix']."CubeCart_category AS C WHERE I.cat_id = C.cat_id AND I.cat_id > 0 AND (C.cat_desc != '##HIDDEN##' OR C.cat_desc IS NULL) AND I.disabled = '0'");
	$cache->writeCache($noProducts);
}

// query database
$cache = new cache('boxes.info.noCategories');
$noCategories = $cache->readCache();
if (!$cache->cacheStatus) {
	$noCategories = $db->select("SELECT COUNT(`cat_id`) as no FROM ".$glob['dbprefix']."CubeCart_category WHERE `hide` != 1 AND (`cat_desc` != '##HIDDEN##' OR `cat_desc` IS NULL)");
	$cache->writeCache($noCategories);
}

$box_content = new XTemplate ('boxes'.CC_DS.'info.tpl');

$box_content->assign('LANG_INFO_TITLE', $lang['info']['information']);
$box_content->assign('LANG_INFO_PRODUCTS', $lang['info']['products']);
$box_content->assign('DATA_NO_PRODUCTS', $noProducts[0]['no']);
$box_content->assign('LANG_INFO_CATEGORIES', $lang['info']['categories']);
$box_content->assign('DATA_NO_CATEGORIES', $noCategories[0]['no']);
$box_content->assign('LANG_INFO_PRICES', $lang['info']['prices']);
$box_content->assign('DATA_CURRENCY', $currencyVars[0]['name']);

$box_content->parse('info');

$box_content = $box_content->text('info');
?>