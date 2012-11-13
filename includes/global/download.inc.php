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
|	download.inc.php
|   ========================================
|	Gathers the customers digital download
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");


$query		= 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_Downloads INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON '.$glob['dbprefix'].'CubeCart_Downloads.productId =  '.$glob['dbprefix'].'CubeCart_inventory.productId WHERE cart_order_id = '.$db->mySQLSafe(base64_decode($_GET['oid'])).' AND '.$glob['dbprefix'].'CubeCart_Downloads.productId = '.$db->mySQLSafe($_GET['pid']).' AND accessKey = '.$db->mySQLSafe($_GET['ak']).' AND noDownloads<'.$config['dnLoadTimes'].' AND  expire>'.time();
$download	= $db->select($query);

require_once 'classes'.CC_DS.'cart'.CC_DS.'order.php';
$order = new order();
$order_status = $order->getOrderStatus($download[0]['cart_order_id']);

if ($download && ($order_status == 2 || $order_status ==3 )) {
	if (strstr($download[0]['digitalDir'], 'ftp://') || strstr($download[0]['digitalDir'], 'http://') || strstr($download[0]['digitalDir'], 'https://')) {
		$record['noDownloads']	= 'noDownloads + 1';

		$where					= '`cart_order_id` = '.$db->mySQLSafe(base64_decode($_GET['oid'])).' AND `productId` = '.$db->mySQLSafe($_GET['pid']).' AND `accessKey` = '.$db->mySQLSafe($_GET['ak']);
		$update					= $db->update($glob['dbprefix'].'CubeCart_Downloads', $record, $where);
		httpredir($download[0]['digitalDir']);
	} else {

		$record['noDownloads']	= 'noDownloads + 1';

		$where		= '`cart_order_id` = '.$db->mySQLSafe(base64_decode($_GET['oid'])).' AND '.$glob['dbprefix'].'CubeCart_Downloads.productId = '.$db->mySQLSafe($_GET['pid']).' AND `accessKey` = '.$db->mySQLSafe($_GET['ak']);
		$update		= $db->update($glob['dbprefix'].'CubeCart_Downloads', $record, $where);

		## Close the session to allow for header() to be sent
		session_write_close();

		if (deliverFile($download[0]['digitalDir'])) {
			exit;
		} else {
			die ('There was an error dowloading the file. Please contact a member of support for help.');
		}
	}
} else {
	httpredir('index.php?_g=co&_a=error&code=10002');
}
?>