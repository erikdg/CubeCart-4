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
|	index.inc.php
|   ========================================
|	Manage Orders
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');

$lang = getLang('admin'.CC_DS.'admin_orders.inc.php');
$lang = getLang('orders.inc.php');

permission('orders', 'read', true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

// delete document
if(isset($_GET['delete']) && $_GET['delete']==true) {

	$cache = new cache();
	$cache->clearCache();

	$record['noOrders'] = 'noOrders - 1';
	$where = '`customer_id` = '.$_GET['customer'];
	$update = $db->update($glob['dbprefix'].'CubeCart_customer', $record, $where);

	$where = '`cart_order_id` = '.$db->mySQLSafe($_GET['delete']);

	$delete = $db->delete($glob['dbprefix'].'CubeCart_order_sum', $where);

	if ($delete) {
		$msg = "<p class='infoText'>".$lang['admin']['orders_delete_success']."</p>";
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['orders_delete_fail']."</p>";
	}

	$delete = $db->delete($glob['dbprefix'].'CubeCart_order_inv', $where);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_Coupons', $where);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_Downloads', $where);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_transactions', '`order_id` = '.$db->mySQLSafe($_GET['delete']));

}


$sqlQuery = '';

if(isset($_GET['status'])){
	$sqlQuery = 'WHERE '.$glob['dbprefix'].'CubeCart_order_sum.status = '.$db->mySQLsafe($_GET['status']);
} elseif(isset($_GET['oid'])) {
	if(empty($_GET['oid'])) {
	 	# Show all
		$sqlQuery = '';
	} else {
		$sqlQuery = 'WHERE cart_order_id = '.$db->mySQLsafe($_GET['oid']);
	}
} elseif(isset($_GET['customer_id']) && $_GET['customer_id']>0 && !isset($_GET['delete'])) {
	$sqlQuery = 'WHERE '.$glob['dbprefix'].'CubeCart_customer.customer_id = '.$db->mySQLsafe($_GET['customer_id']);
}


// query database
if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}

$ordersPerPage = 25;

$query = "SELECT ".$glob['dbprefix']."CubeCart_customer.customer_id, ".$glob['dbprefix']."CubeCart_order_sum.status, `cart_order_id`, `time`, `title`, `firstName`, `lastName`, `ip`, `prod_total`, ".$glob['dbprefix']."CubeCart_customer.email FROM ".$glob['dbprefix']."CubeCart_order_sum INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_order_sum.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id ".$sqlQuery." ORDER BY `time` DESC";

$results = $db->select($query, $ordersPerPage, $page);
$numrows = $db->numrows($query);
$exclude		= array('delete' => 1);
$pagination = paginate($numrows, $ordersPerPage, $page, "page", 'txtLink', 10, $exclude);
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle"><?php echo $lang['admin']['orders_orders']; ?></td>
     <?php if(!isset($_GET['mode'])){ ?><td align="right" valign="middle"><a <?php if(permission('orders','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=orders/orderBuilder" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" /><?php echo $lang['admin_common']['add_new'];?></a></td><?php } ?>
  </tr>
</table>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
<p class="copyText"><?php echo $lang['admin']['orders_all_orders']; ?></p>
<p style="text-align:right" class="copyText"><?php echo $lang['admin']['orders_filter']; ?>
<select name="status" class="dropDown" onchange="jumpMenu('parent',this,0)">

		<option value="<?php echo $glob['adminFile']; ?>?_g=orders/index">-- <?php echo $lang['admin_common']['all']; ?> --</option>
		<?php
		for($i=1; $i<=6; ++$i)
		{
		?>
		<option value="<?php echo $glob['adminFile']; ?>?_g=orders/index&amp;status=<?php echo $i; ?>" <?php if($_GET['status']==$i) { echo 'selected="selected"'; } ?>><?php echo $lang['glob']['orderState_'.$i]; ?></option>
		<?php
		}
		?>

</select>
</p>
<p class="copyText"><?php echo $pagination; ?></p>
<table border="0" width="100%" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle"><?php echo $lang['admin']['orders_order_no']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['orders_status']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['orders_date_time']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['orders_customer']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['orders_ip_address']; ?></td>
    <td class="tdTitle"><?php echo $lang['admin']['orders_cart_total']; ?></td>
    <td class="tdTitle" align="center"><?php echo $lang['admin']['orders_action']; ?></td>
  </tr>
  <?php
  if($results){

  	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

	$cellColor = '';
	$cellColor = cellColor($i);

  ?>
  <tr>
    <td class="<?php echo $cellColor; ?>"><a href="<?php echo $glob['adminFile']; ?>?_g=orders/orderBuilder&amp;edit=<?php echo $results[$i]['cart_order_id']; ?>" class="txtLink"><?php echo $results[$i]['cart_order_id']; ?></a></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php
	echo $lang['glob']['orderState_'.$results[$i]['status']];
	?></span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo formatTime($results[$i]['time']); ?></span></td>
    <td class="<?php echo $cellColor; ?>"><a href="<?php echo $glob['adminFile']; ?>?_g=customers/index&amp;searchStr=<?php echo urlencode($results[$i]['email']); ?>" class="txtLink"><?php echo $results[$i]['title']." ".$results[$i]['firstName']." ".$results[$i]['lastName']; ?></a></td>
    <td class="<?php echo $cellColor; ?>"><a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ip']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ip']; ?></a></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo priceFormat($results[$i]['prod_total'],true); ?></span></td>

    <td align="center" class="<?php echo $cellColor; ?>">
	<a <?php if(permission('orders','delete')){ ?>href="javascript:decision('<?php echo $lang['admin_common']['delete_q']; ?>','<?php echo $glob['adminFile']; ?>?_g=orders/index&amp;delete=<?php echo $results[$i]['cart_order_id']; ?>&customer=<?php echo $results[$i]['customer_id']; ?>');" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['delete']; ?></a>
	<br />
	<a <?php if(permission('orders','edit')){ ?>href="<?php $glob['adminFile']; ?>?_g=orders/orderBuilder&amp;edit=<?php echo $results[$i]['cart_order_id']; ?>" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['edit']; ?></a>


	</td>
  </tr>
  <?php } // end loop
  } else { ?>
   <tr>
    <td colspan="7" class="tdText"><?php echo $lang['admin']['orders_no_orders_in_db']; ?></td>
  </tr>
  <?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>