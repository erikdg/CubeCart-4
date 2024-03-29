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
|	changePass.inc.php
|   ========================================
|	Change the Customers Password
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'changePass.inc.php');

// send email if form is submit
if(isset($_POST['submit']) && $cc_session->ccUserData['customer_id']>0){

	$query = 'SELECT `salt` FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `customer_id`='.$db->mySQLSafe($cc_session->ccUserData['customer_id']);
	$salt = $db->select($query);

	$checkOld = $db->numrows('SELECT `customer_id` FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `customer_id`='.$db->mySQLSafe($cc_session->ccUserData['customer_id']).' AND `password` = '.$db->mySQLSafe(md5(md5($salt[0]['salt']).md5($_POST['oldPass']))));

	if (!$checkOld) {
		$errorMsg = $lang['changePass']['password_incorrect'];
	} else if (empty($_POST['newPass']) || $_POST['newPass'] != $_POST['newPassConf']) {
		$errorMsg = $lang['changePass']['conf_not_match'];
	} else {
		// update
		$salt = randomPass(6);
		$data['salt'] = "'".$salt."'";
		$data['password'] = $db->mySQLSafe(md5(md5($salt).md5($_POST['newPass'])));
		$where = 'customer_id='.$db->mySQLSafe($cc_session->ccUserData['customer_id']);
		$updatePassword = $db->update($glob['dbprefix'].'CubeCart_customer',$data, $where);

		## make email
		require('classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php');

		$lang = getLang('email.inc.php');

		$mail = new htmlMimeMail();

		$macroArray = array(
			'CUSTOMER_NAME' => sanitizeVar($cc_session->ccUserData['firstName'].' '.$cc_session->ccUserData['lastName']),
			'STORE_URL' => $GLOBALS['storeURL'],
			'SENDER_IP' => get_ip_address()
		);

		$text = macroSub($lang['email']['profile_mofified_body'], $macroArray);
		unset($macroArray);

		$mail->setText($text);
		$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
		$mail->setReturnPath($config['masterEmail']);
		$mail->setSubject($lang['email']['profile_mofified_subject']);
		$mail->setHeader('X-Mailer', 'CubeCart Mailer');
		$send = $mail->send(array(sanitizeVar($cc_session->ccUserData['email'])), $config['mailMethod']);

	}

}

$change_pass = new XTemplate ('content'.CC_DS.'changePass.tpl');

	$change_pass->assign('LANG_CHANGE_PASS_TITLE',$lang['changePass']['change_pass']);

	if(!isset($_POST['submit'])) {

		$change_pass->assign('LANG_PASS_DESC',$lang['changePass']['change_pass_below']);
		$change_pass->parse('change_pass.session_true.no_error');

	} elseif(isset($errorMsg)){

		$change_pass->assign('VAL_ERROR',$errorMsg);
		$change_pass->parse('change_pass.session_true.error');

	} else {

		$change_pass->assign('LANG_PASS_DESC',$lang['changePass']['password_updated']);
		$change_pass->parse('change_pass.session_true.no_error');
		$change_pass->parse('change_pass.session_true');

	}

	if($cc_session->ccUserData['customer_id']>0 && !$updatePassword) {

		$change_pass->assign('TXT_OLD_PASS',$lang['changePass']['old_pass']);

		$change_pass->assign('TXT_NEW_PASS',$lang['changePass']['new_pass']);

		$change_pass->assign('TXT_NEW_PASS_CONF',$lang['changePass']['confirm_pass']);

		$change_pass->assign('TXT_SUBMIT',$lang['changePass']['submit']);

		$change_pass->parse('change_pass.session_true.form');
		$change_pass->parse('change_pass.session_true');

	} elseif(!$updatePassword) {

		$lang = getLang('includes'.CC_DS.'content'.CC_DS.'account.inc.php');
		$change_pass->assign('LANG_LOGIN_REQUIRED',$lang['account']['login_to_view']);
		$change_pass->parse('change_pass.session_false');

	}

	$change_pass->parse('change_pass');
$page_content = $change_pass->text('change_pass');
?>