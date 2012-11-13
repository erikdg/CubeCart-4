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
|	tracker.inc.php
|   ========================================
|	Tracking code for JROX.COM Affiliate Manager 	
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
$module = fetchDbConfig('JROX');

$affCode = "<!-- Begin JAM Affiliate Tracker -->
<img border='0' src='".$module['URL']."/sale.php?amount=".sprintf("%.2f", $orderSum['subtotal'])."&trans_id=".$cart_order_id."' width='0' height='0' alt='' />
<!-- End JAM Affiliate Tracker -->";
?>
