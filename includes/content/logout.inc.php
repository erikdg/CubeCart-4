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
|	logout.inc.php
|   ========================================
|	Remove customer id from session
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

## include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'logout.inc.php');

## delete cookie
$logout = new XTemplate ('content'.CC_DS.'logout.tpl');

$logout->assign('LANG_LOGOUT_TITLE',$lang['logout']['logout']);

if($cc_session->ccUserData['customer_id']>0){
	$cc_session->destroySession($GLOBALS[CC_SESSION_NAME]);
	## lose any session data that may be lingering e.g. PayPal Express Checkout
	session_unset();
	$logout->assign('LANG_LOGOUT_STATUS',$lang['logout']['session_destroyed']);

} else {
	$logout->assign('LANG_LOGOUT_STATUS',$lang['logout']['no_session']);
}

$logout->parse('logout');
$page_content = $logout->text('logout');
?>