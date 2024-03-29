<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a Trade Mark of Devellion Limited
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
|	Configure ClixGalore
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
<p><a href="http://www.clixgalore.com/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="clixGalore" border="0" title="" /></a></p>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
<p class="copyText">&quot;Solutions that boost revenue, sales, leads and hits to your website.&quot;<br />
<a href="http://www.clixgalore.com/" target="_blank" class="txtLink">http://www.clixgalore.com/</a></p>
<form action="https://www.clixgalore.com/memberlogin.aspx" method="post" enctype="multipart/form-data"  name="frm_Login" target="_blank" id="frm_Login">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
	<tr>
		<td class="tdTitle">Login to clixGalore (Opens in new window) </td>
	</tr>
	<tr>
		<td align="center" class="tdText"><a href="https://www.clixgalore.com/memberlogin.aspx" class="txtLink" target="_blank">Login to Admin</a></td>
	</tr>
</table>
</form>
<br />
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
    </select></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdText"><strong>clixGalore A/C No: </strong></td>
    <td class="tdText"><input type="text" name="module[acNo]" value="<?php echo $module['acNo']; ?>" class="textbox" size="40" />	</td>
  </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>