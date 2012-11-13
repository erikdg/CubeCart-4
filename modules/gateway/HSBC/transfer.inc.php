<?php

/************************************************
* HSBC API Module by Adam Harris @ XOMY Limited *
* http://www.xomy.com | adam@xomy.com           *
* ---------------------------------------------	*
* Upgraded for CubeCart 4 by Martin Purcell		*
* ---------------------------------------------	*
*                                               *
* Before making any modifications, please       *
* contact me at the above email so that we can  *
* discuss the implications and advantages for   *
* the module.                                   *
*                                               *
* This module is released for the benefit of    *
* the community and should not be sold.         *
*                                               *
* This module is not released under GPL and     *
* cannot be redistributed without permission    *
* from myself.                                  *
************************************************/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$module = fetchDbConfig('HSBC');

function repeatVars() {
	return false;
}

function fixedVars() {
	return false;
}

function success() {
	global $basket;
	return ($_GET['f']==1) ? false : true;
}

///////////////////////////
// Other Vars
////////
$formAction = 'index.php?_g=co&amp;_a=step3&amp;process=1&amp;cart_order_id='.$_GET['cart_order_id'];
$formMethod = 'post';
$formTarget = '_self';
$transfer = 'manual';
$stateUpdate = true;
?>
