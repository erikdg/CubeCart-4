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
|	functions.inc.php
|   ========================================
|	Installer Functions
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
function keySearch($find, $array, $keyname = null) {
	foreach ($array as $key => $arrayVal) {
		if (is_array($arrayVal)) {
			$result = keySearch($find, $arrayVal, $key);
			if ($result != false) return $result;
		} else {
			if (strtolower($arrayVal) == strtolower($find)) {
				return (!empty($keyname)) ? $keyname : $key;
			}
		}
	}
	return false;
}

?>