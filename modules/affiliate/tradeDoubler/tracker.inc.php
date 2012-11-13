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
|	tracker.inc.php
|   ========================================
|	Tracking code for tradeDoubler
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
$module = fetchDbConfig('tradeDoubler');

$testVar = ($module['testMode'] == 1) ? '&testonly=1' : '';

$affCode = "<!-- Begin tradeDoubler Affiliate Tracker -->
<img src='http://www.awin1.com/sale.php?sale=".sprintf("%.2f",$orderSum['subtotal'])."&extra=".$cart_order_id."&type=s&mid=".$module['acNo'].$testVar."' width='0' height='0' alt='' />
<!-- End tradeDoubler Affiliate Tracker -->";
?>