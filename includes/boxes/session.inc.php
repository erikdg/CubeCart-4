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
|	session.inc.php
|   ========================================
|	Session Links & Welcome Text
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

if (!$cc_session->user_is_search_engine() || !$config['sef'])  {

	## include lang file
	$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'session.inc.php');
	$box_content = new XTemplate ('boxes'.CC_DS.'session.tpl');

	## build attributes
	if ($cc_session->ccUserData['customer_id']>0 && $cc_session->ccUserData['type']==1 && $_GET['_a'] != 'logout') {

		$box_content->assign('LANG_WELCOME_BACK', $lang['session']['welcome_back']);

		$box_content->assign('TXT_USERNAME', $cc_session->ccUserData['firstName'].' '.$cc_session->ccUserData['lastName']);
		$box_content->assign('LANG_LOGOUT', $lang['session']['logout']);
		$box_content->assign('LANG_YOUR_ACCOUNT', $lang['session']['your_account']);
		$box_content->parse('session.session_true');
	} else {
		$box_content->assign('LANG_WELCOME_GUEST', $lang['session']['welcome_guest']);
		$box_content->assign('VAL_SELF', urlencode(str_replace('&amp;','&',currentPage())));
		$box_content->assign('LANG_LOGIN', $lang['session']['login']);
		$box_content->assign('LANG_REGISTER', $lang['session']['register']);
		$box_content->parse('session.session_false');
	}

	$box_content->parse('session');
	$box_content = $box_content->text('session');

} else {
	$box_content = null;
}
?>