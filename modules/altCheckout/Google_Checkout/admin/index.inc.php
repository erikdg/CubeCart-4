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
|	Configure Google Checkout
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
<p><a href="http://checkout.google.com/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>
<?php
if(isset($msg))
{
	echo msg($msg);
}

?>
<p class="tdText"><strong>IMPORTANT:</strong> Google Checkouts is not compatible with real time shipping quotes or the shipping methods built into the CubeCart. This because no customer information is captured prior to transferring them to Google. For this reason you must configure custom shipping methods for them and a table of this data is sent to Google. Tax settings will be inherited from your CubeCart stores tax settings.</p>
<p class="tdText"><a href='<?php echo $glob['adminFile']."?_g=settings/altShipping";?>' class="txtLink">Configure Google Shipping Methods</a></p>
<p>Google Checkout will ONLY function with a valid SSL certificate in operation. The URLS will show below once this has been enabled.</p>
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
  <td align="left" class="tdText"><strong>Google merchant ID:</strong></td>
    <td class="tdText"><input type="text" name="module[merchId]" value="<?php echo $module['merchId']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Google merchant key:</strong></td>
    <td class="tdText"><input type="text" name="module[merchKey]" value="<?php echo $module['merchKey']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Mode:</strong></td>
    <td class="tdText"><select name="module[mode]">
      <option value="sandbox" <?php if($module['mode'] == "sandbox") echo "selected='selected'"; ?>>SandBox (Testing Environment)</option>
      <option value="live" <?php if($module['mode'] == "live") echo "selected='selected'"; ?>>Live</option>
    </select></td>
  </tr>
   <tr>
    <td width="33%" align="left" valign="top" class="tdText"><strong>Send Welcome Email:</strong><br />
(Contains user/password for future access)</td>
    <td class="tdText"><select name="module[welcomeEmail]">
		<option value="0" <?php if($module['welcomeEmail'] == 0) echo "selected='selected'"; ?>>No</option>
		<option value="1" <?php if($module['welcomeEmail'] == 1) echo "selected='selected'"; ?>>Yes</option>

	</select></td>
  </tr>
  <tr>
    <tr>
    <td align="left" class="tdText"><strong>Button size:</strong></td>
    <td class="tdText">
	<select name="module[size]">
      <option value="large" <?php if($module['size'] == "large") echo "selected='selected'"; ?>>Large</option>
      <option value="medium" <?php if($module['size'] == "medium") echo "selected='selected'"; ?>>Medium</option>
	  <option value="small" <?php if($module['size'] == "small") echo "selected='selected'"; ?>>Small</option>
    </select></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Debug:</strong></td>
    <td class="tdText">
	<select name="module[debug]">
		<option value="1" <?php if($module['debug'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['debug'] == 0) echo "selected='selected'"; ?>>No</option>
	</select>	</td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>API Callback URL: </strong></td>
    <td class="tdText"><input type="text" value="<?php if($config['ssl']) { echo $config['storeURL_SSL']."/index.php?_g=rm&amp;type=altCheckout&amp;cmd=call&amp;module=Google_Checkout"; } else { echo "VALID SSL CONFIGURATION REQUIRED"; } ?>" style="width: 95%;" /><br />
Please set callback method to XML. <br />
<strong>IMPORTANT:</strong> Google Checkout will ONLY work in Live mode if the API Callback URL is under the HTTPS protocol. You may need to purchase an SSL certificate if your hosting doesn't have this functionality allready.
</td>
  </tr>
    <tr>
    <td align="left" class="tdText"><strong>Public business website:</strong></td>
    <td class="tdText"><input type="text" value="<?php if($config['ssl']) { echo $config['storeURL_SSL']."/index.php?_g=co&amp;_a=confirmed&amp;s=3"; } else { echo "VALID SSL CONFIGURATION REQUIRED"; } ?>" style="width: 95%;" /><br />
Customers will be returned here after payment. (Set under the &quot;Settings&quot; tab in the Google Checkout control panel)
</td>
  </tr>

  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>



