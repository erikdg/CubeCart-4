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
|	online.inc.php
|   ========================================
|	View Front Sessions
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$timeLimit = time() - 900;
$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_sessions LEFT JOIN '.$glob['dbprefix'].'CubeCart_customer ON '.$glob['dbprefix'].'CubeCart_sessions.customer_id = '.$glob['dbprefix'].'CubeCart_customer.customer_id WHERE timeLast>'.$timeLimit.' ORDER BY timeLast DESC';
// query database
$results = $db->select($query, $rowsPerPage, $page);
$numrows = $db->numrows($query);
?>
<p class='pageTitle'><?php echo $lang['admin']['stats_cust_online'];?></p>
<p class="copyText"><?php echo $lang['admin']['stats_cust_active'];?></p>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_hash'];?></td>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_customer'];?></td>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_location'];?></td>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_sess_start_time'];?></td>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_last_click_time'];?></td>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_last_ip_address'];?></td>
    <td nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['stats_sess_length'];?></td>
  </tr>
<?php
if($results)
{

	for($i = 0, $maxi = count($results); $i < $maxi; ++$i)
	{

		$rank = ($page * $rowsPerPage) + ($i + 1);

		$cellColor = cellColor($i);
?>

  <tr>
    <td class="<?php echo $cellColor; ?>" width="15"><span class="copyText"><?php echo $rank; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>" width="100" nowrap='nowrap'>
	<span class="copyText">
	<?php if($results[$i]['customer_id']==0){
	echo $lang['admin']['stats_geust'];
	} else {
	echo $results[$i]['title']." ".$results[$i]['firstName']." ".$results[$i]['lastName'];
	} ?>
	</span></td>
	<td class="<?php echo $cellColor; ?>" width="100" nowrap='nowrap'><a href="<?php echo $results[$i]['location']; ?>" class="txtLink"><?php echo $results[$i]['location']; ?></a></td>
    <td class="<?php echo $cellColor; ?>" nowrap='nowrap'><span class="copyText"><?php echo formatTime($results[$i]['timeStart']); ?></span></td>
	<td class="<?php echo $cellColor; ?>" nowrap='nowrap'><span class="copyText"><?php echo formatTime($results[$i]['timeLast']); ?></span></td>
    <td class="<?php echo $cellColor; ?>" nowrap='nowrap'>
    	<a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ip']; ?>','misc',300,120,'yes,resizable=yes')"><?php echo $results[$i]['ip']; ?></a>
    </td>
    <td class="<?php echo $cellColor; ?>" nowrap='nowrap'><span class="copyText"><?php echo sprintf('%.2f',($results[$i]['timeLast']-$results[$i]['timeStart'])/60); ?> <?php echo $lang['admin']['stats_mins'];?></span></td>
  </tr>
		<?php } 	} else { ?>
  <tr>
    <td colspan="3"><span class="copyText"><?php echo $lang['admin']['stats_sorry_no_data'];?></span></td>
  </tr>
  <?php } ?>
</table>
