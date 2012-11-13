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
|	sessions.inc.php
|   ========================================
|	Lists last x amount of admin logins
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_adminusers.inc.php');
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
$rowsPerPage = 50;
?>
<p class="pageTitle"><?php echo $lang['admin']['adminusers_admin_sessions']; ?></p>

<p class="copyText"><?php echo $lang['admin']['adminusers_sessions_desc']; ?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['adminusers_login_id']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['adminusers_username']; ?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_time']; ?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_ip_address']; ?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_success']; ?></td>
  </tr>
<?php

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_admin_sessions ORDER BY `time` DESC';
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);

if($results){

	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

		$cellColor = '';
		$cellColor = cellColor($i);
?>
  <tr>
  	<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['loginId']; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['username']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo formatTime($results[$i]['time']); ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ipAddress']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ipAddress']; ?></a></td>
	    <td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['success']; ?>.gif" alt="" title="" /></td>
  </tr>
<?php }
}
?>

</table>
<p class="copyText"><?php echo paginate($numrows, $rowsPerPage, $page, 'page'); ?></p>