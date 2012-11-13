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
|	error.inc.php
|   ========================================
|	Display error message from language file
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$lang = getLang('includes'.CC_DS.'content'.CC_DS.'error.inc.php');
$error = new XTemplate ('content'.CC_DS.'error.tpl');

$error->assign('LANG_SORRY', sprintf($lang['error']['error'], sanitizeVar($_GET['code'])));

if (array_key_exists($_GET['code'], $lang['error'])) {
	$error->assign('LANG_DESC', $lang['error'][$_GET['code']]);
} else {
	$error->assign('LANG_DESC', $lang['error']['no_error_msg']);
}

$error->parse('error');
$page_content = $error->text('error');
?>