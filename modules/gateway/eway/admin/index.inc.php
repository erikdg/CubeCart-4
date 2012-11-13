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
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	index.inc.php
|   ========================================
|	Configure Protx
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

##### Include these two line at the beginning of all your module's files #####
require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'common.inc.php';
$modLoad = @new ModuleLoader(__FILE__, $config['defaultLang'], $language, $moduleName);

if(!isset($_GET['mode']) && isset($modLoad->settings['mode']) && !empty($modLoad->settings['mode'])) {
	$_GET['mode'] = $modLoad->settings['mode'];
} elseif(!isset($_GET['mode'])) {
	$_GET['mode'] = "AU";
}
##############################################################################
?>
<?php if ($modLoad->message) echo msg($modLoad->message); ?>

<p><a href="http://www.eway.com.au/"><img src="modules/<?php echo $moduleType; ?>/<?php echo $moduleName; ?>/admin/logo.gif" alt="" border="0" title="" /></a></p>

<p class="copyText">&quot;e-commerce the easy way.&quot;</p>

<form action="?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>&amp;mode=<?php echo $_GET['mode']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Configuration Settings </td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Mode:</strong></td>
    <td class="tdText">
	<select name="null" onchange="jumpMenu('parent',this,0)">
		<option value="?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>&amp;mode=AU" <?php if($_GET['mode']=="AU") echo "selected='selected'"; ?>>eWay Australia</option>
		<option value="?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>&amp;mode=NZ" <?php if($_GET['mode']=="NZ") echo "selected='selected'"; ?>>eWay New Zealand (Hosted Payment Page)</option>
		<option value="?_g=<?php echo $_GET['_g']; ?>&amp;module=<?php echo $_GET['module']; ?>&amp;mode=UK" <?php if($_GET['mode']=="UK") echo "selected='selected'"; ?>>eWay United Kingdom (Hosted Payment Page)</option>
    </select>
	</td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Status:</strong></td>
    <td class="tdText">
	<select name="module[status]">
		<option value="1" <?php if($modLoad->settings['status']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($modLoad->settings['status']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>
	</td>
  </tr>
  <?php if($_GET['mode']=="AU") { ?>
  <tr>
    <td align="left" class="tdText"><strong>Enable Card Validation:</strong></td>
    <td class="tdText">
	<select name="module[validation]">
		<option value="1" <?php if($modLoad->settings['validation']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($modLoad->settings['validation']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Merchant Id:</strong></td>
    <td class="tdText"><input type="text" name="module[acNo]" value="<?php echo $modLoad->settings['acNo']; ?>" class="textbox" size="30" /></td>
  </tr>
  <?php } else { ?>
  <tr>
  <td align="left" class="tdText"><strong>Customer Id:</strong></td>
    <td class="tdText"><input type="text" name="module[customerid]" value="<?php echo $modLoad->settings['customerid']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Customer Name:</strong></td>
    <td class="tdText"><input type="text" name="module[customername]" value="<?php echo $modLoad->settings['customername']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Company Logo (SSL Required!):</strong><br />(Max Size 960px X 65px)</td>
    <td class="tdText"><input type="text" name="module[companylogo]" value="<?php echo (!empty($modLoad->settings['companylogo'])) ? $modLoad->settings['companylogo'] : $GLOBALS['storeURL']."/images/getLogo.php?skin=".$config['skinDir']; ?>" class="textbox" size="60" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Page Banner (SSL Required!):</strong><br />(Max Size 960px X 65px)</td>
    <td class="tdText"><input type="text" name="module[pagebanner]" value="<?php echo $modLoad->settings['pagebanner']; ?>" class="textbox" size="60" /></td>
  </tr>
  <?php } ?>
  <tr>
    <td align="left" class="tdText"><strong>Test Mode:</strong></td>
    <td class="tdText">
	<select name="module[test]">
		<option value="1" <?php if($modLoad->settings['test']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($modLoad->settings['test']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>	</td>
  </tr>
   <tr>
  	<td align="left" class="tdText"><strong>Description:</strong>
	</td>
    <td class="tdText"><input type="text" name="module[desc]" value="<?php echo $modLoad->settings['desc']; ?>" class="textbox" size="30" /></td>
  </tr>
  
  <td align="left" class="tdText"><strong>Default:</strong></td>
      <td class="tdText">
	<select name="module[default]">
		<option value="1" <?php if($modLoad->settings['default'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($modLoad->settings['default'] == 0) echo "selected='selected'"; ?>>No</option>
	</select>
	</td>
  </tr>
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText">
    <input type="hidden" name="module[mode]" value="<?php echo $_GET['mode']; ?>" />
    <input type="submit" class="submit" value="Edit Config" />
    </td>
  </tr>
</table>
</form>