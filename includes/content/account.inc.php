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
|	account.inc.php
|   ========================================
|	Customers Account Homepage
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'account.inc.php');

$account = new XTemplate ('content'.CC_DS.'account.tpl');

$account->assign('LANG_YOUR_ACCOUNT', $lang['account']['your_account']);
$account->assign('TXT_PERSONAL_INFO', $lang['account']['personal_info']);
$account->assign('TXT_ORDER_HISTORY', $lang['account']['order_history']);
$account->assign('TXT_CHANGE_PASSWORD', $lang['account']['change_password']);
$account->assign('TXT_NEWSLETTER', $lang['account']['newsletter']);
$account->assign('LANG_LOGIN_REQUIRED', $lang['account']['login_to_view']);

if ($cc_session->ccUserData['customer_id']>0) {
	$account->parse('account.session_true');
} else {
	$account->parse('account.session_false');
}

$account->parse('account');
$page_content = $account->text('account');
?>