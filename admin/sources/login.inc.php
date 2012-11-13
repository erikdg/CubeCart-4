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
|	Admin Session Start
+--------------------------------------------------------------------------
*/
if(!defined('CC_INI_SET')){ die("Access Denied"); }

if(isset($_GET['ccSSL']) && $_GET['ccSSL']==1){
	$enableSSl = 1;
}
include('includes'.CC_DS.'sslSwitch.inc.php');

if (isset($_POST['username']) && isset($_POST['password'])){

	$result = $admin_session->login($_POST['username'], $_POST['password']);
	// data for admin session log
	$data['username'] = $db->mySQLSafe($_POST['username']);
	$data['time'] = time();
	$data['ipAddress'] = $db->mySQLSafe(get_ip_address());

	if($result) {
		// First level of brute force attack prevention
		if($db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],true,'b')){
			$blocked = true;
		} else {

			$data['success'] = '1';
			// Reset fail level
			$newdata['failLevel'] = '0';
			$newdata['blockTime'] = '0';
			$newdata['noLogins'] = 'noLogins+1';

			$db->update($glob['dbprefix'].'CubeCart_admin_users', $newdata, '`adminId` = '.$result[0]['adminId']);

		}

	} else {
		// First level of brute force attack prevention
		$blocked = $db->blocker($_POST['username'],$ini['bfattempts'],$ini['bftime'],false,'b');

		if(!$blocked) {

			// check user exists
			$query = sprintf('SELECT `adminId`, `failLevel`, `blockTime`, `username`, `lastTime` FROM '.$glob['dbprefix'].'CubeCart_admin_users WHERE `username` = %s',
			$db->mySQLSafe($_POST['username']));

			$user = $db->select($query);

			// Second level of brute force attack prevention
			if($user) {

				if($user[0]['blockTime']>0 && $user[0]['blockTime']<time()) {
					// reset fail level and time
					$newdata['failLevel'] = '1';
					$newdata['blockTime'] = '0';
				} elseif($user[0]['failLevel']==($ini['bfattempts']-1)) {

					$timeAgo = time() - $ini['bftime'];

					if($user[0]['lastTime']<$timeAgo) {
						$newdata['failLevel'] = 1;
						$newdata['blockTime'] = 0;
					} else {

						// block the account
						$newdata['failLevel'] = $ini['bfattempts'];
						$newdata['blockTime'] = time()+$ini['bftime'];

					}

				} elseif($user[0]['blockTime']<time()) {

					$timeAgo = time() - $ini['bftime'];
					if($user[0]['lastTime']<$timeAgo) {
						$newdata['failLevel'] = 1;
					} else {
						// set fail level + 1
						$newdata['failLevel'] = $user[0]['failLevel']+1;
					}

					$newdata['blockTime'] = 0;
				} else {
					$msg = "<p class='warnText'>".sprintf($lang['admin_common']['blocked'],($ini['bftime']/60))."</p>";
					$blocked = true;
				}

				if(is_array($newdata)) {
					$newdata['lastTime'] = time();
					$db->update($glob['dbprefix']."CubeCart_admin_users", $newdata, "adminId=".$user[0]['adminId'],$stripQuotes="");
				}

			}

		} else {
			// login failed message
			$msg = "<p class='warnText'>".$lang['admin_common']['login_failed']."</p>";

		}

	}

	if($blocked) {
		$msg = "<p class='warnText'>".sprintf($lang['admin_common']['blocked'],sprintf("%.0f",($ini['bftime']/60)))."</p>";
	} else {

		$insert = $db->insert($glob['dbprefix']."CubeCart_admin_sessions", $data);

		// if there is over max amount of login records delete last one
		// this prevents database attacks of bloating
		if($db->numrows('SELECT `loginId` FROM '.$glob['dbprefix'].'CubeCart_admin_sessions')>250) {
			$loginId = $db->select('SELECT min(`loginId`) as id FROM '.$glob['dbprefix'].'CubeCart_admin_sessions');
			$db->delete($glob['dbprefix']."CubeCart_admin_sessions","`loginId`='".$loginId[0]['id']."'");
		}

	}


	if($result && !$blocked) {
		$admin_session->createSession($result[0]['adminId']);

		if(isset($_GET['goto']) && !empty($_GET['goto'])){
			// check redirect URL is safe!
			if (preg_match('/^(http(s?)\:\/\/|\/\/)/i', $_GET['goto']) && !preg_match('@^'.$glob['storeURL'].'|^'.$config['storeURL_SSL'].'@i', $_GET['goto'])) {
				httpredir($GLOBALS['rootRel'].$glob['adminFile']);
			} else {
				httpredir(sanitizeVar(urldecode($_GET['goto'])));
			}
		} else {
			httpredir($GLOBALS['rootRel'].$glob['adminFile']);
		}

	}

}
if(isset($_GET['email']) && validateEmail($_GET['email'])) {
	$msg = "<p class='infoText'>".$lang['admin_common']['other_new_pass_sent']." ".sanitizeVar(urldecode($_GET['email']))."</p>";
}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if(isset($msg)) {
	echo msg($msg,false);
} elseif(!isset($GLOBALS[CC_ADMIN_SESSION_NAME]) && !isset($_POST['username']) && !isset($_POST['password'])) {
?>
<p class="infoText"><?php echo  $lang['admin_common']['other_no_admin_sess'];?></p>
<?php } elseif (isset($_POST['username']) && isset($_POST['password'])){ ?>
<p class="warnText"><?php echo  $lang['admin_common']['other_login_fail_2'];?></p>
<?php }

$goTo = sanitizeVar($_GET['goto']);

if(detectSSL()) {
	// make sure goto URL is HTTPS rather than HTTP
	$goTo = str_replace($glob['storeURL'], $config['storeURL_SSL'],$goTo);
	$onclickurl = $glob['storeURL'].'/'.$glob['adminFile'].'?_g=login';
	$postUrl = $config['storeURL_SSL'].'/'.$glob['adminFile'].'?_g=login&amp;ccSSL=1';
} else {
	// make sure goto URL is HTTP rather than HTTPS
	$goTo = str_replace($config['storeURL_SSL'], $glob['storeURL'],$goTo);
	$onclickurl = $config['storeURL_SSL'].'/'.$glob['adminFile'].'?_g=login&amp;ccSSL=1';
	$postUrl = $glob['storeURL'].'/'.$glob['adminFile'].'?_g=login';
}

if(!empty($goTo)){
	$onclickurl .= '&amp;goto='.urlencode($goTo);
	$postUrl .= '&amp;goto='.urlencode($goTo);
}
?>
<form action="<?php echo  $postUrl; ?>" method="post" enctype="multipart/form-data" name="ccAdminLogin" target="_self"  onsubmit="disableSubmit(document.getElementById('login'),'<?php echo  $lang['admin_common']['please_wait']; ?>');" >
<div style="margin: auto; width: 285px; padding-bottom: 15px; padding-top: 25px;"><a href="<?php echo  $glob['adminFile']; ?>"><img src="<?php echo  $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/ccAdminLogoLrg.gif" alt="" width="280" height="55" border="0" title="" /></a></div>
<table border="0" align="center" width="285" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo  $lang['admin_common']['other_login_below'];?></td>
    </tr>
  <tr>
    <td class="tdText"><?php echo  $lang['admin_common']['other_username'];?></td>
    <td><input name="username" type="text" id="username" class="textbox" value="<?php if(isset($_POST['username'])) echo sanitizeVar($_POST['username']); ?>" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo  $lang['admin_common']['other_password'];?></td>
    <td><input name="password" type="password" autocomplete="off" id="password" class="textbox" /></td>
  </tr>
  <?php
  if($config['ssl'] && !$config['force_ssl']) {
?>
	  <tr>
		<td>&nbsp;</td>
		<td class="tdText"><?php echo  $lang['admin_common']['other_login_ssl'];?> <input type="checkbox" name="ccSSL" value="1" <?php if($_GET['ccSSL']==1) { echo 'checked="checked"'; }?>
		onclick="parent.location='<?php echo  $onclickurl; ?>'" /></td>
	  </tr>
	  <?php
  }
  ?>
  <tr>
    <td>&nbsp;</td>
    <td><a href="<?php echo  $glob['adminFile']; ?>?_g=requestPass" class="txtLink"><?php echo  $lang['admin_common']['other_request_pass'];?></a> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<input name="login" type="submit" id="login" value="<?php echo  $lang['admin_common']['other_login'];?>" class="submit" />	</td>
  </tr>
</table>
</form>
<div style="margin: auto; width: 285px; padding-top: 10px; text-align: right;" class="copyrightText">Copyright <a href="http://www.devellion.com" target="_blank" class="copyrightText">Devellion Limited</a> <?php echo  date("Y");?>.<br />All rights reserved.</div>