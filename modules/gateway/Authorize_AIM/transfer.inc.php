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
|	transfer.php
|   ========================================
|	Core functions for the Authorize_AIM Gateway
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
$formAction = 'index.php?_g=co&amp;_a=step3&amp;process=1&amp;cart_order_id='.$_GET['cart_order_id'];
$formMethod = 'post';
$formTarget = '_self';
$transfer = 'manual';
?>