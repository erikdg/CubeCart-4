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
|	Configure psiGate
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

<p><a href="http://www.psigate.com/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>

<form action="<?php echo $glob['adminFile']; ?>?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Configuration Settings </td>
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
    <td align="left" class="tdText"><strong>Enable Card Validation:</strong></td>
    <td class="tdText">
	<select name="module[validation]">
		<option value="1" <?php if($module['validation']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['validation']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
  </tr>
   <tr>
  	<td align="left" class="tdText"><strong>Description:</strong>
	</td>
    <td class="tdText"><input type="text" name="module[desc]" value="<?php echo $module['desc']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Merchant Id:</strong></td>
    <td class="tdText"><input type="text" name="module[acNo]" value="<?php echo $module['acNo']; ?>" class="textbox" size="30" /></td>
  </tr>
    <tr>
  <td align="left" class="tdText"><strong>Passphrase:</strong></td>
    <td class="tdText"><input type="text" name="module[passPhrase]" value="<?php echo $module['passPhrase']; ?>" class="textbox" size="30" /></td>
  </tr>
    <tr>
      <td align="left" class="tdText"><strong>Gateway URL: </strong></td>
      <td class="tdText"><input type="text" name="module[url]" value="<?php echo $module['url']; ?>" class="textbox" size="30" /></td>
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
  <td align="left" class="tdText"><strong>Test Mode:</strong></td>
      <td class="tdText">
	<select name="module[test]">
		<option value="1" <?php if($module['test'] == 1) echo "selected='selected'"; ?>>On</option>
		<option value="0" <?php if($module['test'] == 0) echo "selected='selected'"; ?>>Off</option>
	</select>
	</td>
  </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
