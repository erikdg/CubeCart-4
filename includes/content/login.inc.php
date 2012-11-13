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
|	login.inc.php
|   ========================================
|	Assign customer id to session
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'login.inc.php');

$_GET['_a'] = sanitizeVar($_GET['_a']);


if ($_GET['_a'] == 'login' && isset($_POST['username']) && isset($_POST['password'])) {
	$remember = (!empty($_POST['remember'])) ? true : false;
	$cc_session->authenticate($_POST['username'],$_POST['password'], $remember);

}


$login = new XTemplate ('content'.CC_DS.'login.tpl');

$login->assign('LANG_LOGIN_TITLE',$lang['login']['login']);

$login->assign('VAL_SELF',urlencode(sanitizeVar($_GET['redir'])));
$login->assign('LANG_USERNAME',$lang['login']['username']);

if(isset($_POST['username'])){
	$login->assign('VAL_USERNAME', sanitizeVar($_POST['username']));
}

$login->assign('LANG_PASSWORD',$lang['login']['password']);
$login->assign('LANG_REMEMBER',$lang['login']['remember_me']);
$login->assign('TXT_LOGIN',$lang['login']['login']);
$login->assign('LANG_FORGOT_PASS',$lang['login']['forgot_pass']);
$login->assign('LANG_REGISTER',$lang['login']['register']);

if(isset($_POST['remember']) && $_POST['remember']==1) $login->assign('CHECKBOX_STATUS',"checked='checked'");

if($cc_session->ccUserData['customer_id'] > 0  && $cc_session->ccUserData['type']==1 &&  isset($_POST['submit'])){
	$login->assign('LOGIN_STATUS',$lang['login']['login_success']);
} elseif($cc_session->ccUserData['customer_id']>0 && $cc_session->ccUserData['type']==1 && !isset($_POST['submit'])) {
	$login->assign('LOGIN_STATUS',$lang['login']['already_logged_in']);
} elseif($cc_session->ccUserData['customer_id'] == 0 && isset($_POST['submit'])) {
	if($cc_session->ccUserBlocked){
		$login->assign('LOGIN_STATUS',sprintf($lang['login']['blocked'],sprintf('%.0f',$ini['bftime']/60)));
	} else{
		$login->assign('LOGIN_STATUS',$lang['login']['login_failed']);
	}
	$login->parse('login.form');
} else {
	$login->assign('LOGIN_STATUS',$lang['login']['login_below']);
	$login->parse('login.form');
}
$login->parse('login');
$page_content = $login->text('login');
?>