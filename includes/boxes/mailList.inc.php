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
|	mailingList.inc.php
|   ========================================
|	Mailing List Box
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

// include lang file
$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'mailList.inc.php');

$box_content = new XTemplate('boxes'.CC_DS.'mailList.tpl');

$box_content->assign('LANG_MAIL_LIST_TITLE',$lang['mailList']['mailing_list']);
$box_content->assign('FORM_METHOD',currentPage());
$box_content->assign('LANG_MAIL_LIST_DESC',$lang['mailList']['subscribe_below']);
$box_content->assign('LANG_EMAIL',$lang['mailList']['email']);
$box_content->assign('LANG_EMAIL_ADDRESS',$lang['mailList']['email_address']);
$box_content->assign('LANG_GO',$lang['mailList']['join_now']);

if (isset($_POST['act']) && $_POST['act'] == 'mailList') {
	## see if email is already subscribed
	## if already in db change status

	$email = $db->select('SELECT `email`, `optIn1st` FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `email` = '.$db->mySQLSafe($_POST['email']));
	## if not in database insert it

	if ($email && $email[0]['optIn1st']) {
		$box_content->assign('LANG_MAIL_LIST_DESC', sprintf($lang['mailList']['already_subscribed'], $_POST['email']));
	} else if (!validateEmail($_POST['email'])) {
		$box_content->assign('LANG_MAIL_LIST_DESC', $lang['mailList']['enter_valid_email']);
		$box_content->parse('mail_list.form');
	} else if (!$email) {
		## insert
		$record['optIn1st']		= 1;
		$record['ipAddress']	= $db->mySQLSafe(get_ip_address());
		$record['email']		= $db->mySQLSafe($_POST['email']);
		$record['type']			= 0;
		$record['regTime']		= $db->mySQLSafe(time());

		$insert = $db->insert($glob['dbprefix'].'CubeCart_customer', $record);
		$box_content->assign('LANG_MAIL_LIST_DESC', sprintf($lang['mailList']['added_to_mail'], $_POST['email']));
	} else {
		## subscribe them again
		$record['optIn1st'] = 1;

		$where = '`email` = '.$db->mySQLSafe(strip_tags($_POST['email']));
		$update = $db->update($glob['dbprefix'].'CubeCart_customer', $record, $where);
		$box_content->assign('LANG_MAIL_LIST_DESC', sprintf($lang['mailList']['subscribed_to_mail'], $_POST['email']));
	}
} else {
	$box_content->parse('mail_list.form');
}

$box_content->parse('mail_list');
$box_content = $box_content->text('mail_list');
?>