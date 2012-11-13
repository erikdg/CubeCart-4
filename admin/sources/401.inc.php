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
|	401.inc.php
|   ========================================
|	Admin Access Denied Page
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
?>
<p class="warnText"><?php echo $lang['admin_common']['other_401']; ?></p>
<p align="center"><img src="images/largeWarning.gif" alt="" width="220" height="192" title="" /></p>