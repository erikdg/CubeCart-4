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
|	viewDoc.inc.php
|   ========================================
|	Displays a site document
+--------------------------------------------------------------------------
*/
// query database

if (!defined('CC_INI_SET')) die('Access Denied');

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'viewDoc.inc.php');

$_GET['docId'] = (int)sanitizeVar($_GET['docId']);

if (LANG_FOLDER != $config['defaultLang']) {
	$docresult = $db->select('SELECT `doc_name`, `doc_content` FROM '.$glob['dbprefix'].'CubeCart_docs_lang WHERE `doc_master_id` = '.$db->mySQLSafe($_GET['docId']).' AND `doc_lang` = '.$db->mySQLSafe(LANG_FOLDER));
	if ($config['seftags']) {
		$sefresult = $db->select('SELECT `doc_metatitle`, `doc_metadesc`, `doc_metakeywords` FROM '.$glob['dbprefix'].'CubeCart_docs WHERE `doc_id` = '.$db->mySQLSafe($_GET['docId']));
		$docresult['sefSiteTitle']		= $sefresult['sefSiteTitle'];
		$docresult['sefSiteDesc']		= $sefresult['sefSiteDesc'];
		$docresult['sefSiteKeywords']	= $sefresult['sefSiteKeywords'];
	}
}

if (!isset($docresult) || !$docresult) {
	if ($config['seftags'])  {
		$docresult = $db->select('SELECT `doc_metatitle`, `doc_metadesc`, `doc_metakeywords`, `doc_name`, `doc_content` FROM '.$glob['dbprefix'].'CubeCart_docs WHERE `doc_id` = '.$db->mySQLSafe($_GET['docId']));
	} else {
		$docresult = $db->select('SELECT `doc_name`, `doc_content` FROM '.$glob['dbprefix'].'CubeCart_docs WHERE `doc_id` = '.$db->mySQLSafe($_GET['docId']));
	}
}

$view_doc = new XTemplate('content'.CC_DS.'viewDoc.tpl');

if (isset($docresult) && $docresult) {

	$view_doc->assign('DOC_NAME', validHTML($docresult[0]['doc_name']));
	$view_doc->assign('DOC_CONTENT', (!get_magic_quotes_gpc ()) ? stripslashes($docresult[0]['doc_content']) : $docresult[0]['doc_content']);

	if ($config['seftags']) {
		$meta['siteTitle']			= $docresult[0]['doc_name'];
		$meta['metaDescription']	= substr(strip_tags($docresult[0]['doc_content']), 0, 35);
		$meta['sefSiteTitle']		= $docresult[0]['doc_metatitle'];
		$meta['sefSiteDesc']		= $docresult[0]['doc_metadesc'];
		$meta['sefSiteKeywords']	= $docresult[0]['doc_metakeywords'];
	} else {
		$meta['siteTitle']			= $config['siteTitle'].' - '.$docresult[0]['doc_name'];
		$meta['metaDescription']	= substr(strip_tags($docresult[0]['doc_content']), 0, 35);
	}
} else {
	$view_doc->assign('DOC_NAME', $lang['viewDoc']['error']);
	$view_doc->assign('DOC_CONTENT', $lang['viewDoc']['does_not_exist']);
}

$view_doc->parse('view_doc');
$page_content = $view_doc->text('view_doc');

?>