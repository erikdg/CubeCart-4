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
|	extra.inc.php
|   ========================================
|	Extra Thingamybobs
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
switch (sanitizeVar($_GET['_a'])) {
	case 'prodImages':
		require_once 'includes'.CC_DS.'extra'.CC_DS.'prodImages.inc.php';
		break;
}
?>