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
|	Configure WorldPay
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

<p><a href="http://www.worldpay.com/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
<p class="copyText">&quot;Internet Payment Solutions.&quot;</p>

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
  <td align="left" class="tdText"><strong>Installation ID:</strong></td>
    <td class="tdText"><input type="text" name="module[acNo]" value="<?php echo $module['acNo']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Test Mode: </strong></td>
    <td class="tdText"><select name="module[testMode]">
	  <option value="100" <?php if($module['testMode'] == 100) echo "selected='selected'"; ?>>On - Always Successful</option>
      <option value="101" <?php if($module['testMode'] == 101) echo "selected='selected'"; ?>>On - Always Declined</option>
      <option value="0" <?php if($module['testMode'] == 0) echo "selected='selected'"; ?>>Off</option>
    </select></td>
  </tr>
  <!--
  <tr>
    <td align="left" class="tdText"><strong>Auth Mode: </strong></td>
    <td class="tdText"><select name="module[authMode]">
	<option value="" <?php if($module['authMode'] == "") echo "selected='selected'"; ?>>N/A</option>
	  <option value="A" <?php if($module['authMode'] == "A") echo "selected='selected'"; ?>>Full Auth</option>
      <option value="E" <?php if($module['authMode'] == "E") echo "selected='selected'"; ?>>Pre Auth</option>
	  <option value="O" <?php if($module['authMode'] == "O") echo "selected='selected'"; ?>>Post Auth</option>
    </select></td>
  </tr>
  --->
  <td align="left" class="tdText"><strong>Default:</strong></td>
      <td class="tdText">
	<select name="module[default]">
		<option value="1" <?php if($module['default'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['default'] == 0) echo "selected='selected'"; ?>>No</option>
	</select>	</td>
  </tr>
  <tr>
   <td colspan="2" align="left" class="tdText"><strong>Payment Response URL:</strong><br/>This has to be set in the WorldPay control panel.<br />
	 <input type="text" value="<?php echo $GLOBALS['storeURL']."/index.php?_g=rm&amp;type=gateway&amp;cmd=call&amp;module=WorldPay";?>" size="70" />	</td>
    </tr>
  <tr>
    <td colspan="2" class="tdText">Please make sure "Payment Response enabled" is set to true in your WorldPay control panel. </td>
    </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
<p>For WorldPay to show a success or fail screen please follow the instructions below:</p>


As a default the shopper response feature is set to OFF, to enable this feature:

<ol>
<li>Log in to the Merchant Interface</li>
<li>Select Installations from the left hand navigation</li>
<li>Choose an installation and select the Integration Setup button for either the TEST or PRODUCTION environment</li>
<li>Check the Enable Shopper Response checkbox.</li>
<li>Select the Save Changes button</li>
</ol>
More information can be found at: <a href="http://www.rbsworldpay.com/support/kb/bg/paymentresponse/payment_response.html" class="txtLink">http://www.rbsworldpay.com/support/kb/bg/paymentresponse/payment_response.html</a>

