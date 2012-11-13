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
|	logs.inc.php
|   ========================================
|	Logs admin actions
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_adminusers.inc.php');

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
$rowsPerPage = 50;
?>
<p class="pageTitle"><?php echo $lang['admin']['adminusers_admin_logs'];?></p>

<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['adminusers_id'];?></td>
    <td class="tdTitle"><?php echo $lang['admin']['adminusers_username2'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_admin_log'];?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_admin_log_time'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_admin_log_ip'];?></td>
  </tr>
<?php

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_admin_log ORDER BY `time` DESC';
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);

if($results){

	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

		$cellColor = '';
		$cellColor = cellColor($i);
?>
  <tr>
  	<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['id']; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['user']; ?></span></td>
	<td align="left" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['desc']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo formatTime($results[$i]['time']); ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ipAddress']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ipAddress']; ?></a></td>
  </tr>
<?php }
}
?>

</table>
<p class="copyText"><?php echo paginate($numrows, $rowsPerPage, $page, "page"); ?></p>