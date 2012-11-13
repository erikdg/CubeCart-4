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
|	Main Homepage of Admin
+--------------------------------------------------------------------------
*/
if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_misc.inc.php');
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

// no Products
$query = 'SELECT count(`productId`) as noProducts FROM '.$glob['dbprefix'].'CubeCart_inventory';
$noProducts = $db->select($query);

// no Categories
$query		= sprintf("SELECT COUNT(S.cart_order_id) as noOrders FROM %1\$sCubeCart_order_sum AS S, %1\$sCubeCart_customer AS C WHERE C.customer_id = S.customer_id", $glob['dbprefix']);
$noOrders	= $db->select($query);

// no Customers
$query = 'SELECT count(`customer_id`) as noCustomers FROM '.$glob['dbprefix'].'CubeCart_customer WHERE `type` = 1 OR `type` = 2';
$noCustomers = $db->select($query);

// last admin session
$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_admin_sessions ORDER BY `time` DESC LIMIT 1, 1';
$lastSession = $db->select($query);

$_GET['po'] = (!isset($_GET['po'])) ? '' : $_GET['po'];
$_GET['rev'] = (!isset($_GET['rev'])) ? '' : $_GET['rev'];

## check if setup folder remains after install/upgrade
if ($glob['installed'] && !$config['debug'] && file_exists(CC_ROOT_DIR.'/setup')) {
	echo sprintf('<p class="warnText">%s</p>', $lang['admin_common']['setup_folder_exists']);
}
@chmod('includes'.CC_DS.'global.inc.php',0444);
if (substr(PHP_OS, 0, 3) != 'WIN' && cc_is_writable('includes'.CC_DS.'global.inc.php')) {
	echo sprintf('<p class="warnText">%s</p>', $lang['admin_common']['other_global_risk']);
}

## check if setup folder remains after install/upgrade
if ($glob['dbusername'] == 'root') {
	echo sprintf('<p class="warnText">%s</p>', 'WARNING: You are currently connected to the MySQL database using the root account. This is very insecure, and should be changed if possible.');
}
if($key->key_data['expiry']>1){
	echo sprintf('<p class="warnText">%s</p>', 'WARNING: This store is currently using a trial software license key that is set to expire on '.formatTime($key->key_data['expiry']).'. If you have purchased a full software license key please be sure to edit the includes/global.inc.php file accordingly. <a href="https://support.cubecart.com/index.php?_m=knowledgebase&_a=viewarticle&kbarticleid=61&nav=0,17">More Information</a>');
}
?>
<p class="pageTitle"><?php echo $lang['admin_common']['other_welcome_note']; ?></p>
<?php
if ($lastSession) {
	$loginTime = formatTime($lastSession[0]['time']);
	if ($lastSession[0]['success']) {
		echo "<p class='infoText'>".sprintf($lang['admin_common']['other_last_login_success'], strip_tags($lastSession[0]['username']), $loginTime)."</p>";
	} else {
		echo "<p class='warnText'>".sprintf($lang['admin_common']['other_last_login_failed'], strip_tags($lastSession[0]['username']), $loginTime)."</p>";
	}
}
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="50%" valign="top" style="padding-right: 5px;">
	<?php
	$query = "SELECT `cart_order_id`, `name`, `time`, ".$glob['dbprefix']."CubeCart_order_sum.customer_id FROM ".$glob['dbprefix']."CubeCart_order_sum INNER JOIN ".$glob['dbprefix']."CubeCart_customer ON ".$glob['dbprefix']."CubeCart_order_sum.customer_id = ".$glob['dbprefix']."CubeCart_customer.customer_id WHERE ".$glob['dbprefix']."CubeCart_order_sum.status = 1 OR ".$glob['dbprefix']."CubeCart_order_sum.status = 2 ORDER BY `time` DESC";

	$poPerPage = 12;
	$pendingOrders = $db->select($query, $poPerPage, $_GET['po']);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $poPerPage, $_GET['po'], 'po');

	if ($pendingOrders) {
	?>

	<table width="100%" border="0" cellpadding="3" cellspacing="1" class="toDoTable">
  <tr>
    <td width="50%" class="tdtoDo"><?php echo $lang['admin_common']['other_pending_orders']; ?></td>
  </tr>
  <tr>
    <td width="50%" rowspan="3" align="left" valign="top" class="tdText"><?php

		for ($i = 0, $maxi = count($pendingOrders); $i < $maxi; ++$i) {
			echo "<a href='".$GLOBALS['rootRel'].$glob['adminFile']."?_g=orders/orderBuilder&amp;edit=".$pendingOrders[$i]['cart_order_id']."' class='txtDash'>".$pendingOrders[$i]['cart_order_id']."</a> - <a href='".$glob['adminFile']."?_g=customers/index&amp;edit=".$pendingOrders[$i]['customer_id']."' class='txtDash'>".$pendingOrders[$i]['name']."</a><br />(".formatTime($pendingOrders[$i]['time']).")  <br />";
		}
		echo $pagination;
	?>
    </td>
  </tr>
</table>
<br />
<?php
}
?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
	  <tr>
	    <td colspan="2" class="tdTitle"><?php echo $lang['admin_common']['other_quick_search']; ?></td>
    </tr>
	  <tr>
	    <td><span  class="tdText"><?php echo $lang['admin_common']['other_order_no']; ?></span></td>
      <td>
  <form name="orderSearch" method="get" action="<?php echo $glob['adminFile']; ?>">
<input type="hidden" name="_g" value="orders/index" />
  <input name="oid" type="text" class="textbox" size="30" <?php if(!permission('orders','read')) { echo 'disabled="disabled"'; } ?> />
  <input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin_common']['other_search_now']; ?>" <?php if(!permission('orders','read')) { echo 'disabled="disabled"'; } ?> />
  </form></td>
    </tr>
	  <tr>
	    <td><span class="tdText"><?php echo $lang['admin_common']['other_customer']; ?></span></td>
      <td>
  <form name="customerSearch" method="get" action="<?php echo $glob['adminFile']; ?>">
  <input type="hidden" name="_g" value="customers/index" />
  <input name="searchStr" type="text" class="textbox" id="searchStr" size="30" <?php if(!permission('customers','read')) { echo 'disabled="disabled"'; } ?> />
  <input name="search" type="submit" class="submit" id="search" value="<?php echo $lang['admin_common']['other_search_now']; ?>" <?php if(!permission('customers','read')) { echo 'disabled="disabled"'; } ?> />
  </form></td>
    </tr>
    </table>
	<br />
    <table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
      <tr>
        <td class="tdTitle"><?php echo $lang['admin_common']['other_announcements']; ?></td>
    </tr>

<?php
if (isset($config['latestNewsRRS']) && !empty($config['latestNewsRRS'])) {
	## include MagpieRSS to Parse News RSS into Array
	require('classes'.CC_DS.'magpie'.CC_DS.'rss_fetch.inc');
	$rss = fetch_rss(str_replace('&amp;','&',$config['latestNewsRRS']));

	if(is_array($rss->items)) {
		foreach ($rss->items as $item ) {
			echo "<tr><td class=\"tdText\"><a href=\"".$item['link']."\" target=\"_blank\" class=\"txtLink\">".$item['title']."</a></td></tr>";
		}
	} else {
		echo "<tr><td class=\"tdText\">".$lang['admin_common']['other_no_announcements']."</td></tr>";
	}
} else {
	echo "<tr><td class=\"tdText\">".$lang['admin_common']['other_no_announcements']."</td></tr>";
}
?>
      </table>
<?php if (!isset($config['lk']) || empty($config['lk'])) { ?>
    <br />
    <iframe src="<?php echo $glob['adminFile']; ?>?_g=misc/licForm" style="border: none; margin: 0px; overflow: hidden; padding: 0px; <?php if(strpos(@$_SERVER['HTTP_USER_AGENT'],"MSIE") !== false){ echo "width: 99%"; } else { ?>width: 100%<?php } ?>;" frameborder="0"></iframe>
<?php } ?>

</td>
<td width="50%" valign="top" style="padding-left: 5px;">
<?php
$query = sprintf("SELECT R.id, R.name, R.time FROM %1\$sCubeCart_reviews AS R RIGHT JOIN %1\$sCubeCart_inventory as I ON R.productId = I.productId WHERE R.approved = 0 ORDER BY time ASC", $glob['dbprefix']);
$reviewsPerPage = 5;
$reviews = $db->select($query, $reviewsPerPage, $_GET['rev']);
$numrows = $db->numrows($query);
$pagination = paginate($numrows, $reviewsPerPage, $_GET['rev'], 'rev');

if ($reviews) {
?>
  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="toDoTable">
  <tr>
    <td width="50%" class="tdtoDo"><?php echo $lang['admin_common']['other_product_reviews']; ?></td>
  </tr>
  <tr>
    <td width="50%" align="left" valign="top" class="tdText">
<?php
	for ($i = 0, $maxi = count($reviews); $i < $maxi; ++$i) {
		echo "<a href='".$glob['adminFile']."?_g=reviews/index&amp;edit=".$reviews[$i]['id']."' class='txtDash'>".$reviews[$i]['name']."</a> (".formatTime($reviews[$i]['time']).")<br />";
	}
	echo $pagination;
?>
	</td>
  </tr>
  </table><br />
<?php
}

if ($config['stock_warn_type'] == 1) {
	$query = "SELECT `name`, `stock_level`, `productId` FROM ".$glob['dbprefix']."CubeCart_inventory WHERE `useStockLevel` = 1 AND `stock_level` <= `stockWarn` ORDER BY `stock_level` ASC";
} else {
	if (!is_numeric($config['stock_warn_level'])) $config['stock_warn_level'] = 5;
	$query = "SELECT `name`, `stock_level`, `productId` FROM ".$glob['dbprefix']."CubeCart_inventory WHERE `useStockLevel` = 1 AND `stock_level` <= ".$config['stock_warn_level']." ORDER BY `stock_level` ASC";
}

$stockPerPage = 20;
$stock = $db->select($query, $stockPerPage, $_GET['po']);
$numrows = $db->numrows($query);
$pagination = paginate($numrows, $stockPerPage, $_GET['po'], 'po');

if ($stock) {
?>
  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="toDoTable">
	<tr>
	  <td width="50%" align="left" valign="top" class="tdtoDo"><?php echo $lang['admin_common']['other_stock_warnings'];?></td>
	</tr>
	<tr>
	  <td width="50%" align="left" valign="top" class="tdText">
	<?php
	for ($i = 0, $maxi = count($stock); $i < $maxi; ++$i) {
		echo " <a href='".$glob['adminFile']."?_g=products/index&amp;edit=".$stock[$i]['productId']."' class='txtDash'>".$stock[$i]['name']."</a> (".$stock[$i]['stock_level'].")<br />";
	}
	echo $pagination;
	?>
	  </td>
	</tr>
  </table>
  <br />
<?php } ?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin_common']['other_store_overview']; ?></td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><a href="http://www.cubecart.com" target="_blank" class="txtLink">CubeCart</a> <?php echo $lang['admin_common']['other_version']; ?></td>
    <td width="50%" class="tdText"><span style="float: right;"><a href="https://cp.cubecart.com/dashboard#downloads" target="_blank"><img src="https://www.cubecart.com/external/vCheck4.php?v=<?php echo urlencode($ini['ver']);?>" alt="<?php echo $lang['admin_common']['other_visit_cc'];?>" border="0" title="" /></a></span><?php echo $ini['ver']; ?> </td>
  </tr>
  <tr>
    <td width="33%"><a href="http://www.php.net" target="_blank" class="txtLink">PHP</a> <span  class="tdText"><?php echo $lang['admin_common']['other_version']; ?></span></td>
    <td width="50%"><span class="tdText"><?php echo phpversion();?></span></td>
  </tr>
  <tr>
    <td width="33%"><a href="http://www.mysql.com" target="_blank" class="txtLink">MySQL</a> <span class="tdText"><?php echo $lang['admin_common']['other_version']; ?></span></td>
    <td width="50%"><span class="tdText"><?php echo mysql_get_server_info(); ?></span></td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><?php echo $lang['admin_common']['other_img_upload_size']; ?></td>
    <td width="50%" class="tdText">
	<?php
	if (!isset($config['uploadSize'])) {
		$config['uploadSize'] = 0;
	}
	echo format_size($config['uploadSize']);
	?> </td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><?php echo $lang['admin']['misc_server_software']; ?></td>
    <td width="50%" class="tdText"><?php echo @$_SERVER['SERVER_SOFTWARE']; ?></td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><?php echo $lang['admin']['misc_client_browser']; ?></td>
    <td width="50%" class="tdText"><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT']); ?></td>
  </tr>
  </table>
  <br />
  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin_common']['other_store_inventory']; ?></td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><span class="tdText"><?php echo $lang['admin_common']['other_no_products'];?></span></td>
    <td width="50%" class="tdText"><?php echo number_format($noProducts[0]['noProducts']); ?></td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><span class="tdText"><?php echo $lang['admin_common']['other_no_customers']; ?></span></td>
    <td width="50%" class="tdText"><?php echo number_format($noCustomers[0]['noCustomers']); ?></td>
  </tr>
  <tr>
    <td width="33%" class="tdText"><?php echo $lang['admin_common']['other_no_orders']; ?></td>
    <td width="50%" class="tdText"><?php echo number_format($noOrders[0]['noOrders']); ?></td>
  </tr>
</table>

	</td>
  </tr>
</table>
<!-- Code added for CubeCart Support Staff please ignore
Licensed Domain: <?php echo $key->key_data['hostname']; ?>
Software License Key: <?php echo $key->key_data['license_key']; ?>
-->
