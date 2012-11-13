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
|	transfer.inc.php
|   ========================================
|	Core Print Order Form Functions
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

function repeatVars()
{

		return false;

}

function fixedVars()
{

		return false;

}

///////////////////////////
// Other Vars
////////
$formAction = 'index.php?_g=cs&amp;_p='.urlencode('modules/gateway/Print_Order_Form/orderForm.inc.php').'&amp;cart_order_id='.$orderSum['cart_order_id'];
$formMethod = 'post';
$formTarget = '_self';
$transfer = 'auto';
?>