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
|	requestPass.inc.php
|   ========================================
|	Request Admin Password
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

if (isset($_POST['email']) && validateEmail($_POST['email'])){

	$query = sprintf('SELECT `adminId`, `username`, `name` FROM '.$glob['dbprefix'].'CubeCart_admin_users WHERE `email` = %s', $db->mySQLSafe($_POST['email']));

	$result = $db->select($query);


	if($result) {

		$newPass = randomPass();
		$salt = randomPass(6);
		$data['salt'] = $db->mySQLSafe($salt);
		$data['password'] = $db->mySQLSafe(md5(md5($salt).md5($newPass)));
		$update = $db->update($glob['dbprefix'].'CubeCart_admin_users',$data,'`adminId` ='.$result[0]['adminId']);

		// make email
		require('classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php');

		$mail = new htmlMimeMail();

		$lang = getLang('email.inc.php');

			$macroArray = array(
				'RECIP_NAME' => $result[0]['name'],
				'USERNAME' => $result[0]['username'],
				'PASSWORD' => $newPass,
				'STORE_URL' => $GLOBALS['storeURL'],
				'SENDER_IP' => get_ip_address()
			);

		$text = macroSub($lang['email']['admin_reset_pass_body'],$macroArray);
		unset($macroArray);

		$mail->setText($text);
		$mail->setReturnPath($_POST['email']);
		$mail->setFrom('CubeCart Mailer <'.$config['masterEmail'].'>');
		$mail->setSubject($lang['email']['admin_reset_pass_subject']);
		$mail->setHeader('X-Mailer', 'CubeCart Mailer');
		$mail->setHeader('Reply-To', $_POST['email']);
		$mail->setHeader('Return-Path',$config['masterEmail']);
		$result = $mail->send(array($_POST['email']), $config['mailMethod']);

		httpredir($glob['adminFile']."?_g=login&email=".urlencode($_POST['email']));

	} else {
		$msg = "<p class='warnText'>".$lang['admin_common']['other_pass_reset_failed']."</p>";
	}

}
 require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
if(isset($msg))
{
	echo msg($msg);
}
else
{
// paragraph just so the display sits better
?>
<p>&nbsp;</p>
<?php
}
?>

<form action="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=requestPass" method="post" enctype="multipart/form-data" name="login" target="_self">
<div style="margin: auto; width: 285px; padding-bottom: 15px; padding-top: 25px;"><a href="<?php echo $glob['adminFile']; ?>"><img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/ccAdminLogoLrg.gif" alt="" width="280" height="55" border="0" title="" vspace="10" /></a></div>
<table border="0" align="center" width="285" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin_common']['other_enter_email_below'];?></td>
    </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin_common']['other_email_address'];?></td>
    <td><input name="email" type="text" id="email" class="textbox" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input name="login" type="submit" class="submit" id="login" value="<?php echo $lang['admin_common']['other_send_pass'];?>" /></td>
  </tr>
</table>
</form>
<div style="margin: auto; width: 285px; padding-top: 10px; text-align: right;" class="copyrightText">Copyright <a href="http://www.devellion.com" target="_blank" class="copyrightText">Devellion Limited</a> <?php echo  date("Y");?>.<br />All rights reserved.</div>