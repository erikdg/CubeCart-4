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
|	profile.inc.php
|   ========================================
|	Customers Profile
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')) die("Access Denied");

## include lang file
$lang1 = getLang('includes'.CC_DS.'content'.CC_DS.'reg.inc.php');
$lang2 = getLang('includes'.CC_DS.'content'.CC_DS.'profile.inc.php');

$lang = array_merge($lang1, $lang2);

## send email if form is submit
if (isset($_POST['submit']) && $cc_session->ccUserData['customer_id']>0) {

	if ($_POST['which_field']=='T'){
		$county = $_POST['county'];
	} elseif ($_POST['which_field']=='S') {
		$county = $_POST['county_sel'];
	}

	if ($_POST['email']!=$cc_session->ccUserData['email']) {
		$emailArray = $db->select('SELECT `customer_id`, `type` FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `email`='.$db->mySQLSafe($_POST['email']));
	}

	if (empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['add_1']) || empty($_POST['town']) || empty($county) || empty($_POST['postcode']) || empty($_POST['country'])) {
		$errorMsg = $lang['profile']['complete_all'];
	} else if (!validateEmail($_POST['email'])) {
		$errorMsg = $lang['profile']['email_invalid'];
	} else if(!preg_match('#^([0-9-\s]+)$#',$_POST['phone'])) {
		$errorMsg = $lang['profile']['enter_valid_tel'];
	} else if(!empty($_POST['mobile']) && !preg_match('#^([0-9-\s]+)$#', $_POST['mobile'])) {
		$errorMsg = $lang['profile']['enter_valid_tel'];
	} else if(isset($emailArray) && $emailArray && $emailArray[0]['type'] == 1) {
		$errorMsg = $lang['profile']['email_inuse'];
	} else {
		## update database
		$data['title'] = $db->mySQLSafe($_POST['title']);
		$data['firstName'] = $db->mySQLSafe($_POST['firstName']);
		$data['lastName'] = $db->mySQLSafe($_POST['lastName']);
		$data['email'] = $db->mySQLSafe($_POST['email']);
		$data['companyName'] = $db->mySQLSafe($_POST['companyName']);
		$data['add_1'] = $db->mySQLSafe($_POST['add_1']);
		$data['add_2'] = $db->mySQLSafe($_POST['add_2']);
		$data['town'] = $db->mySQLSafe($_POST['town']);
		$data['county'] = $db->mySQLSafe($county);
		$data['postcode'] = $db->mySQLSafe($_POST['postcode']);
		$data['country'] = $db->mySQLSafe($_POST['country']);
		$data['phone'] = $db->mySQLSafe($_POST['phone']);
		$data['mobile'] = $db->mySQLSafe($_POST['mobile']);

		$where = '`customer_id` = '.$cc_session->ccUserData['customer_id'];
		$updateAcc = $db->update($glob['dbprefix'].'CubeCart_customer',$data,$where);

		## make email
		require('classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php');

		$lang = getLang('email.inc.php');

		$mail = new htmlMimeMail();

		$macroArray = array(
			'CUSTOMER_NAME' => sanitizeVar($_POST['firstName'].' '.$_POST['lastName']),
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
		$send = $mail->send(array(sanitizeVar($_POST['email'])), $config['mailMethod']);

		$getF = sanitizeVar($_GET['f']); // fixes Fatal error: Can't use function return value in write context

		if(!empty($getF)) {
			httpredir('index.php?_g=co&_a='.sanitizeVar($_GET['f']));
		}

		## rebuild customer array
		$query	= "SELECT * FROM ".$glob['dbprefix']."CubeCart_sessions INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_sessions.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE sessId = '".$GLOBALS[CC_SESSION_NAME]."'";
		$result	= $db->select($query);
		$cc_session->ccUserData = $result[0];
	}
}

$profile = new XTemplate ('content'.CC_DS.'profile.tpl');

$profile->assign('LANG_PERSONAL_INFO_TITLE',$lang['profile']['personal_info']);

if($_GET['ppupdate']) {
	$_GET['f'] 	= 'step1';
	$errorMsg 	= $lang['profile']['incomplete_ppdetails'];
}

if (isset($updateAcc) && $updateAcc) {
	$profile->assign('LANG_PROFILE_DESC',$lang['profile']['account_updated']);
	$profile->parse('profile.session_true.no_error');

} else if(isset($errorMsg)) {
	$profile->assign('VAL_ERROR',$errorMsg);
	$profile->parse('profile.session_true.error');
} else {
	$profile->assign('LANG_PROFILE_DESC',$lang['profile']['edit_below']);
	$profile->parse('profile.session_true.no_error');
}

if ($cc_session->ccUserData['customer_id'] > 0 || $cc_session->ccUserData[0]['customer_id'] > 0) {
	if(isset($_GET['f']) && !empty($_GET['f'])) {
		$profile->assign('VAL_EXTRA_GET','&amp;f='.sanitizeVar($_GET['f']));
	}

	$profile->assign('TXT_TITLE',$lang['profile']['title']);
	$profile->assign('VAL_TITLE',$cc_session->ccUserData['title']);

	$profile->assign('LANG_TITLE_DESC',$lang['reg']['title_desc']);

	$profile->assign('TXT_FIRST_NAME',$lang['profile']['first_name']);
	$profile->assign('VAL_FIRST_NAME',$cc_session->ccUserData['firstName']);

	$profile->assign('TXT_LAST_NAME',$lang['profile']['last_name']);
	$profile->assign('VAL_LAST_NAME',$cc_session->ccUserData['lastName']);

	$profile->assign('TXT_COMPANY_NAME',$lang['profile']['company_name']);
	$profile->assign('VAL_COMPANY_NAME',$cc_session->ccUserData['companyName']);

	$profile->assign('TXT_EMAIL',$lang['profile']['email']);
	$profile->assign('VAL_EMAIL',$cc_session->ccUserData['email']);

	$profile->assign('TXT_ADD_1',$lang['profile']['address']);
	$profile->assign('VAL_ADD_1',$cc_session->ccUserData['add_1']);

	$profile->assign('TXT_ADD_2','');
	$profile->assign('VAL_ADD_2',$cc_session->ccUserData['add_2']);

	$profile->assign('TXT_TOWN',$lang['profile']['town']);
	$profile->assign('VAL_TOWN',$cc_session->ccUserData['town']);

	$profile->assign('TXT_COUNTY',$lang['profile']['county']);
	/*
	$profile->assign('VAL_COUNTY',$cc_session->ccUserData['county']);
	*/

	$profile->assign('TXT_POSTCODE',$lang['profile']['postcode']);
	$profile->assign('VAL_POSTCODE',$cc_session->ccUserData['postcode']);

	$profile->assign('TXT_COUNTRY',$lang['profile']['country']);


	$jsScript = jsGeoLocationExtended('country', 'county_sel', $lang['cart']['na'],'divCountySelect','divCountyText','county','which_field');

	$counties = $db->select("SELECT `name` FROM  ".$glob['dbprefix']."CubeCart_iso_counties WHERE `countryId` = '".$cc_session->ccUserData['country']."';");

	$profile->assign('VAL_DEL_COUNTY',$cc_session->ccUserData['county']);

	if (is_array($counties)) {
		$profile->assign('VAL_COUNTY_SEL_STYLE', "style='display:block;'");
		$profile->assign('VAL_COUNTY_TXT_STYLE', "style='display:none;'");
		$profile->assign('VAL_COUNTY_WHICH_FIELD', 'S');
	} else {
		$profile->assign('VAL_COUNTY_SEL_STYLE', "style='display:none;'");
		$profile->assign('VAL_COUNTY_TXT_STYLE', "style='display:block;'");
		$profile->assign('VAL_COUNTY_WHICH_FIELD', "T");
	}
	$profile->assign('JS_COUNTY_OPTIONS', '<script type="text/javascript">'.$jsScript."</script>");

	for($i = 0, $maxi = count($counties); $i < $maxi; ++$i) {


		if (strtolower($counties[$i]['name']) == strtolower($cc_session->ccUserData['county'])) {
			$profile->assign('COUNTY_SELECTED',"selected='selected'");
		} else {
			$profile->assign('COUNTY_SELECTED','');
		}

		$profile->assign('VAL_DEL_COUNTY_ID',$counties[$i]['name']);

		$countyName = $counties[$i]['name'];

		if (strlen($countyName)>20) {
			$countyName = substr($countyName,0,20).'&hellip;';
		}

		$profile->assign('VAL_DEL_COUNTY_NAME',$countyName);
		$profile->parse('profile.session_true.county_opts');
	}
	// end: Flexible Taxes

	$cache = new cache('glob.countries');
	$countries = $cache->readCache();

	if (!$cache->cacheStatus) {
		$countries = $db->select('SELECT `id`, `printable_name` FROM '.$glob['dbprefix'].'CubeCart_iso_countries ORDER BY `printable_name`');
		$cache->writeCache($countries);
	}

	for ($i = 0, $maxi = count($countries); $i < $maxi; ++$i) {
		if ($countries[$i]['id'] == $cc_session->ccUserData['country']) {
			$profile->assign('COUNTRY_SELECTED',"selected='selected'");
		} else {
			$profile->assign('COUNTRY_SELECTED','');
		}

		$profile->assign('VAL_COUNTRY_ID',$countries[$i]['id']);

		$countryName = '';
		$countryName = $countries[$i]['printable_name'];

		if (strlen($countryName)>20) {
			$countryName = substr($countryName,0,20).'&hellip;';
		}

		$profile->assign('VAL_COUNTRY_NAME',$countryName);
		$profile->parse('profile.session_true.country_opts');

	}

	$profile->assign('VAL_COUNTRY',$cc_session->ccUserData['country']);

	$profile->assign('TXT_PHONE',$lang['profile']['phone']);
	$profile->assign('VAL_PHONE',$cc_session->ccUserData['phone']);

	$profile->assign('TXT_MOBILE',$lang['profile']['mobile']);
	$profile->assign('VAL_MOBILE',$cc_session->ccUserData['mobile']);

	$profile->assign('TXT_SUBMIT',$lang['profile']['update_account']);

	$profile->parse('profile.session_true');

} else {
	$profile->assign('LANG_LOGIN_REQUIRED',$lang['profile']['login_required']);
	$profile->parse('profile.session_false');
}

$profile->parse('profile');
$page_content = $profile->text('profile');
?>