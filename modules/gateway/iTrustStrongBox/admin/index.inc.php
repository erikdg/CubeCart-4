<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission('settings','read',true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if(isset($_POST['module'])){
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'status.inc.php';
	$cache = new cache('config.'.$moduleName);
	$cache->clearCache();
	$module = array(); 
	$msg = writeDbConf($_POST['module'], $moduleName, $module);
}
$module = fetchDbConfig($moduleName);
?>

<p><a href="http://www.itrustoffice.com/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
<p class="copyText">StrongBox</p>

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
    </select>	</td>
  </tr>
   <tr>
  	<td align="left" class="tdText"><strong>Description:</strong>	</td>
    <td class="tdText"><input type="text" name="module[desc]" value="<?php echo $module['desc']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
    <td align="left" class="tdText">
      <strong>Company Name:</strong>
    </td>
    <td class="tdText">
      <input type="text" name="module[dealerId]" value="<?php echo $module['dealerId']; ?>" class="textbox" size="50" />
    </td>
  </tr>
  <tr>
	<td align="left" class="tdText">
		<strong>Customer ID Prefix:</strong> 
		<br/>e.g. &quot;Store1_&quot;<br/>To uniquely identify customer id from <br/>this website in StrongBox application.
	</td>
    <td class="tdText"><input type="text" name="module[CustPrefix]" value="<?php echo $module['CustPrefix']; ?>" class="textbox" size="30" /></td>
  </tr>
	<tr>
    <td align="left" class="tdText"><strong>Default:</strong></td>
      <td class="tdText">
	<select name="module[default]">
		<option value="1" <?php if($module['default'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['default'] == 0) echo "selected='selected'"; ?>>No</option>
	</select>	</td>
  </tr>
    <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
