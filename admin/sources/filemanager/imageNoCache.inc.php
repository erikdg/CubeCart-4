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
|	imageNoCache.inc.php
|   ========================================
|	Preview Image
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
$skipFooter = 1;

require('classes'.CC_DS.'gd'.CC_DS.'gd.inc.php');

$imagePath = ($glob['rootRel'] != CC_DS) ? str_replace($glob['rootRel'], '', $_GET['file']) : $_GET['file'];
$imagePath = CC_ROOT_DIR.CC_DS.$imagePath;

$img = new gd($imagePath);
$img->show(1);
?>