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
|	randomProduct.inc.php
|   ========================================
|	Random Product Box
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$lang = getLang("includes".CC_DS."boxes".CC_DS."randomProd.inc.php");
$seed = mt_rand(1, 10000);

if(isset($_GET['catId']) && $_GET['catId']!== 'saleItems') {
	$_GET['catId'] = (int)$_GET['catId'];
}

$whereClause	= (isset($_GET['catId']) && is_numeric($_GET['catId'])) ? "AND I.cat_id=".$db->mySQLSafe($_GET['catId'])." AND I.cat_id > 0 AND C.hide != '1'" : "AND I.cat_id > 0";
$sql			= sprintf("SELECT I.name, I.image, I.productId FROM %1\$sCubeCart_inventory AS I, %1\$sCubeCart_category AS C WHERE I.cat_id = C.cat_id AND I.disabled != '1' AND C.hide = '0' AND (C.cat_desc != '##HIDDEN##' OR C.cat_desc IS NULL) %2\$s ORDER BY RAND(%3\$d) LIMIT 1", $glob['dbprefix'], $whereClause, $seed);
$randProd		= $db->select($sql);

if ($randProd) {
	if (($val = prodAltLang($randProd[0]['productId'])) !== false) {
		$randProd[0]['name'] = $val['name'];
	}

	$box_content = new XTemplate ('boxes'.CC_DS.'randomProd.tpl');

	$box_content->assign('LANG_RANDOM_PRODUCT',$lang['randomProd']['featured_prod']);
	$box_content->assign('PRODUCT_ID',$randProd[0]['productId']);
	$box_content->assign('PRODUCT_NAME',validHTML($randProd[0]['name']));

	$thumbRootPath	= imgPath($randProd[0]['image'],1,'root');
	$thumbRelPath	= imgPath($randProd[0]['image'],1,'rel');

	if (file_exists($thumbRootPath) && !empty($randProd[0]['image'])) {
		$box_content->assign('IMG_SRC', $thumbRelPath);
	} else {
		$box_content->assign('IMG_SRC', $GLOBALS['rootRel'].'skins/'. SKIN_FOLDER . '/styleImages/thumb_nophoto.gif');
	}

	$box_content->parse('random_prod');
	$box_content = $box_content->text('random_prod');

} else {
	$box_content = '';
}
?>