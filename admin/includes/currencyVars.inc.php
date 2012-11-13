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
|	currencyVars.inc.php
|   ========================================
|	Currency Vars
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
$query = sprintf('SELECT `value`, `symbolLeft`, `symbolRight`, `decimalPlaces`, `name` FROM %sCubeCart_currencies WHERE `code` = %s', $glob['dbprefix'], $db->mySQLSafe($config['defaultCurrency']));
$currencyVars = $db->select($query);
?>