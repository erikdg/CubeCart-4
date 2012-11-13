<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|   Devellion Limited,
|   13 Ducketts Wharf,
|   South Street,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3AR
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: info (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	tracker.inc.php
|   ========================================
|	Tracking code for Aflite
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$module = fetchDbConfig('aflite');

$testVar = ($module['testMode'] == 1) ? '&trace=1' : '';

$affCode = "<!-- Begin Aflite Affiliate Tracker -->
<img src='http://aflite.co.uk/modules/track/goal.php?value=".sprintf("%.2f",$orderSum['subtotal'])."&ref=".$cart_order_id."&mid=".$module['mid']."&goalid=".$module['goalid'].$testVar"' width='0' height='0' alt='' />
<!-- End Aflite Affiliate Tracker -->";
?>