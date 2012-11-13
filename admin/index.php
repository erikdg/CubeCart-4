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
|	index.php
|   ========================================
|	Admin Folder Protection
+--------------------------------------------------------------------------
*/
include '..'.DIRECTORY_SEPARATOR.'ini.inc.php';
include '..'.CC_DS.'includes'.CC_DS.'global.inc.php';

if (file_exists('..'.CC_DS.'admin.php')) {
	header('location: ../admin.php');
} else {
	header('HTTP/1.1 404 Not Found');
	header('HTTP/1.0 404 Not Found');
	header('Status: 404 Not Found');
}
?>
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL <?php echo $_SERVER['REQUEST_URI']; ?> was not found on this server.</p>
<hr>
<?php echo $_SERVER['SERVER_SIGNATURE']; ?>
</body></html>