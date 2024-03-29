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
|	unsubscribe.inc.php
|   ========================================
|	Unsubscribe page from Bulk Email
+--------------------------------------------------------------------------
*/
if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'unsubscribe.inc.php');

$unsubscribe=new XTemplate ('content'.CC_DS.'unsubscribe.tpl');

// get all required data
if(isset($_REQUEST['email'])){

	$record['optIn1st'] = 0;

	$where = '`email` = '.$db->mySQLSafe($_REQUEST['email']);

	$update =$db->update($glob['dbprefix'].'CubeCart_customer', $record, $where);

}

$unsubscribe->assign('UNSUBSCRIBE_TITLE',$lang['unsubscribe']['unsubscribe']);

$unsubscribe->assign('TXT_ENTER_EMAIL',$lang['unsubscribe']['email']);

$unsubscribe->assign('TXT_SUBMIT',$lang['unsubscribe']['go']);

if(isset($_REQUEST['email']) && !validateEmail($_REQUEST['email'])){

	$unsubscribe->assign('VAL_ERROR',$lang['unsubscribe']['enter_valid_email']);
	$unsubscribe->parse('unsubscribe.error');
	$unsubscribe->parse('unsubscribe.form');

} elseif($update && isset($_REQUEST['email'])){

	$unsubscribe->assign('LANG_UNSUBSCRIBE_DESC',sprintf($lang['unsubscribe']['email_removed'],$_REQUEST['email']));
	$unsubscribe->parse('unsubscribe.no_error');

} elseif(!$update && isset($_REQUEST['email'])) {

	$unsubscribe->assign('VAL_ERROR',sprintf($lang['unsubscribe']['email_not_found'],$_REQUEST['email']));
	$unsubscribe->parse('unsubscribe.error');
	$unsubscribe->parse('unsubscribe.form');

} else {
	$unsubscribe->assign('LANG_UNSUBSCRIBE_DESC',$lang['unsubscribe']['enter_email_below']);
	$unsubscribe->parse('unsubscribe.no_error');
	$unsubscribe->parse('unsubscribe.form');
}

$unsubscribe->parse('unsubscribe');
$page_content = $unsubscribe->text('unsubscribe');
?>