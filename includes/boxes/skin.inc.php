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
|	skin.inc.php
|   ========================================
|	Skin Jump Box
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

// include lang file
$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'skin.inc.php');

if ($config['changeskin']) {
	$box_content = new XTemplate ('boxes'.CC_DS.'skin.tpl');
	$box_content->assign('LANG_SKIN_SELECT',$lang['skin']['select']);

	$path = CC_ROOT_DIR.CC_DS.'skins';
	foreach (glob($path.CC_DS.'*') as $skinpath) {
		$folder = basename($skinpath);
		if (is_dir($skinpath) && !preg_match('#^\.#iuU', $folder)) {
			$selected = ($folder==SKIN_FOLDER) ? ' selected="selected"': '';

			if (file_exists($skinpath.CC_DS.'config.php')) {
				include $skinpath.CC_DS.'config.php';
			} else {
				$skinName = $folder;
			}

			$box_content->assign('SKIN_NAME', $skinName);
			$box_content->assign('SKIN_VAL', $folder);
			$box_content->assign('SKIN_SELECTED', $selected);

			$box_content->assign('VAL_CURRENT_PAGE', $returnPage);
			$box_content->parse('skin.option');
		}
	}
	$box_content->parse('skin');
	$box_content = $box_content->text('skin');
} else {
	$box_content = null;
}
?>