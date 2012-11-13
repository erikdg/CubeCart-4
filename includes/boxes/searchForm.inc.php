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
|	searchForm.inc.php
|   ========================================
|	Search Box
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

// include lang file
$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'searchForm.inc.php');
$box_content = new XTemplate ('boxes'.CC_DS.'searchForm.tpl');

$box_content->assign('LANG_SEARCH_FOR',$lang['searchForm']['search_for']);
if (isset($_GET['searchStr'])) {
	$box_content->assign('SEARCHSTR', sanitizeVar($_GET['searchStr']));
} else {
	$box_content->assign('SEARCHSTR', '');
}
$box_content->assign('LANG_GO', $lang['searchForm']['go']);
$box_content->assign('LANG_ADVANCED_SEARCH', $lang['searchForm']['search_advanced']);

$box_content->parse('search_form');
$box_content = $box_content->text('search_form');
?>