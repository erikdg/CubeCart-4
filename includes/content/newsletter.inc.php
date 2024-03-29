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
|	newsletter.inc.php
|   ========================================
|	Subscribe to the Newsletter
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'newsletter.inc.php');

// send email if form is submit
if(isset($_POST['submit']) && $cc_session->ccUserData['customer_id']>0){

		// update database
			$data['optIn1st'] = $db->mySQLSafe($_POST['optIn1st']);
			$data['htmlEmail'] = $db->mySQLSafe($_POST['htmlEmail']);

			$where = 'customer_id = '.$cc_session->ccUserData['customer_id'];
			$update = $db->update($glob['dbprefix'].'CubeCart_customer',$data,$where);

			// rebuild customer array
			$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_sessions INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_sessions.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE `sessId` = '".$GLOBALS[CC_SESSION_NAME]."'";
			$result = $db->select($query);
			$cc_session->ccUserData = $result[0];
}

$newsletter = new XTemplate ('content'.CC_DS.'newsletter.tpl');

	$newsletter->assign('LANG_NEWSLETTER_TITLE',$lang['newsletter']['newsletter_prefs']);

	if($update) {
		$newsletter->assign('LANG_NEWSLETTER_DESC',$lang['newsletter']['prefs_updates']);
	} else {
		$newsletter->assign('LANG_NEWSLETTER_DESC',$lang['newsletter']['edit_prefs_below']);
	}

	if($cc_session->ccUserData['customer_id']>0) {

		$newsletter->assign('TXT_SUBSCRIBED',$lang['newsletter']['subscribe']);
		$newsletter->assign('LANG_YES',$lang['front']['yes']);
		$newsletter->assign('LANG_NO',$lang['front']['no']);

		if($cc_session->ccUserData['optIn1st']==1){
			$newsletter->assign('STATE_SUBSCRIBED_YES',"checked='checked'");
			$newsletter->assign('STATE_SUBSCRIBED_NO','');
		} else {
			$newsletter->assign('STATE_SUBSCRIBED_YES','');
			$newsletter->assign('STATE_SUBSCRIBED_NO',"checked='checked'");
		}

		$newsletter->assign('TXT_EMAIL_FORMAT',$lang['newsletter']['email_format']);
		$newsletter->assign('LANG_TEXT',$lang['newsletter']['plain_text']);
		$newsletter->assign('LANG_HTML',$lang['newsletter']['html']);
		$newsletter->assign('LANG_HTML_ABBR',$lang['newsletter']['html_abbr']);

		if($cc_session->ccUserData['htmlEmail']==1){
			$newsletter->assign('STATE_HTML_TEXT','');
			$newsletter->assign('STATE_HTML_HTML',"checked='checked'");
		} else {
			$newsletter->assign('STATE_HTML_TEXT',"checked='checked'");
			$newsletter->assign('STATE_HTML_HTML','');
		}

		$newsletter->assign('TXT_SUBMIT',$lang['newsletter']['update']);

		$newsletter->parse('newsletter.session_true');

	} else {
		$newsletter->assign('LANG_LOGIN_REQUIRED',$lang['newsletter']['login_required']);
		$newsletter->parse('newsletter.session_false');

	}

	$newsletter->parse('newsletter');
$page_content = $newsletter->text('newsletter');
?>