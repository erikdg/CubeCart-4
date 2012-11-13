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
|	List Modules
+--------------------------------------------------------------------------
*/


if (!defined('CC_INI_SET')) die("Access Denied");

if(isset($_GET['module'])) {
	switch($_GET['module']) {
		case 'shipping':
			$section = 'shipping';
		break;
		case 'gateway':
			$section = 'gateway';
		break;
		default:
			$section = 'settings';
	}
}
permission($section, "read", true);

$lang = getLang('admin'.CC_DS.'admin_misc.inc.php');

require $glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php';
?>
<p class="pageTitle"><?php echo ucfirst($module); ?> Modules</p>

<table border="0" cellspacing="1" cellpadding="3" align="center" class="mainTable">
  <tr>
	<td class="tdTitle"><?php echo $lang['admin']['misc_module_name']; ?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_module_action']; ?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_module_status']; ?></td>
  </tr>
<?php

## New Module Loader

$modulePath = CC_ROOT_DIR.CC_DS.'modules'.CC_DS.$module;
$moduleList = listAddons($modulePath);

if (is_array($moduleList)) {
	$i = 0;
	foreach ($moduleList as $moduleDir) {
		$cellColor = cellColor($i);
		if (file_exists($modulePath.CC_DS.$moduleDir.CC_DS.'admin'.CC_DS.'index.inc.php')) {
			++$i;
			## Display the module
			$moduleStatus	= fetchDbConfig($moduleDir);
			$moduleName		= str_replace('_', ' ', $moduleDir);

			if (file_exists($modulePath.CC_DS.$moduleDir.CC_DS.'admin'.CC_DS.'logo.gif')) {
				$logo = sprintf('<img src="modules/%s/%s/admin/logo.gif" alt="%s" />', $module, $moduleDir, $moduleName);
			} else {
				$logo = $moduleName;
			}

			$statusImage = sprintf('<img src="images/admin/%d.gif" width="10" height="10" alt="" />', $moduleStatus['status']);
			$configLink = "?_g=modules&amp;module=$module/$moduleDir";
?>
	<tr>
	  <td align="left" valign="top" class="<?php echo $cellColor; ?>">
	  	<a href="<?php echo $configLink; ?>" class="txtLink"><?php echo $logo; ?></a>
	  </td>
	  <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
		<a href="<?php echo $configLink; ?>" class="txtLink">Configure</a>
	  </td>
	  <td align="center" valign="middle" class="<?php echo $cellColor; ?>">
		<?php echo $statusImage; ?>
	  </td>
	</tr>
<?php
		}
	}
}
?>
</table>