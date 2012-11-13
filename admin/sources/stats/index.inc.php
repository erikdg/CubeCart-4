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
|	Store Statistics
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$lang = getLang('admin'.CC_DS.'admin_stats.inc.php');

permission('statistics', 'read', true);

include_once($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
include('classes'.CC_DS.'gd'.CC_DS.'phplot.php');
?>
<p class="pageTitle"><?php echo $lang['admin']['stats_store_stats'];?></p>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin']['stats_choose_view'];?></td>
  </tr>
  <tr>
    <td colspan="2">
	<ul>
	<li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=sales"  class="txtLink"><?php echo $lang['admin']['stats_sales'];?></a></li>
	<li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=searchTerms"  class="txtLink"><?php echo $lang['admin']['stats_search_terms'];?></a></li>
	<li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=prodViews"  class="txtLink"><?php echo $lang['admin']['stats_product_pop'];?></a></li>
	<li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=prodSales"  class="txtLink"><?php echo $lang['admin']['stats_product_pop_sales'];?></a></li>
	<li><a href="<?php echo $glob['adminFile']; ?>?_g=stats/index&amp;stats=online"  class="txtLink"><?php echo $lang['admin']['stats_cust_online'];?></a></li>
	</ul>
	</td>
  </tr>
</table>
<?php

$imageNo = 0;

if(isset($_GET['mStart']) && !is_numeric($_GET['mStart'])) {
	$_GET['mStart'] = '';
}
if(isset($_GET['dStart']) && !is_numeric($_GET['dStart'])) {
	$_GET['dStart'] = '';
}
if(isset($_GET['yStart']) && !is_numeric($_GET['yStart'])) {
	$_GET['yStart'] = '';
}

switch ($_GET['stats']) {
	case 'sales';
		require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');
		include('sales.year.inc.php');
		include('sales.month.inc.php');
		include('sales.day.inc.php');
		break;
	case 'searchTerms';
		include('search.inc.php');
		break;
	case 'prodSales';
		include('product.sales.inc.php');
    	break;
	case 'prodViews';
		include('product.views.inc.php');
	    break;
	case 'online';
		include('online.inc.php');
		break;
}
?>