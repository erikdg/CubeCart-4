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
|	transLogs.inc.php
|   ========================================
|	Display Transaction Histories
+--------------------------------------------------------------------------
*/
if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_order_transactions.inc.php');
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

$rowsPerPage	= 50;
$page			= (isset($_GET['page'])) ? $_GET['page'] : 0;

if (isset($_GET['oid'])) {
	$query = sprintf('SELECT * FROM %sCubeCart_transactions WHERE `order_id` = %s ORDER BY `time` DESC', $glob['dbprefix'], $db->mySQLsafe($_GET['oid']));
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	?>
<p class="pageTitle"><?php echo $lang['admin']['transactions_title'];?> : <?php echo $results[0]['order_id']; ?></p>
&nbsp; <a href="?_g=orders/transLogs" class="txtLink">&laquo; <?php echo $lang['admin_common']['history_back']; ?></a> - <a href="?_g=orders/orderBuilder&edit=<?php echo $results[0]['order_id']; ?>" class="txtLink"><?php echo $lang['admin_common']['order_details']; ?> &raquo;</a><br />
<table width="100%" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
	<td align="center" class="tdTitle" width="160"><?php echo $lang['admin']['transactions_time'];?></td>
	<td align="center" class="tdTitle" width="180"><?php echo $lang['admin']['transactions_gateway'];?></td>
	<td align="center" class="tdTitle" width="180"><?php echo $lang['admin']['transactions_trans_id'];?></td>
	<td align="center" class="tdTitle" width="90"><?php echo $lang['admin']['transactions_status'];?></td>
	<td align="center" class="tdTitle" width="60"><?php echo $lang['admin']['transactions_amount'];?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['transactions_notes'];?></td>
  </tr>
<?php
	if ($results) {
		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
			$cellColor = cellColor($i);
?>
	<tr>
	  <td class="<?php echo $cellColor; ?> copyText"><?php echo formatTime($results[$i]['time']); ?></td>
	  <td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['gateway']; ?></td>
	  <td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['trans_id']; ?></td>
	  <td class="<?php echo $cellColor; ?> copyText"><strong><?php echo $results[$i]['status']; ?></strong></td>
	  <td align="right" class="<?php echo $cellColor; ?> copyText" nowrap="nowrap"><?php echo $results[$i]['amount']; ?></td>
	  <td class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['notes']; ?></td>
	</tr>
<?php
		}
	}
} else {
?>
<p class="pageTitle"><?php echo $lang['admin']['transactions_title'];?></p>
<form method="get" enctype="text/plain">
<input type="hidden" name="_g" value="orders/transLogs" />
<input type="text" name="searchKey" value="" class="textbox" /> <input type="submit" value="Search" class="submit" />
</form>
<br />
<table cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
	<td align="center" nowrap="nowrap" width="200" class="tdTitle"><?php echo $lang['admin']['transactions_order_id'];?> / <?php echo $lang['admin']['transactions_trans_id'];?></td>
	<td align="center" nowrap="nowrap" width="60" class="tdTitle"><?php echo $lang['admin']['transactions_amount'];?></td>
	<td align="center" nowrap="nowrap" width="180" class="tdTitle"><?php echo $lang['admin']['transactions_gateway'];?></td>
	<td align="center" nowrap="nowrap" width="160" class="tdTitle"><?php echo $lang['admin']['transactions_time'];?></td>
  </tr>
<?php
if (!empty($_GET['searchKey'])) {
	$sql	= sprintf("SELECT * FROM %1\$sCubeCart_transactions WHERE (`order_id` LIKE '%%%2\$s%%' OR `trans_id` LIKE '%%%2\$s%%')", $glob['dbprefix'], $db->mySQLsafe($_GET['searchKey'], ''));
	if ($db->numrows($sql) >= 1) {
		$results = $db->select($sql, $rowsPerPage, $page);
		$numrows = $db->numrows($sql);
		$search = true;
	}
}

if (!$search) {
	$query = sprintf("SELECT DISTINCT `order_id`, `time`, `amount`, `gateway`, `trans_id` FROM %1\$sCubeCart_transactions GROUP BY `order_id` ORDER BY `time` DESC", $glob['dbprefix']);
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
}

if ($results) {
	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
		$cellColor = cellColor($i);
?>
  <tr>
	<td align="left" class="<?php echo $cellColor; ?> copyText">
	<a href="?_g=orders/transLogs&oid=<?php echo $results[$i]['order_id']; ?>" class="txtLink"><?php echo $results[$i]['order_id']; ?></a><br />
	<?php echo $results[$i]['trans_id']; ?>&nbsp;
	</td>
	<td align="center" class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['amount']; ?></td>
	<td align="center" class="<?php echo $cellColor; ?> copyText"><?php echo $results[$i]['gateway']; ?></td>
	<td align="center" class="<?php echo $cellColor; ?> copyText"><?php echo formatTime($results[$i]['time']); ?></td>
  </tr>
<?php
	}
} else {
	?>
  <tr>
	<td class="tdText" colspan="9"><?php echo $lang['admin']['transactions_no_transactions'];?></td>
  </tr>
<?php } ?>
</table>
<?php } ?>
<p class="copyText"><?php echo paginate($numrows, $rowsPerPage, $page, "page"); ?></p>