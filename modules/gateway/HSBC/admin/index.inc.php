<?php

/************************************************
* HSBC API Module by Adam Harris @ XOMY Limited *
* http://www.xomy.com | adam@xomy.com           *
*                                               *
* Before making any modifications, please       *
* contact me at the above email so that we can  *
* discuss the implications and advantages for   *
* the module.                                   *
*                                               *
* This module is released for the benefit of    *
* the community and should not be sold.         *
*                                               *
* This module is not released under GPL and     *
* cannot be redistributed without permission    *
* from myself.                                  *
************************************************/

if (!defined('CC_INI_SET')) die("Access Denied");
permission('settings', 'read', true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if (isset($_POST['module'])) {
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'status.inc.php';
	$cache = new cache('config.'.$moduleName);
	$cache->clearCache();
	//$module = fetchDbConfig($moduleName); // Uncomment this is you wish to merge old config with new
	$module	= array(); // Comment this out if you don't want the old config to merge with new
	$msg	= writeDbConf($_POST['module'], $moduleName, $module);

}
$module = fetchDbConfig($moduleName);
?>

<p><a href="https://secure-epayments.hsbc.com/" target="_blank"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>
<?php
if(isset($msg)){
	echo $msg;
}
?>

<form action="<?php echo $glob['adminFile']; ?>?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="3" class="tdTitle">Configuration Settings </td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Status:</strong></td>
    <td class="tdText">
	<select name="module[status]">
		<option value="1" <?php if($module['status']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['status']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>
	</td>
	<td></td>
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
    <td align="left" class="tdText"><strong>Enable Card Validation:</strong></td>
    <td class="tdText">
	<select name="module[validation]">
		<option value="1" <?php if($module['validation']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['validation']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Require CVV Code:</strong></td>
    <td class="tdText">
	<select name="module[reqCvv]">
		<option value="1" <?php if($module['reqCvv']==1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['reqCvv']==0) echo "selected='selected'"; ?>>No</option>
    </select>	</td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Test Mode:</strong></td>
      <td class="tdText">
	<select name="module[test]">
		<option value="1" <?php if($module['test'] == 1) echo "selected='selected'"; ?>>On (Approved)</option>
		<option value="2" <?php if($module['test'] == 2) echo "selected='selected'"; ?>>On (Declined)</option>
		<option value="0" <?php if($module['test'] == 0) echo "selected='selected'"; ?>>Off</option>
	</select>
	</td>
  </tr>
  <tr>
	<td align="left" class="tdText" nowrap="nowrap"><strong>Transaction Mode:</strong></td>
	<td class="tdText">
	  <select name="module[authmode]">
		<option value="0" <?php if($module['authmode'] == 0) echo 'selected="selected"'; ?>>Standard Transaction</option>
		<option value="1" <?php if($module['authmode'] == 1) echo 'selected="selected"'; ?>>Pre-Authorisation</option>
	  </select>
	</td>
  </tr>
  <tr><td colspan="3"><hr /></td></tr>
  <tr>
	<td align="left" class="tdText"><strong>Description:</strong></td>
	<td class="tdText"><input type="text" name="module[desc]" value="<?php echo $module['desc']; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This is the description displayed on the checkout page</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Client ID:</strong></td>
	<td class="tdText"><input type="text" name="module[acNo]" value="<?php echo $module['acNo']; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This is your HSBC Client ID, found in the header of Secure ePayments after logging in. Typically 5 numbers.</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Alias ID:</strong></td>
	<td class="tdText"><input type="text" name="module[alias]" value="<?php echo $module['alias']; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This is your HSBC Alias ID, found in the header of Secure ePayments after logging in. Typically of the format UK12345678GBP (13 characters)</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>User ID:</strong></td>
	<td class="tdText"><input type="text" name="module[userID]" value="<?php echo $module['userID']; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This is your HSBC User ID, as used to login to Secure ePayments. Typically alphanumeric or the company name.</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Password:</strong></td>
	<td class="tdText"><input type="text" name="module[passPhrase]" value="<?php echo $module['passPhrase']; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This is your HSBC Password, as used to login to Secure ePayments</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>Gateway Path: </strong></td>
	<td class="tdText"><input type="text" name="module[url]" value="<?php echo (!empty($module['url'])) ? $module['url'] : 'www.secure-epayments.apixml.hsbc.com'; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This should be set to: www.secure-epayments.apixml.hsbc.com</td>
  </tr>
  <tr><td colspan="3"><hr /></td></tr>
  <tr>
	<td align="left" class="tdText"><strong>PAS Path: </strong></td>
	<td class="tdText"><input type="text" name="module[pas]" value="<?php echo (!empty($module['pas'])) ? $module['pas'] : 'www.ccpa.hsbc.com/ccpa'; ?>" class="textbox" size="30" /></td>
	<td class="tdText">This should be set to: www.ccpa.hsbc.com/ccpa</td>
  </tr>
   <tr>
    <td align="left" class="tdText"><strong>Accept American Express:</strong></td>
    <td class="tdText">
	<select name="module[amex]">
		<option value="1" <?php if($module['amex']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['amex']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
    <td class="tdText">You <strong>must</strong> have a seperate American Express merchant account which needs to be connected to your HBSC Merchant Account. If you are unsure about this option, please contact your HSBC Secure ePayments Account Manager or call 0845 702 3344</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>AVS Check: </strong></td>
	<td class="tdText">
	  <select name="module[avs]">
		<option value="1" <?php if ($module['avs'] == 1) echo 'selected="selected"'; ?>>On</option>
		<option value="0" <?php if ($module['avs'] == 0) echo 'selected="selected"'; ?>>Off</option>
	  </select>
	</td>
	<td class="tdText">It is advised to only ship goods to the cardholder's address. Enabling this function increases security by checking the card against the supplied address and postcode.</td>
  </tr>
  <tr>
	<td align="left" class="tdText"><strong>AVS Message: </strong></td>
	<td class="tdText"><textarea name="module[avstext]"><?php echo $module['avstext']; ?></textarea></td>
	<td class="tdText">Example:<br /><br />Please Note: We can only ship your order to the Card Holder's Registered Address. Should you wish to ship to another location (e.g. your office), you will need to call us on 0845-123-4567 to complete your order.</td>
  </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>