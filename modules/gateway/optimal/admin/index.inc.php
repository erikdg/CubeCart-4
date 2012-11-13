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
|   Date: Friday, 15 July 2005
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	index.inc.php
|   ========================================
|	Configure Optimal Payments
+--------------------------------------------------------------------------
*/
if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission('settings','read',true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if(isset($_POST['module']))
{
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'status.inc.php';
	$cache = new cache('config.'.$moduleName);
	$cache->clearCache();
	//$module = fetchDbConfig($moduleName); // Uncomment this is you wish to merge old config with new
	$module = array(); // Comment this out if you don't want the old config to merge with new
	$msg = writeDbConf($_POST['module'], $moduleName, $module);
}
$module = fetchDbConfig($moduleName);
?>

<p><a href="http://www.optimalpayments.co.uk"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>
<?php
if(isset($msg)) {
	echo msg($msg);
}
?>
<p class="copyText">&quot;The Optimal Payment Solution.&quot;</p>

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
    <td align="left" class="tdText"><strong>API Method:</strong></td>
    <td class="tdText">
	<select name="module[API_method]">
		<option value="tradegard" <?php if($module['API_method']=='tradegard') echo "selected='selected'"; ?>>Checkout API Tradegard</option>
		<option value="standard" <?php if($module['API_method']=='standard') echo "selected='selected'"; ?>>Checkout API</option>
    </select>	</td>
  </tr>
  <!--
  <tr>
    <td align="left" class="tdText"><strong>Enable Card Validation:</strong></td>
    <td class="tdText">
	<select name="module[validation]">
		<option value="1" <?php if($module['validation']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['validation']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
  </tr>
  -->
   <!--
      <tr>
    <td align="left" class="tdText"><strong>Require CVV Code:</strong></td>
    <td class="tdText">
	<select name="module[reqCvv]">
		<option value="1" <?php if($module['reqCvv']==1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['reqCvv']==0) echo "selected='selected'"; ?>>No</option>
    </select>	</td>
  </tr>
  -->
   <tr>
  	<td align="left" class="tdText"><strong>Description:</strong>	</td>
    <td class="tdText"><input type="text" name="module[desc]" value="<?php echo $module['desc']; ?>" class="textbox" size="30" /></td>
  </tr>
  <!--
  <tr>
  <td align="left" class="tdText"><strong>Merchant Account Number:</strong></td>
    <td class="tdText"><input type="text" name="module[accountNum]" value="<?php echo $module['accountNum']; ?>" class="textbox" size="30" /></td>
  </tr>
  -->
  <tr>
  <td align="left" class="tdText"><strong>Shop ID:</strong></td>
    <td class="tdText"><input type="text" name="module[shopId]" value="<?php echo $module['shopId']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Shared Key:</strong></td>
    <td class="tdText"><input type="text" name="module[sharedKey]" value="<?php echo $module['sharedKey']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td><strong>Callback URL:</strong></td>
  <td><?php 
  if($config['SSL']) {
  echo $config['storeURL_SSL']; ?>/modules/gateway/optimal/call.php
  <?php } else { 
  ?>
  SSL is required to use Optimal Payments call back feature to automate your orders. Please enable SSL to view the URL. 
  <?php
  }
  ?>
  </td>
  </tr>
   <tr>
   <td align="left" class="tdText"><strong>Default:</strong></td>
      <td class="tdText">
	<select name="module[default]">
		<option value="1" <?php if($module['default'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['default'] == 0) echo "selected='selected'"; ?>>No</option>
	</select>	</td>
  </tr>
	<!--
   <tr>
     <td align="left" class="tdText"><strong>Debugging: </strong></td>
     <td class="tdText"><select name="module[debug]">
       <option value="0" <?php if($module['debug'] == 0) echo "selected='selected'"; ?>>No</option>
	   <option value="1" <?php if($module['debug'] == 1) echo "selected='selected'"; ?>>Yes</option>
     </select></td>
   </tr>
   -->
   <tr>
    <td align="left" class="tdText"><strong>Test Mode:</strong></td>
    <td class="tdText">
	<select name="module[test_mode]">
		<option value="1" <?php if($module['test_mode']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['test_mode']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
  </tr>
   <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
