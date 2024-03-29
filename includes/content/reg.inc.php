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
|	reg.inc.php
|   ========================================
|	Customer Registration
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'reg.inc.php');

if($cc_session->ccUserData['customer_id']>0 && $cc_session->ccUserData['type']==1) {
	httpredir('index.php?_g=co&_a=step1');
}
if(isset($_POST['email'])) {

	if ($_POST['which_field']=='T'){
		$county = $_POST['county'];
	} elseif ($_POST['which_field']=='S') {
		$county = $_POST['county_sel'];
	}

#	if (!isset($_POST['skipReg'])) {
#		$emailArray = $db->select("SELECT customer_id, type FROM ".$glob['dbprefix']."CubeCart_customer WHERE email=".$db->mySQLSafe($_POST['email']));
#	}
	$emailArray = $db->select('SELECT `customer_id`, `type` FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `email`='.$db->mySQLSafe($_POST['email']));

	if($config['floodControl']=='recaptcha') {
		$response = recaptcha_check_answer(	$ini['recaptcha_private_key'],
											$_SERVER['REMOTE_ADDR'],
											$_POST['recaptcha_challenge_field'],
											$_POST['recaptcha_response_field']);
	} elseif($config['floodControl']==1) {
		$spamCode = fetchSpamCode($_POST['ESC'], true);
	}

	if ($_POST['skipReg']==1 && (empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['add_1']) || empty($_POST['town']) || empty($county) || empty($_POST['postcode']) || empty($_POST['country']))) {

		$errorMsg = $lang['reg']['fill_required'];

	} elseif(!isset($_POST['skipReg']) && (empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['add_1']) || empty($_POST['town']) || empty($county) || empty($_POST['postcode']) || empty($_POST['country']) || empty($_POST['password']) || empty($_POST['passwordConf']))) {

		$errorMsg = $lang['reg']['fill_required'];

	} elseif(!isset($_POST['skipReg']) && ($_POST['password'] != $_POST['passwordConf'])) {

		$errorMsg = $lang['reg']['pass_not_match'];

	} elseif(!validateEmail($_POST['email'])) {

		$errorMsg = $lang['reg']['enter_valid_email'];

	} elseif(!preg_match('#^([0-9\-\s\+\.\(\)]+)$#', $_POST['phone'])) {

		$errorMsg = $lang['reg']['enter_valid_tel'];

	} elseif(!empty($_POST['mobile']) && !preg_match('#^([0-9-\s]+)$#', $_POST['mobile'])) {

		$errorMsg = $lang['reg']['enter_valid_tel'];

	} elseif($emailArray && $emailArray[0]['type']==1) {

		$errorMsg = $lang['reg']['email_in_use'];
	} elseif($config['floodControl']=='recaptcha' && !$response->is_valid) {
		$errorMsg = $lang['reg']['error_code'];
	} elseif($config['floodControl']==1 && (!isset($_POST['spamcode']) || ($spamCode['SpamCode']!=strtoupper($_POST['spamcode'])) || (get_ip_address()!=$spamCode['userIp']))) {
		$errorMsg = $lang['reg']['error_code'];
	} else if(!isset($_POST['tandc'])) {
		$errorMsg = $lang['reg']['tandc'];
	} else {



		$record['email']		= $db->mySQLSafe($_POST['email']);
		$record['title']		= $db->mySQLSafe($_POST['title']);
		$record['firstName']	= $db->mySQLSafe($_POST['firstName']);
		$record['lastName']		= $db->mySQLSafe($_POST['lastName']);
		$record['companyName']	= $db->mySQLSafe($_POST['companyName']);
		$record['add_1']		= $db->mySQLSafe($_POST['add_1']);
		$record['add_2']		= $db->mySQLSafe($_POST['add_2']);
		$record['town']			= $db->mySQLSafe($_POST['town']);
		$record['county']		= $db->mySQLSafe($county);
		$record['postcode']		= $db->mySQLSafe($_POST['postcode']);
		$record['country']		= $db->mySQLSafe($_POST['country']);
		$record['phone']		= $db->mySQLSafe($_POST['phone']);
		$record['mobile']		= $db->mySQLSafe($_POST['mobile']);
		$record['regTime']		= $db->mySQLSafe(time());
		$record['ipAddress']	= $db->mySQLSafe(get_ip_address());

		if(isset($_POST['optIn1st'])){

			$record['optIn1st'] = $db->mySQLSafe($_POST['optIn1st']);

		}

		$salt = randomPass(6);
		$record['salt'] = "'".$salt."'";

		// they don't want to register (Ghost Registration)
		if ($_POST['skipReg']==1) {
			$randomPass = randomPass(10);
			$record['type'] = 2;
			$record['password'] = $db->mySQLSafe(md5(md5($salt).md5($randomPass)));
		} else {
			$record['type'] = 1;
			$record['password'] = $db->mySQLSafe(md5(md5($salt).md5($_POST['password'])));
		}
		$record['htmlEmail'] = $db->mySQLSafe($_POST['htmlEmail']);

		if ($emailArray && $emailArray['type'] == 0) {
		// update

			$where = '`customer_id` = '.$db->mySQLSafe($emailArray[0]['customer_id']);

			$update = $db->update($glob['dbprefix'].'CubeCart_customer', $record, $where);

			$sessData['customer_id'] = $emailArray[0]['customer_id'];
			$update = $db->update($glob['dbprefix'].'CubeCart_sessions', $sessData,'`sessId`='.$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));

			$redir = sanitizeVar(urldecode($_GET['redir']));

			## prevent phishing attacks
			if (preg_match('/^http(s?):\/\//i', $redir) && !preg_match('@^'.$glob['storeURL'].'|^'.$config['storeURL_SSL'].'@i', $redir)) {
				$redir = 'index.php';
			}

		} else {

			$insert = $db->insert($glob['dbprefix'].'CubeCart_customer', $record);

			## send welcome email
			if (!$_POST['skipReg']) {
				require_once 'classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php';

				$lang = getLang('email.inc.php');
				$mail = new htmlMimeMail();

				$macroArray = array(
					'CUSTOMER_NAME' => sanitizeVar($_POST['firstName']).' '.sanitizeVar($_POST['lastName']),
					'EMAIL'			=> sanitizeVar($_POST['email']),
					'PASSWORD'		=> sanitizeVar($_POST['password']),
					'STORE_URL'		=> $GLOBALS['storeURL'],
					'SENDER_IP'		=> get_ip_address()
				);

				$text = macroSub($lang['email']['new_reg_body'],$macroArray);
				unset($macroArray);

				$mail->setText($text);
				$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
				$mail->setReturnPath($config['masterEmail']);
				$mail->setSubject($lang['email']['new_reg_subject']);
				$mail->setHeader('X-Mailer', 'CubeCart Mailer');
				$mail->send(array(sanitizeVar($_POST['email'])), $config['mailMethod']);
			}

			$sessData['customer_id'] = $db->insertid();
			$update = $db->update($glob['dbprefix'].'CubeCart_sessions', $sessData,'`sessId` = '.$db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));

			$redir = sanitizeVar(urldecode($_GET['redir']));

			## prevent phishing attacks
			if (preg_match('/^http(s?):\/\//i', $redir) && !preg_match('@^'.$glob['storeURL'].'|^'.$config['storeURL_SSL'].'@i', $redir)) {
				die('Redirect URL not allowed!');
			}
		}

		require_once('classes'.CC_DS.'cart'.CC_DS.'shoppingCart.php');
		$cart	= new cart();
		$basket	= $cart->cartContents($cc_session->ccUserData['basket']);

		if (is_array($basket['conts']) && !empty($basket['conts'])) {
			httpredir('index.php?_g=co&_a=step1');
		} else if (isset($_GET['redir']) && !empty($_GET['redir']) && !preg_match('/logout|login|forgotPass|changePass/i', $redir)) {

			httpredir($redir);
		} else {
			httpredir('index.php');
		}
	}
}

$reg = new XTemplate ('content'.CC_DS.'reg.tpl');

	if (isset($_GET['co'])) {
		$reg->assign('LANG_CART',$lang['reg']['cart']);
		$reg->assign('LANG_CHECKOUT',$lang['reg']['checkout']);
		$reg->assign('LANG_PAYMENT',$lang['reg']['payment']);
		$reg->assign('LANG_COMPLETE',$lang['reg']['complete']);
		$reg->parse('reg.checkout_progress');
	}

	if (isset($errorMsg)) {
		$reg->assign('VAL_ERROR',$errorMsg);
		$reg->parse('reg.error');
	} else {
		$reg->assign('LANG_REGISTER_DESC',$lang['reg']['note_required']);
		$reg->parse('reg.no_error');
	}

	$reg->assign('ICON_REQUIRED',$lang['reg']['required_asterix']);
	$reg->assign('ICON_REQUIRED_SECURITY',$lang['reg']['required_asterix']);

	// REGISTER ONLY
	if (!isset($_GET['co'])) {
		$reg->assign('LANG_REGISTER',$lang['reg']['express_reg']);
		$reg->assign('LANG_SECURITY_DETAILS',$lang['reg']['security_details']);
		$reg->assign('VAL_SKIP_REG_CHECKED','');
		$reg->assign('VAL_PASS_CLASS','textbox');
	} else {
		$reg->assign('LANG_REGISTER',$lang['reg']['express_reg_co']);
		$reg->assign('LANG_SECURITY_DETAILS',$lang['reg']['security_details_co']);
		$reg->assign('LANG_NO_ACCOUNT_WANTED',$lang['reg']['security_details_optout_co']);

		if ($_GET['co'] && isset($_POST['skipReg'])	) {
			$reg->assign('VAL_SKIP_REG_CHECKED','checked="checked"');
			$reg->assign('VAL_PASS_CLASS','textboxDisabled');
			$reg->assign('VAL_PASS_MODE','disabled="disabled"');
			$reg->assign('VAL_PASS_HIDE_REQUIRED','style="visibility: hidden"');
		} else {
			$reg->assign('VAL_SKIP_REG_CHECKED','');
			$reg->assign('VAL_PASS_CLASS','textbox');
			$reg->assign('VAL_PASS_MODE','');
			$reg->assign('VAL_PASS_HIDE_REQUIRED','style="visibility: visible"');
		}

		$reg->parse('reg.account_opt');
	}
	$reg->assign('LANG_REGISTER_SUBMIT',$lang['reg']['submit_and_cont']);

	if(isset($_GET['redir']) && !empty($_GET['redir'])) {
		$formAction = 'index.php?_g=co&amp;_a=reg&amp;redir='.sanitizeVar(urlencode($_GET['redir']));
	} else {
		$formAction = 'index.php?_g=co&amp;_a=reg';
	}

	if (isset($_GET['co'])) {
		$formAction .= '&amp;co=1';
	}

	$reg->assign('VAL_ACTION',$formAction);

	$reg->assign('LANG_PERSONAL_DETAILS',$lang['reg']['personal_details']);
	$reg->assign('LANG_ADDRESS',$lang['reg']['address']);
	$reg->assign('LANG_TITLE',$lang['reg']['title']);
	$reg->assign('LANG_TITLE_DESC',$lang['reg']['title_desc']);
	$reg->assign('LANG_FIRST_NAME',$lang['reg']['first_name']);
	$reg->assign('LANG_ADDRESS_FORM',$lang['reg']['address2']);
	$reg->assign('LANG_COMPANY_NAME',$lang['reg']['company_name']);
	$reg->assign('LANG_LAST_NAME',$lang['reg']['last_name']);
	$reg->assign('LANG_EMAIL_ADDRESS',$lang['reg']['email_address']);
	$reg->assign('LANG_TOWN',$lang['reg']['town']);
	$reg->assign('LANG_TELEPHONE',$lang['reg']['phone']);
	$reg->assign('LANG_COUNTY',$lang['reg']['county']);
	$reg->assign('LANG_MOBILE',$lang['reg']['mobile']);
	$reg->assign('LANG_COUNTRY',$lang['reg']['country']);
	$reg->assign('LANG_POSTCODE',$lang['reg']['postcode']);
	$reg->assign('LANG_CHOOSE_PASSWORD',$lang['reg']['choose_pass']);
	$reg->assign('LANG_CONFIRM_PASSWORD',$lang['reg']['conf_pass']);
	$reg->assign('LANG_PRIVACY_SETTINGS',$lang['reg']['privacy_settings']);
	$reg->assign('LANG_RECIEVE_EMAILS',$lang['reg']['receive_emails']);
	$reg->assign('LANG_EMAIL_FORMAT',$lang['reg']['email_format']);
	$reg->assign('LANG_HTML_FORMAT',$lang['reg']['styled_html']);
	$reg->assign('LANG_PLAIN_TEXT',$lang['reg']['plain_text']);

	$sql = sprintf("SELECT `doc_id` FROM %sCubeCart_docs WHERE `doc_terms` = '1'", $glob['dbprefix']);
	$docs = $db->select($sql, 1);

	$reg->assign('LINK_TANDCS', sprintf('index.php?_a=viewDoc&amp;docId=%d', $docs[0]['doc_id']));
	$reg->assign('LANG_TANDCS', $lang['reg']['tandcs']);

	$reg->assign('LANG_PLEASE_READ', $lang['reg']['please_read']);


	// start: Flexible Taxes, by Estelle Winterflood
	// counties selector
	$jsScript = jsGeoLocationExtended('country', 'county_sel', $lang['cart']['na'],'divCountySelect','divCountyText','county','which_field');
	if(isset($_POST['country']) && $_POST['country']>0) {
		$counties = $db->select('SELECT `name` FROM  '.$glob['dbprefix'].'CubeCart_iso_counties WHERE `countryId` = '.$db->mySQLSafe($_POST['country']).' ORDER BY `name` ASC;');
	} else {
		$counties = $db->select("SELECT `name` FROM  ".$glob['dbprefix']."CubeCart_iso_counties WHERE `countryId` = '".$config['siteCountry']."' ORDER BY `name` ASC;");
	}

	$reg->assign('VAL_DEL_COUNTY', sanitizeVar($_POST['county']));

	if (is_array($counties)) {
		$reg->assign('VAL_COUNTY_SEL_STYLE', 'style="display:block;"');
		$reg->assign('VAL_COUNTY_TXT_STYLE', 'style="display:none;"');
		$reg->assign('VAL_COUNTY_WHICH_FIELD', 'S');
	} else {
		$reg->assign('VAL_COUNTY_SEL_STYLE', 'style="display:none;"');
		$reg->assign('VAL_COUNTY_TXT_STYLE', 'style="display:block;"');
		$reg->assign('VAL_COUNTY_WHICH_FIELD', 'T');
	}
	$reg->assign('JS_COUNTY_OPTIONS', '<script type="text/javascript">'.$jsScript.'</script>');

	for($i = 0, $maxi = count($counties); $i < $maxi; ++$i) {

		$reg->assign('VAL_DEL_COUNTY_ID',$counties[$i]['name']);

		if($county==$counties[$i]['name']){
			$reg->assign('COUNTY_SELECTED','selected="selected"');
		} else {
			$reg->assign('COUNTY_SELECTED','');
		}

		$countyName = $counties[$i]['name'];

		if(strlen($countyName)>20) {
			$countyName = substr($countyName,0,15).'&hellip;';
		}

		$reg->assign('VAL_DEL_COUNTY_NAME',$countyName);
		$reg->parse('reg.county_opts');
	}
	// end: Flexible Taxes

	$cache = new cache('glob.countries');
	$countries = $cache->readCache();

	if (!$cache->cacheStatus) {
		$countries = $db->select("SELECT `id`, `printable_name` FROM ".$glob['dbprefix']."CubeCart_iso_countries ORDER BY `printable_name` ASC");
		$cache->writeCache($countries);
	}

	for($i= 0, $maxi = count($countries); $i < $maxi; ++$i){

		$reg->assign('VAL_COUNTRY_ID',$countries[$i]['id']);

		$countryName = '';
		$countryName = $countries[$i]['printable_name'];

		if(strlen($countryName)>20){

		$countryName = substr($countryName,0,20).'&hellip;';

		}

		$reg->assign('VAL_COUNTRY_NAME',$countryName);

		if(isset($_POST['country']) && $_POST['country'] == $countries[$i]['id']){

			$reg->assign('VAL_COUNTRY_SELECTED','selected="selected"');

		} elseif(!isset($_POST['country']) && ($countries[$i]['id']==$config['siteCountry'])) {

			$reg->assign('VAL_COUNTRY_SELECTED','selected="selected"');

		} else {
			$reg->assign('VAL_COUNTRY_SELECTED','');
		}
		$reg->parse('reg.repeat_countries');

	}


	if(isset($_POST['title'])) {

		$reg->assign('VAL_TITLE',stripslashes(sanitizeVar($_POST['title'])));
		$reg->assign('VAL_FIRST_NAME',stripslashes(sanitizeVar($_POST['firstName'])));
		$reg->assign('VAL_LAST_NAME',stripslashes(sanitizeVar($_POST['lastName'])));
		$reg->assign('VAL_EMAIL',stripslashes(sanitizeVar($_POST['email'])));
		$reg->assign('VAL_PHONE',stripslashes(sanitizeVar($_POST['phone'])));
		$reg->assign('VAL_MOBILE',stripslashes(sanitizeVar($_POST['mobile'])));
		$reg->assign('VAL_COMPANY_NAME',stripslashes(sanitizeVar($_POST['companyName'])));
		$reg->assign('VAL_ADD_1',stripslashes(sanitizeVar($_POST['add_1'])));
		$reg->assign('VAL_ADD_2',stripslashes(sanitizeVar($_POST['add_2'])));
		$reg->assign('VAL_TOWN',stripslashes(sanitizeVar($_POST['town'])));
		$reg->assign('VAL_COUNTY',stripslashes($county));
		$reg->assign('VAL_POSTCODE',stripslashes(sanitizeVar($_POST['postcode'])));

		if($_POST['password'] == $_POST['passwordConf']) {
			$reg->assign('VAL_PASSWORD',sanitizeVar($_POST['password']));
			$reg->assign('VAL_PASSWORD_CONF',sanitizeVar($_POST['passwordConf']));
		}

		if(isset($_POST['optIn1st']) && $_POST['optIn1st']==1) {
			$reg->assign('VAL_OPTIN1ST_CHECKED','checked="checked"');
		}

		if($_POST['htmlEmail']==0) {
			$reg->assign('VAL_HTMLEMAIL_SELECTED','selected="selected"');
		}
	}

	// Start Spam Bot Control
	if($config['floodControl']=='recaptcha') {
		$reg->assign('TXT_SPAMBOT', $lang['reg']['spambot']);
		$recaptcha	= recaptcha_get_html($ini['recaptcha_public_key'], false, detectSSL());
		$reg->assign('DISPLAY_RECAPTCHA', $recaptcha);
		$reg->parse('reg.recaptcha');
	} elseif($config['floodControl']==1) {

		$spamCode = strtoupper(randomPass(5));
		$ESC = createSpamCode($spamCode);

		$imgSpambot = imgSpambot($ESC);

		$reg->assign('VAL_ESC',$ESC);
		$reg->assign('TXT_SPAMBOT',$lang['reg']['spambot']);
		$reg->assign('IMG_SPAMBOT',$imgSpambot);
		$reg->parse('reg.spambot');
	}

$reg->parse('reg');
$page_content = $reg->text('reg');
?>