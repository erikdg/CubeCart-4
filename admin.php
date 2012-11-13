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
|	admin.php
|   ========================================
|	Selects which encoding method to use
+--------------------------------------------------------------------------
*/

header('X-Frame-Options: SAME-ORIGIN');

require_once 'ini.inc.php';
require_once 'includes'.CC_DS.'global.inc.php';
require_once 'includes'.CC_DS.'functions.inc.php';

## If you are behind a proxy, please configure the fields below
## Examples below are for GoDaddy hosting
$glob['proxyEnable']= false;
$glob['proxyHost']	= ''; // e.g. proxy.shr.secureserver.net
$glob['proxyPort']	= ''; // e.g. 3128
$glob['proxyUser']	= ''; // leave this empty for godaddy
$glob['proxyPass']	= ''; // leave this empty for godaddy

## Load the encoded file
if (file_exists('admin_no_enc.php')) {
	require_once 'admin_no_enc.php';
} else {
	if ($glob['encoder'] == 'zend') {
		$php_version = (version_compare(PHP_VERSION, '5.3.0', '<')) ? '5.2' : '5.3';
		require 'admin_php'.$php_version.'_enc_zend.php';
	} else {
		require 'admin_enc_ion.php';
	}
}
?>
