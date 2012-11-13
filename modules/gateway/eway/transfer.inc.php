<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Tuesday, 17th July 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	transfer.php
|   ========================================
|	Core functions for the eWay Gateway
+--------------------------------------------------------------------------
*/

function repeatVars(){

		return FALSE;

}

function fixedVars(){
	global $module, $orderSum, $config;

	return FALSE;
}

///////////////////////////
// Other Vars
////////
$formAction = '?_g=co&_a=step3&amp;process=1&amp;cart_order_id='.$_GET['cart_order_id'];
$formMethod = 'post';
$formTarget = '_self';
$transfer 	= 'manual';
?>