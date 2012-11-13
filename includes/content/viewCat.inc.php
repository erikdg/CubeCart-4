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
|	viewCat.inc.php
|   ========================================
|	Display the Current Category
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'viewCat.inc.php');
$page = (isset($_GET['page'])) ? (int)sanitizeVar($_GET['page']) : 0;
$view_cat = new XTemplate ('content'.CC_DS.'viewCat.tpl');
$view_cat->assign('LANG_DIR_LOC', $lang['viewCat']['location']);

////////////////////////
// BUILD SUB CATEGORIES
////////////////////////

if (isset($_GET['catId'])) {
	$_GET['catId'] = ($_GET['catId'] == 'saleItems') ? $_GET['catId'] : (int)sanitizeVar($_GET['catId']);
	## build query
	$emptyCat	= ($config['show_empty_cat']) ? '' : ' AND `noProducts` >= 1';
	$query		= "SELECT * FROM ".$glob['dbprefix']."CubeCart_category WHERE `cat_father_id` = ".$db->mySQLSafe($_GET['catId'])." AND `hide` = '0'".$emptyCat." ORDER BY `priority`,`cat_name` ASC";

	## get category array in foreign innit
	$resultsForeign = $db->select("SELECT `cat_master_id` as cat_id, `cat_name` FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE `cat_lang` = '" . LANG_FOLDER . "'");

	## query database
	$subCategories = $db->select($query);
}

if (isset($_GET['catId']) && $_GET['catId']>0 && $subCategories) {
	## loop results
	for ($i = 0, $maxi = count($subCategories); $i < $maxi; ++$i) {
		if (is_array($resultsForeign)) {
			for ($k = 0, $maxk = count($resultsForeign); $k < $maxk; ++$k) {
				if ($resultsForeign[$k]['cat_id'] == $subCategories[$i]['cat_id']) {
					$subCategories[$i]['cat_name'] = $resultsForeign[$k]['cat_name'];
				}
			}
		}

		$catImg = imgPath($subCategories[$i]['cat_image'], true, 'rel');
		$catImgRoot = imgPath($subCategories[$i]['cat_image'], true, 'root');

		if (!empty($subCategories[$i]['cat_image']) && file_exists($catImgRoot)) {
			$view_cat->assign('IMG_CATEGORY', $catImg);
		} else {
			$view_cat->assign('IMG_CATEGORY', $GLOBALS['rootRel'].'skins/'. SKIN_FOLDER . '/styleImages/catnophoto.gif');
		}

		$view_cat->assign('TXT_LINK_CATID', $subCategories[$i]['cat_id']);
		$view_cat->assign('TXT_CATEGORY', validHTML($subCategories[$i]['cat_name']));
		$view_cat->assign('NO_PRODUCTS', $subCategories[$i]['noProducts']);
		$view_cat->parse('view_cat.sub_cats.sub_cats_loop');
	}
	$view_cat->parse('view_cat.sub_cats');
}

////////////////////////////
// BUILD PRODUCTS
////////////////////////////

## New! Product sorting by field
$allowedSort	= array('price', 'description', 'name', 'productCode', 'date_added');
if (isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowedSort)) {
	switch ($_GET['sort_order']) {
		case 'high':
			$orderType = 'DESC';
			$orderText = '&uarr';
			$sortIcon = 'bullet_arrow_up.gif';
			break;
		case 'low':
			$orderType = 'ASC';
			$orderText = '&darr';
			$sortIcon = 'bullet_arrow_down.gif';
			break;
		default:
			$orderType = 'ASC';
			$sortIcon = 'bullet_arrow_down.gif';
	}
	$orderSort = sprintf(' ORDER BY %s %s', $_GET['sort_by'], $orderType);
} else {
	if ($config['cat_newest_first']) {
		$orderSort = sprintf(' ORDER BY `date_added` DESC, `name` ASC');
	} else {
		$orderSort = false;
	}
}

## build query
if (isset($_REQUEST['searchStr']) || !empty($_REQUEST['priceMin']) || !empty($_REQUEST['priceMax'])) {
	unset($_GET['Submit']);

	$_REQUEST['searchStr'] = ($_REQUEST['searchStr'] == 'or' || $_REQUEST['searchStr'] == 'and' || $_REQUEST['searchStr'] == 'not') ? '' : $_REQUEST['searchStr'];
	// Trim allowing alpha numeric only
	// This should allow all kinds of accents and umlauts etc on the word....
	$_REQUEST['searchStr'] = preg_replace("/[^\w\s'_-]/", "", $_REQUEST['searchStr']);
	$_REQUEST['searchStr'] = preg_replace(array('#\sor\s#i', '#\sand\s#i', '#\snot\s#i'), ' ', $_REQUEST['searchStr']);


	/* LOG SEARCH PHRASE */
	$searchQuery = 'SELECT `id` FROM '.$glob['dbprefix'].'CubeCart_search WHERE `searchstr` = '.$db->mySQLsafe($_REQUEST['searchStr']);
	$searchLogs = $db->select($searchQuery);

	$insertStr['searchstr'] = $db->mySQLsafe($_REQUEST['searchStr']);
	$insertStr['hits'] = $db->mySQLsafe(1);
	$updateStr['hits'] = 'hits+1';

	if ($searchLogs) {
		$counted = $db->update($glob['dbprefix'].'CubeCart_search', $updateStr,'`id` = '.$searchLogs[0]['id'],'');
	} else if (!empty($_REQUEST['searchStr'])) {
		$counted = $db->insert($glob['dbprefix'].'CubeCart_search', $insertStr);
	}

	$indexes = $db->getFulltextIndex('inventory', 'I'); //array('inventory', 'inv_lang'));

	if (!empty($_REQUEST['priceMin']) && is_numeric($_REQUEST['priceMin'])) $where[] = sprintf("I.price >= %s", number_format($_REQUEST['priceMin']/$currencyVars[0]['value'], 2, '.', ''));
	if (!empty($_REQUEST['priceMax']) && is_numeric($_REQUEST['priceMax'])) $where[] = sprintf("I.price <= %s", number_format($_REQUEST['priceMax']/$currencyVars[0]['value'], 2, '.', ''));

	if (isset($_REQUEST['inStock'])) $where[] = "((I.useStockLevel = 0) OR (I.useStockLevel = 1 AND I.stock_level > 0))";

	if (!empty($_REQUEST['category'])) {
		if (is_array($_REQUEST['category'])) {
			foreach ($_REQUEST['category'] as $cat_id) {
				if (is_numeric($cat_id)) $cats[] = $cat_id;
			}
			if (!empty($cats)) $where[] = sprintf("I.cat_id IN (%s)", implode(',', $cats));
		} else if (is_numeric($_REQUEST['category'])) {
			$where[] = sprintf("I.cat_id = '%d'", $db->mySQLsafe($_REQUEST['category']));
		}
	}

	$where[] = 'C.cat_id = I.cat_id';
	$where[] = "C.hide = '0'";
	$where[] = "(C.cat_desc != '##HIDDEN##' OR C.cat_desc IS NULL)";
	$where[] = "I.disabled = '0'";

	$whereString = sprintf('AND %s', implode(' AND ', $where));

	$terms = trim($_REQUEST['searchStr']);
	
	$strlen_check = explode(" ",$terms);
	$min_length = $db->getSearchWordLen();
	$skip_full_text = false;
	foreach($strlen_check as $word) {
		if(!$skip_full_text && strlen($word) <= $min_length) {
			$skip_full_text = true;
		}
	}

	if (is_array($indexes) && !$skip_full_text) {
		// Tweaks thanks to Technocrat
        $terms = str_replace(array('-', '*', '~', '+'), ' ', $terms);
        $terms = str_replace(' ', '*) +(*', $terms);
		$terms .= '*)';
		$terms = '+(*'.$terms;

		sort($indexes);
		if (empty($orderSort)) {
			$orderSort = ' ORDER BY SearchScore DESC';
		}

		if (isset($_REQUEST['searchStr'])) {
			$matchString = sprintf('MATCH (%s) AGAINST(%s IN BOOLEAN MODE)', implode(',', $indexes), $db->mySQLsafe($terms));
			$search = sprintf("SELECT DISTINCT(I.productId), I.*, %2\$s AS SearchScore FROM %1\$sCubeCart_inventory AS I, %1\$sCubeCart_category AS C WHERE (%2\$s) >= %4\$F AND C.cat_id > 0 %3\$s %5\$s", $glob['dbprefix'], $matchString, $whereString, 0.5, $orderSort);
		} else {
			$search = sprintf("SELECT DISTINCT(I.productId), I.* FROM %1\$sCubeCart_inventory AS I, %1\$sCubeCart_category AS C WHERE I.cat_id > 0 %2\$s %3\$s", $glob['dbprefix'], $whereString, $orderSort);
		}
		$productListQuery = $search;
		## Moved into if to stop MySQL error on index failure
		$productResults = $db->select($productListQuery, $config['productPages'], $page);
	}

	// If there are STILL no results fall back to basic search.
	if (!$productResults) {
		if (!isset($searchArray)) {
			$searchwords = preg_split( '/[ ,]/', $db->mySQLsafe($_REQUEST['searchStr'],false));
			foreach ($searchwords as $word) {
				$searchArray[] = $word;
			}
		}
		$noKeys = count($searchArray);
		$regexp = '';
		for ($i=0; $i<$noKeys; ++$i) {
			$ucSearchTerm = strtoupper($searchArray[$i]);
			if (($ucSearchTerm != 'AND') && ($ucSearchTerm != 'OR')) {
				$regexp .= '[[:<:]]'.$searchArray[$i].'[[:>:]].*';
			}
		}
		$regexp = substr($regexp, 0, strlen($regexp)-2);
		$like	= "(I.name RLIKE '".$regexp."' OR I.description RLIKE '".$regexp."' OR I.productCode RLIKE '".$regexp."')";

		if (empty($orderSort) || strstr($orderSort, 'SearchScore')) $orderSort = sprintf(' ORDER BY name ASC');
		$productListQuery	= sprintf("SELECT DISTINCT(I.productId), I.*, I.name AS SearchScore FROM %1\$sCubeCart_inventory AS I, %1\$sCubeCart_category AS C WHERE %2\$s AND C.cat_id > 0 %3\$s ORDER BY %4\$s", $glob['dbprefix'], $like, $whereString, str_replace('ORDER BY', '', $orderSort));
		$productResults	= $db->select($productListQuery, $config['productPages'], $page);
	}

} else if ($_GET['catId'] == 'saleItems' && $config['saleMode'] >= 1) {
	$productListQuery = sprintf("SELECT DISTINCT(I.productId), C.cat_id, I.productCode, I.description, I.image, I.price, I.name, I.popularity, I.sale_price, I.stock_level, I.useStockLevel FROM %1\$sCubeCart_cats_idx AS C INNER JOIN %1\$sCubeCart_inventory AS I ON C.productId = I.productId WHERE I.disabled = '0' AND I.sale_price != 0 AND C.cat_id > 0 GROUP BY I.productId %2\$s", $glob['dbprefix'], $orderSort);
} else {
	$productListQuery = sprintf("SELECT DISTINCT(I.productId), C.cat_id, I.productCode, I.description, I.image, I.price, I.name, I.popularity, I.sale_price, I.stock_level, I.useStockLevel FROM %1\$sCubeCart_cats_idx AS C INNER JOIN %1\$sCubeCart_inventory AS I ON C.productId = I.productId WHERE I.disabled = '0' AND C.cat_id > 0 AND C.cat_id = '%2\$d' GROUP BY I.productId %3\$s", $glob['dbprefix'], $_GET['catId'], $orderSort);
}

## Run query if we haven't already done a search
if (!isset($productResults)) {
	$productResults = $db->select($productListQuery, $config['productPages'], $page);
}

## Get different languages
if ($productResults && LANG_FOLDER != $config['defaultLang']) {
	for ($i = 0, $maxi = count($productResults);$i < $maxi; ++$i) {
		if (($val = prodAltLang($productResults[$i]['productId'])) !== false) {
			$productResults[$i]['name'] = $val['name'];
			$productResults[$i]['description'] = $val['description'];
		}
	}
}
$totalNoProducts = $db->numrows($productListQuery);

## Get current category info
if (isset($_GET['catId'])) {
	if ($config['seftags']) {
		if ($_GET['catId']>0) {

			$currentCatQuery	= "SELECT `cat_metatitle`, `cat_metadesc`, `cat_metakeywords`, `cat_name`, `cat_father_id`, `cat_id`, `cat_image`, `cat_desc` FROM ".$glob['dbprefix']."CubeCart_category WHERE `cat_id` = ".$db->mySQLSafe($_GET['catId'])." ORDER BY `priority`,`cat_name` ASC";
			$currentCat			= $db->select($currentCatQuery);

			$prevDirSymbol				= $config['dirSymbol'];
			$config['dirSymbol']		= ' - ';
			$meta['siteTitle']			= getCatDir($currentCat[0]['cat_name'],$currentCat[0]['cat_father_id'], $currentCat[0]['cat_id'], false, true, $config['sefprodnamefirst'] ? false : true);
			$config['dirSymbol']		= $prevDirSymbol;

			$meta['metaDescription']	= strip_tags($config['metaDescription']);
			$meta['sefSiteTitle']		= $currentCat[0]['cat_metatitle'];
			$meta['sefSiteDesc']		= $currentCat[0]['cat_metadesc'];
			$meta['sefSiteKeywords']	= $currentCat[0]['cat_metakeywords'];

		} else if (strcmp($_GET['catId'], 'saleItems') == 0) {
			$meta['siteTitle'] = $lang['front']['boxes']['sale_items'];
			$meta['metaDescription'] = strip_tags($config['metaDescription']);
		}
	} else if (is_numeric($_GET['catId'])) {
	    $currentCatQuery = "SELECT `cat_name`, `cat_father_id`, `cat_id`, `cat_image`, `cat_desc` FROM ".$glob['dbprefix']."CubeCart_category WHERE `cat_id` = ".$db->mySQLSafe($_GET['catId'])." AND (cat_desc != '##HIDDEN##' OR cat_desc IS NULL) ORDER BY priority,cat_name ASC";
	    $currentCat = $db->select($currentCatQuery);
	}

	# Get translations
	$resultForeign = $db->select("SELECT `cat_master_id` as cat_id, `cat_name`, `cat_desc` FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE `cat_lang` = '".LANG_FOLDER."' AND cat_master_id = ".$db->mySQLSafe($_GET['catId']));
	if ($resultForeign) {
		$currentCat[0]['cat_name'] = $resultForeign[0]['cat_name'];
		$currentCat[0]['cat_desc'] = $resultForeign[0]['cat_desc'];
	}
}

if (!empty($currentCat[0]['cat_image'])) {
	$view_cat->assign('IMG_CURENT_CATEGORY', imgPath($currentCat[0]['cat_image'], false, 'rel'));
	$view_cat->assign('TXT_CURENT_CATEGORY', validHTML($currentCat[0]['cat_name']));
	$view_cat->parse('view_cat.cat_img');
}

if (isset($_REQUEST['searchStr']) || isset($_REQUEST['priceMin']) || isset($_REQUEST['priceMax'])) {
	$view_cat->assign('TXT_CAT_TITLE', $lang['viewCat']['search_results']);
	$view_cat->assign('CURRENT_LOC', $config['dirSymbol'].$lang['viewCat']['search_results']);

} else if ($_GET['catId']=='saleItems' && $config['saleMode']>0) {
	$view_cat->assign('TXT_CAT_TITLE', $lang['viewCat']['sale_items']);
	$view_cat->assign('CURRENT_LOC', $config['dirSymbol'].$lang['viewCat']['sale_items']);
} else {
	$view_cat->assign('TXT_CAT_TITLE', validHTML($currentCat[0]['cat_name']));
	$view_cat->assign('CURRENT_LOC', getCatDir($currentCat[0]['cat_name'], $currentCat[0]['cat_father_id'], $currentCat[0]['cat_id'], true));
}

if (!empty($currentCat[0]['cat_desc'])) {
	$view_cat->assign('TXT_CAT_DESC', $currentCat[0]['cat_desc']);
	$view_cat->parse('view_cat.cat_desc');
}

$view_cat->assign('LANG_IMAGE', $lang['viewCat']['image']);
$view_cat->assign('LANG_DESC', $lang['viewCat']['description']);
$view_cat->assign('LANG_NAME', $lang['viewCat']['name']);
$view_cat->assign('LANG_PRICE', $lang['viewCat']['price']);
$view_cat->assign('LANG_DATE', (isset($lang['viewCat']['date_added'])) ? $lang['viewCat']['date_added'] : '');

$pagination = paginate($totalNoProducts, $config['productPages'], $page, 'page', 'txtLink', 5, array('Submit' => 1));

if (!empty($pagination)) {
	$view_cat->assign('PAGINATION', $pagination);
	$view_cat->parse('view_cat.pagination_top');
	$view_cat->parse('view_cat.pagination_bot');
}

## create the links for product sorting - need improving later
$sort_order = (!isset($_GET['sort_order']) || $_GET['sort_order'] == 'high') ? 'low' : 'high';
$_GET['sort_by'] = (isset($_GET['sort_by'])) ? $_GET['sort_by'] : '';
switch($_GET['sort_by']) {
	case 'name':
		$view_cat->assign('SORT_NAME_SELECTED', ' selected="selected"');
		break;
	case 'price':
		$view_cat->assign('SORT_PRICE_SELECTED', ' selected="selected"');
		break;
	case 'date_added':
		$view_cat->assign('SORT_PRICE_SELECTED', ' selected="selected"');
		break;
}

#	$view_cat->assign('SORT_DIRECTION_TEXT', $orderText);
unset($_GET['sort_by'], $_GET['sort_order']);

$currPage = currentPage();
if ($config['sef']) {
	$currPage = '?';
}

$sortTypes = array(
	'SORT_PROD_CODE'=> 'productCode',
	'SORT_PRICE'	=> 'price',
	'SORT_DESC'		=> 'description',
	'SORT_NAME'		=> 'name',
	'SORT_DATE'		=> 'date_added',
);

$queryString	= parse_url(html_entity_decode(currentPage()), PHP_URL_QUERY);
parse_str($queryString, $currentQuery);
//ksort($currentQuery); REMOVED AS _a first invokes SEO which we don't want on pagiation
foreach ($sortTypes as $assign_key => $field) {
	$currentQuery['sort_by']	= $field;
	$currentQuery['sort_order']	= $sort_order;
	// str_replace is a hack to fix pagination seo rewrite
	$view_cat->assign($assign_key, $currPage.http_build_query($currentQuery, '', '&amp;'));
}

#$view_cat->assign('SORT_PROD_CODE', $currPage."&amp;sort_by=productCode&amp;sort_order=".$sort_order);
#$view_cat->assign('SORT_PRICE', $currPage."&amp;sort_by=price&amp;sort_order=".$sort_order);
#$view_cat->assign('SORT_DESC', $currPage."&amp;sort_by=description&amp;sort_order=".$sort_order);
#$view_cat->assign('SORT_NAME', $currPage."&amp;sort_by=name&amp;sort_order=".$sort_order);
#$view_cat->assign('SORT_DATE', $currPage."&amp;sort_by=date_added&amp;sort_order=".$sort_order);

if (!empty($sortIcon) && file_exists('skins'.CC_DS.SKIN_FOLDER.CC_DS.'styleImages'.CC_DS.'icons'.CC_DS.$sortIcon)) {
	$view_cat->assign('SORT_ICON', sprintf('<img src="%s", alt="" />', 'skins'.CC_DS.SKIN_FOLDER.CC_DS.'styleImages'.CC_DS.'icons'.CC_DS.$sortIcon));
}

## repeated region
if ($productResults) {
	for ($i = 0, $maxi = count($productResults); $i < $maxi; ++$i) {
		## alternate class
		if (isset($productResults[$i]['name']) && !empty($productResults[$i]['name'])) {
			$view_cat->assign('CLASS', cellColor($i, 'tdEven', 'tdOdd'));

			$thumbRoot		= imgPath($productResults[$i]['image'], true, 'root');
			$thumbRootRel	= imgPath($productResults[$i]['image'], true, 'rel');

			if (file_exists($thumbRoot)) {
				$view_cat->assign('SRC_PROD_THUMB', $thumbRootRel);
			} else {
				$view_cat->assign('SRC_PROD_THUMB', $GLOBALS['rootRel'].'skins/'. SKIN_FOLDER . '/styleImages/thumb_nophoto.gif');
			}

			$view_cat->assign('TXT_TITLE', validHTML(stripslashes($productResults[$i]['name'])));

			if (strlen($productResults[$i]['description']) > $config['productPrecis']) {
				$view_cat->assign('TXT_DESC', substr(strip_tags($productResults[$i]['description']), 0, $config['productPrecis']).'&hellip;');
			} else {
				$view_cat->assign('TXT_DESC', strip_tags($productResults[$i]['description']));
			}


			if (!salePrice($productResults[$i]['price'], $productResults[$i]['sale_price'])) {
				$view_cat->assign('TXT_PRICE', priceFormat($productResults[$i]['price'], true));
				$view_cat->assign('TXT_SALE_PRICE', '');

			} else {
				$view_cat->assign('TXT_PRICE','<span class="txtOldPrice">'.priceFormat($productResults[$i]['price'], true).'</span>');
				$salePrice = salePrice($productResults[$i]['price'], $productResults[$i]['sale_price']);
				$view_cat->assign('TXT_SALE_PRICE', priceFormat($salePrice, true));
			}

			if (isset($_GET['add']) && isset($_GET['quan'])) {
				$view_cat->assign('CURRENT_URL', str_replace(array('&amp;add='.$_GET['add'], '&amp;quan='.$_GET['quan']), '', currentPage()));

			} else {
				$view_cat->assign('CURRENT_URL', currentPage());
			}

			if ($config['outofstockPurchase']) {
				$view_cat->assign('BTN_BUY', $lang['viewCat']['buy']);
				$view_cat->assign('PRODUCT_ID', $productResults[$i]['productId']);
				$view_cat->parse('view_cat.productTable.products.buy_btn');

			} else if ($productResults[$i]['useStockLevel'] && $productResults[$i]['stock_level']>0) {
				$view_cat->assign('BTN_BUY', $lang['viewCat']['buy']);
				$view_cat->assign('PRODUCT_ID', $productResults[$i]['productId']);
				$view_cat->parse('view_cat.productTable.products.buy_btn');

			} else if (!$productResults[$i]['useStockLevel']) {
				$view_cat->assign('BTN_BUY', $lang['viewCat']['buy']);
				$view_cat->assign('PRODUCT_ID', $productResults[$i]['productId']);
				$view_cat->parse('view_cat.productTable.products.buy_btn');
			}

			$view_cat->assign('BTN_MORE', $lang['viewCat']['more']);
			$view_cat->assign('PRODUCT_ID', $productResults[$i]['productId']);

			if ($productResults[$i]['stock_level']<1 && $productResults[$i]['useStockLevel'] && !$productResults[$i]['digital']) {
				$view_cat->assign('TXT_OUTOFSTOCK', $lang['viewCat']['out_of_stock']);
			} else {
				$view_cat->assign('TXT_OUTOFSTOCK', '');
			}

			$view_cat->parse('view_cat.productTable.products');
		}
	}

	$view_cat->assign('LANG_SORT', $lang['viewCat']['sort']);
	$view_cat->parse('view_cat.productTable');

} else if (!$productResults && isset($_REQUEST['searchStr'])) {
	$view_cat->assign('TXT_NO_PRODUCTS', sprintf($lang['viewCat']['no_products_match'], htmlspecialchars(stripslashes($_REQUEST['searchStr']))));
	$view_cat->parse('view_cat.noProducts');

} else {
	$view_cat->assign('TXT_NO_PRODUCTS', $lang['viewCat']['no_prods_in_cat']);
	$view_cat->parse('view_cat.noProducts');
}

$view_cat->parse('view_cat');
$page_content = $view_cat->text('view_cat');
?>