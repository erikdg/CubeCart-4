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
|	install.php
|   ========================================
|	Installation Script	
+--------------------------------------------------------------------------
*/

require("../ini.inc.php");
session_start();
if (isset($_POST['license_key'])) {
	$_SESSION['license_key'] = preg_replace('#[^a-z0-9\-]+#i', '', $_POST['license_key']);
}
$thisVersion = $ini['ver'];
unset($ini['ver']);

require("..".CC_DS."includes".CC_DS."global.inc.php");
require_once("..".CC_DS."classes".CC_DS."db".CC_DS."db.php");
require_once("..".CC_DS."classes".CC_DS."cache".CC_DS."cache.php");
require("..".CC_DS."includes".CC_DS."functions.inc.php");

## Clear the cache
$cache = new cache();
$cache->clearCache();


if (!isset($_POST['step'])) {
	$_POST['step'] = 1;
}
$db = new db();

## look for version history!

## see if history table exists
$checkTable = $db->getRows("SHOW TABLE STATUS LIKE '".$glob['dbprefix']."CubeCart_history';");

## table exists, let's look for version history
if ($checkTable) {
	$versionData = $db->select("SELECT version FROM `".$glob['dbprefix']."CubeCart_history` ORDER BY `id` DESC");
} 

if (!isset($_GET['l'])) $_GET['l'] = "en";

$langFolder = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['l']);
define('LANG_FOLDER', $langFolder);

require("..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."common.inc.php");
require("..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."setup.inc.php");

if ($versionData) {
	$oldVersion = $versionData[0]['version'];
	
	if (version_compare($oldVersion, '4.3.3', '<=')) {
		## Delete session basket data
		$db->misc("UPDATE `".$glob['dbprefix']."CubeCart_sessions` SET `basket` = NULL;");
	}
} else {
	## table doesn't exist use old ini.inc.php file
	$oldIniPath = "..".CC_DS."includes".CC_DS."ini.inc.php";
	if (!file_exists($oldIniPath)) die($lang['setup']['critical_upgrade_error']);
	include($oldIniPath);
	$oldVersion = $ini['ver'];
}


$stageName = $lang['setup']['stage1Name'];

$noInstallSteps = 3;
include("includes".CC_DS."header.inc.php");
?>
<div class="mainPad">
<div class="subTitle"><?php echo sprintf($lang['setup']['installation'],$thisVersion); ?></div>
<?php if(isset($error)){?><div class="errorBar"><?php echo $error; ?></div><?php } ?>
<div> 
<?php
if (version_compare($thisVersion, $oldVersion, '<=')) {
	echo '<p>'.sprintf($lang['setup']['alreadyUpgraded'], $thisVersion).'</p>';
} else {
?>
<table border="0" width="100%" cellpadding="3" cellspacing="1">
  <tr>
  	<td><?php echo sprintf($lang['setup']['stepStatus'],$_POST['step'], $noInstallSteps);?>
	</td>
    <td height="13" width="13" <?php if($_POST['step']==1) echo "class='progressOn'"; else echo "class='progressOff'"; ?>>1</td>
    <td height="13" width="13" <?php if($_POST['step']==2) echo "class='progressOn'"; else echo "class='progressOff'"; ?>>2</td>
	<td height="13" width="13" <?php if($_POST['step']==3) echo "class='progressOn'"; else echo "class='progressOff'"; ?>>3</td>
  </tr>
</table>
<br />
<form name="install" action="upgrade.php?l=<?php echo $langFolder; ?>" method="post" enctype="multipart/form-data">
<?php
if ($_POST['step'] == 1) {
	echo "<p>".sprintf($lang['setup']['upgrade_precis'],$oldVersion,$thisVersion)."</p>";
	
	if(!isset($glob['license_key'])){
	?>
	<p><strong><?php echo $lang['setup']['enter_key'];?></strong><br />
<input type='text' value='' name='license_key' /></p>
	<?php
	}
	$nextStep = 2;
	$buttonText = $lang['setup']['upgrade_now']; 
} else if ($_POST['step'] == 2) {
	
	if (isset($_POST['license_key']) && empty($_POST['license_key'])) {
		echo $lang['setup']['no_key_entered'];
		$nextStep = 1;
		$buttonText = $lang['setup']['upgrade_try_again'];
	} else {
		## upgrade database
		$sqlfile = "db".CC_DS."upgrade.sql";
		$dbprefix = $glob['dbprefix'];
		include "sqlinstaller.php";
		$nextStep = 3;
		$buttonText = sprintf($lang['setup']['upgrade_proceed_to_step'],$nextStep);
		echo "<p>".$lang['setup']['database_upgraded']."</p>";
	}
	
} else if ($_POST['step'] == 3) {
	## do any other required updates
	include "otherupdates.php";
	
	## If there are no more steps we can insert the update history
	if (!isset($nextStep)) {
		$db->misc("INSERT INTO `".$glob['dbprefix']."CubeCart_history` (`version` ,`time`) VALUES ('".$thisVersion."', '".time()."');");
	}
	
	## Make sure enclding/collation is correct
	$db->misc("ALTER DATABASE `".$glob['dbdatabase']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
	$tables = $db->getRows("SHOW TABLE STATUS LIKE '".$glob['dbprefix']."CubeCart_%';");
	if(is_array($tables)){
		foreach($tables as $table) {
			 $db->misc("ALTER TABLE `".$table[0]."`  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;"); 
		}
	}
	 
}
?>
<?php
if ($nextStep>0) {
?>
	<p style="text-align: center">
	<input name="submit" type="submit" class="submit" value="<?php echo $buttonText; ?>"/>	
	<input type="hidden" name="step" value="<?php echo $nextStep; ?>" />
	</p>
<?php
} else {
	include("..".CC_DS."includes".CC_DS."global.inc.php");
	## Send anonymous server stats to CubeCart HQ
	if (function_exists('curl_init')) {
		preg_match('#^(\d+\.\d+\.\d+)#', PHP_VERSION, $php_version);
		preg_match('#^(\d+\.\d+\.\d+)#', mysql_get_server_info(), $sql_version);
		$request	= array(
			'CC_Version'	=> $thisVersion,
			'CC_Previous'	=> $oldVersion,
			'IP_Address'	=> get_ip_address(),	## We ONLY use this to get the user's country with GeoIP, then discard it
			'Server'		=> urlencode($_SERVER['SERVER_SOFTWARE']),
			'MySQL'			=> $sql_version[1],
			'PHP'			=> $php_version[1],
			'PHP_OS'		=> PHP_OS,
			'PHP_SAPI'		=> php_sapi_name(),
			'PHP_EXT'		=> array(
				'Zend'			=> (int)has_zend_optimizer(),
				'Ioncube'		=> (int)has_ioncube_loader(),
			),
		);
		$php_exts	= array('APC','eAccelerator','FileInfo','Hash','mCrypt','mysqli','memcache','XCache','XDebug');
		foreach ($php_exts as $ext) {
			$request['PHP_EXT'][$ext]	= (int)extension_loaded($ext);
		}
		$stat	= curl_init('http://cp.cubecart.com/licence/statistics');
		$curl_options	= array(
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> http_build_query($request, null, '&')
		);
		## curl_setopt_array work around
		if (!function_exists('curl_setopt_array')) {
		   function curl_setopt_array(&$ch, $curl_options)
		   {
		       foreach ($curl_options as $option => $value) {
		           if (!curl_setopt($ch, $option, $value)) {
		               return false;
		           } 
		       }
		       return true;
		   }
		} else {
			curl_setopt_array($stat, $curl_options);
		}
		curl_exec($stat);
		curl_close($stat);
		unset($curl_options, $request, $stat);
	}
?>
<p class="infoBar"><?php echo sprintf($lang['setup']['upgrade_success'], $thisVersion); ?></p>
<table width="100%"  border="0" cellspacing="0" cellpadding="3" class="formTable">
  <tr>
    <td colspan="4">
	<?php echo $lang['setup']['congratulationsSub']; ?>
	<div style="padding-left: 100px;">
	<ul>
	<li><a href="<?php echo $glob['storeURL'];?>/<?php echo $glob['adminFile'];?>"><?php echo $lang['setup']['adminHomepage']; ?></a><br />
	  <?php echo $glob['storeURL'];?>/<?php echo $glob['adminFile'];?></li>
	<li><a href="<?php echo $glob['storeURL'];?>/"><?php echo $lang['setup']['storeHomepage']; ?></a><br />
	<?php echo $glob['storeURL'];?>/</li>
	</ul>
	</div>
	<strong><?php echo $lang['setup']['important']; ?></strong> <?php echo $lang['setup']['deleteInstall']; ?>	</td>
  </tr>
</table>
<?php
}
?>
</form>
<?php
}
include("includes".CC_DS."footer.inc.php");
?>