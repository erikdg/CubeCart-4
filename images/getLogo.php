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
|	getLogo.php
|   ========================================
|	Get Custom Logo if there is one	
+--------------------------------------------------------------------------
*/

define('CC_DS', DIRECTORY_SEPARATOR);

if (isset($_GET['skin']) && !empty($_GET['skin'])) {
	
	$custom		= 'logos'.CC_DS.str_replace(array('/', '\\'), '', $_GET['skin']);
	$default	= '..'.CC_DS.'skins'.CC_DS.$_GET['skin'].CC_DS.'styleImages'.CC_DS.'logo'.CC_DS.'default.gif';
	
	if (!empty($_GET['skin']) && file_exists($custom)) {
		$filename	= $custom;
	} else if (file_exists($default)) {
		$filename	= $default;
	}
	
	if (isset($filename)) {
		$file = getimagesize($filename);
		switch ($file[2]) {
			case 1:
				$mime = 'gif';
				break;
			case 2:
				$mime = 'jpeg';
				break;
			case 3:
				$mime = 'png';
				break;
			default:
				exit;
		}
		header('Content-Disposition: inline; filename="logo.'.$mime.'"');
		header('Content-Type: image/'.$mime);
		header('Content-Length: '.filesize($filename));
		echo file_get_contents($filename);
	}
}
?>