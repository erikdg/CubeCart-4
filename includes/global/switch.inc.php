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
|	switch.inc.php
|   ========================================
|	Switch between language and currency vars
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

## change language if necessary making sure it is cleaned against cross site scripting!!! Or else there'd be truble!!
if ((isset($_GET['lang'])) && (!empty($_GET['lang'])) && (isset($GLOBALS[CC_SESSION_NAME]))) {
	$sessData['lang'] = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['lang']);
	## Make sure language is actually available after it has been made safe
	$sessData['lang'] = file_exists('language'.CC_DS.$sessData['lang']) ? $sessData['lang'] : $config['defaultLang'];
	$sessData['lang'] = $db->mySQLSafe($sessData['lang']);
	$db->update($glob['dbprefix'].'CubeCart_sessions', $sessData,'sessId='.$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
	checkSpoof();
} else if ((isset($_GET['currency'])) && !empty($_GET['currency']) && (isset($GLOBALS[CC_SESSION_NAME]))) {
	$sessData['currency'] = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['currency']);
	## Make sure the currency is valid!
	$validCurrency = $db->select('SELECT `code` FROM '.$glob['dbprefix'].'CubeCart_currencies WHERE `active` = 1 AND `code` = '.$db->mySQLSafe($sessData['currency']));
	$sessData['currency'] = ($validCurrency) ? $validCurrency[0]['code'] : $config['defaultCurrency'];
	$sessData['currency'] = $db->mySQLSafe($sessData['currency']);
	$db->update($glob['dbprefix'].'CubeCart_sessions', $sessData,'sessId='.$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
	checkSpoof();
} else if ((isset($_GET['skin'])) && !empty($_GET['skin']) && (isset($GLOBALS[CC_SESSION_NAME]))) {
	$sessData['skin'] = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['skin']);
	$sessData['skin'] = file_exists('skins'.CC_DS.$sessData['skin']) ? $sessData['skin'] : $config['skinDir'];
	$sessData['skin'] = $db->mySQLSafe($sessData['skin']);
	$db->update($glob['dbprefix'].'CubeCart_sessions', $sessData,'sessId='.$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
	checkSpoof();
} else {
	httpredir('index.php');
}
?>