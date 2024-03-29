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
|	popularProducts.inc.php
|   ========================================
|	Display the most Popular Products
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");
## Only show if we have number of items set to > 0
if($config['noPopularBoxItems']) {
	## include lang file
	$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'popularProducts.inc.php');

	## query database
	if ($config['pop_products_source']) {
		## inner join on inventory table to make sure the product exists still
		$cache = new cache('boxes.popularProds');
		$popularProds = $cache->readCache();

		if (!$cache->cacheStatus) {
			$popularProds = $db->select("SELECT I.name, I.productId , COUNT(I.productId) AS total FROM ".$glob['dbprefix']."CubeCart_order_inv AS O, ".$glob['dbprefix']."CubeCart_inventory AS I, ".$glob['dbprefix']."CubeCart_category AS C WHERE O.productId = I.productId AND C.cat_id = I.cat_id AND C.hide != '1' AND I.disabled != '1' AND I.cat_id > 0 GROUP BY I.name DESC ORDER BY total DESC", $config['noPopularBoxItems']);
			$cache->writeCache($popularProds);
		}
	} else {
		## we wont cache this as it changes too quickly
		$popularProds = $db->select("SELECT I.name, I.productId FROM ".$glob['dbprefix']."CubeCart_inventory AS I, ".$glob['dbprefix']."CubeCart_category AS C WHERE C.cat_id = I.cat_id AND I.cat_id > 0 AND I.disabled = '0' AND (C.cat_desc != '##HIDDEN##' OR C.cat_desc IS NULL) ORDER BY I.popularity DESC",$config['noPopularBoxItems']);
	}

	$box_content = new XTemplate('boxes'.CC_DS.'popularProducts.tpl');

	$box_content->assign('LANG_POPULAR_PRODUCTS_TITLE', $lang['popularProducts']['popular_products']);

	if ($popularProds) {
		foreach ($popularProds as $popProduct) {
			if (($val = prodAltLang($popProduct['productId']))) {
				$popProduct['name'] = $val['name'];
			}
			$popProduct['name']		= validHTML($popProduct['name']);

			$box_content->assign('DATA', $popProduct);
			$box_content->parse('popular_products.li');
		}
	}

	$box_content->parse('popular_products');
	$box_content = $box_content->text('popular_products');
} else {
	$box_content = '';
}
?>