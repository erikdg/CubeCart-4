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
|	index.inc.php
|   ========================================
|	Configure PayPal
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission('settings','read',true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if(isset($_POST['module'])){
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'status.inc.php';
	$cache = new cache('config.'.$moduleName);
	$cache->clearCache();
	//$module = fetchDbConfig($moduleName); // Uncomment this is you wish to merge old config with new
	$module = array(); // Comment this out if you don't want the old config to merge with new
	$msg = writeDbConf($_POST['module'], $moduleName, $module);

}
$module = fetchDbConfig($moduleName);
?>

<p><a href="http://www.epdq.co.uk/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="Barclays ePDQ" border="0" title="" /></a></p>
<?php if(isset($msg)) echo msg($msg); ?>

<form action="<?php echo $glob['adminFile']; ?>?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Configuration Settings</td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Status:</strong></td>
    <td class="tdText">
	  <select name="module[status]">
		<option value="1" <?php if($module['status']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['status']==0) echo "selected='selected'"; ?>>Disabled</option>
      </select>
	</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Default:</strong></td>
	<td class="tdText">
	  <select name="module[default]">
		<option value="1" <?php if($module['default'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['default'] == 0) echo "selected='selected'"; ?>>No</option>
	  </select>
	</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Mode:</strong></td>
	<td class="tdText">
	  <select name="module[test_mode]">
		<option value="0" <?php if($module['test_mode'] == '0') echo "selected='selected'"; ?>>Live</option>
		<option value="1" <?php if($module['test_mode'] == '1') echo "selected='selected'"; ?>>Testing</option>
	  </select>
	</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Charge Type:</strong></td>
	<td class="tdText">
	  <select name="module[chargetype]">
		<option value="Auth" <?php if($module['chargetype'] == 'Auth') echo "selected='selected'"; ?>>Auth</option>
		<option value="PreAuth" <?php if($module['chargetype'] == 'PreAuth') echo "selected='selected'"; ?>>PreAuth</option>
	  </select>
	</td>
  </tr>
  <tr>
  	<td align="left" class="tdText"><strong>Description:</strong>	</td>
    <td class="tdText"><input type="text" name="module[desc]" value="<?php echo $module['desc']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
	<td align="center" colspan="2"><strong>The following values will need to match those in the <a href="https://cpiadmin.epdq.co.uk/cgi-bin/CcxBarclaysEpdqAdminTool.e" target="_blank">ePDQ CPI Administration tool</a>.</strong></td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Client ID:</strong></td>
    <td class="tdText"><input type="text" name="module[clientid]" value="<?php echo $module['clientid']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Passphrase:</strong></td>
    <td class="tdText"><input type="password" autocomplete="off" name="module[passphrase]" value="<?php echo $module['passphrase']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Allowed URL:</strong></td>
    <td class="tdText"><?php echo $GLOBALS['storeURL']; ?>/modules/gateway/ePDQ/jump.php</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>POST URL:</strong></td>
    <td class="tdText"><?php echo $GLOBALS['storeURL']; ?>/index.php?_g=rm&type=gateway&cmd=call&module=ePDQ</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>POST Username:</strong></td>
    <td class="tdText"><input type="text" name="module[post_user]" value="<?php echo $module['post_user']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>POST password:</strong></td>
    <td class="tdText"><input type="password" autocomplete="off" name="module[post_pass]" value="<?php echo $module['post_pass']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>POST email:</strong></td>
    <td class="tdText"><?php echo $GLOBALS['config']['masterEmail']; ?></td>
  </tr>




  <tr>
	<td align="right" class="tdText">&nbsp;</td>
	<td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>