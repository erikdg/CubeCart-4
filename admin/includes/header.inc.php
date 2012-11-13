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
|	header.inc.php
|   ========================================
|	Admin Header
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

$langFolder = (defined('LANG_FOLDER') && constant('LANG_FOLDER')) ? LANG_FOLDER :  $config['defaultLang'];
include CC_ROOT_DIR.CC_DS.'language'.CC_DS.$langFolder.CC_DS.'config.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charsetIso; ?>" />
<link href="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/styles/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>js/prototype.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>js/scriptaculous.js"></script>
<script type="text/javascript">
var fileLoadingImage		= '<?php echo $GLOBALS['rootRel']; ?>images/lightbox/loading.gif';
var fileBottomNavCloseImage	= '<?php echo $GLOBALS['rootRel']; ?>images/lightbox/close.gif';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['rootRel']; ?>js/jslibrary.js"></script>
<?php
if (isset($jsScript)) { ?>
<script type="text/javascript">
<?php echo $jsScript; ?>
</script>
<?php
}
?>
<title>CubeCart&trade; - <?php echo $lang['admin_common']['incs_administration'];?></title>
</head>
<body id="pageTop">
<img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/progress.gif" alt="" title="" id="loading" class="hidden" />
<div id="bg_fade" class="hidden"></div>
<?php
if (isset($ccAdminData['adminId']) && $ccAdminData['adminId']>0) {
?>
<!-- start wrapping table -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" width="180" rowspan="3" class="tdNav">
<?php require(CC_ROOT_DIR . CC_DS . $glob['adminFolder'] . CC_DS . "includes" .CC_DS. "navigation.inc.php"); ?>
	</td>
  </tr>
  <tr>
  <td valign="top" class="tdContent">
<!-- end wrapping table -->
<div id="topBar">
<div id="loginBar">
	<span class="txtLogin"><?php echo $lang['admin_common']['incs_logged_in_as'];?> <strong><?php echo $ccAdminData['username']; ?></strong> [ </span><a href="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=logout" class="txtLink"><?php echo $lang['admin_common']['incs_logout'];?></a> <span class="txtLogin">|</span> <a href="<?php echo $GLOBALS['rootRel']; ?><?php echo $glob['adminFile']; ?>?_g=adminusers/changePass" class="txtLink"><?php echo $lang['admin_common']['incs_change_pass'];?></a> <span class="txtLogin">]</span>
</div>

<div id="dateBar">
	<span class="txtLogin"><?php echo formatTime(time(),$strftime); ?></span>
</div>
</div>
<!-- start of admin content -->
<div id="contentPad">
<?php } ?>