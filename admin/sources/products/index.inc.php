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
|	Add/Edit/Delete Products
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$_GET['searchStr'] = filter_var($_GET['searchStr'], FILTER_SANITIZE_STRING);

$lang = getLang('admin'.CC_DS.'admin_products.inc.php');
require('classes'.CC_DS.'gd'.CC_DS.'gd.inc.php');
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');

permission('products', 'read', true);

$productsPerPage = 25;

if (isset($_POST['DeleteSelected']) && is_array($_POST['product'])) {
	$query = sprintf('DELETE FROM %sCubeCart_inventory WHERE `productId` IN (%s)', $glob['dbprefix'], implode(',', $_POST['product']));
	$db->misc($query);
}


if (isset($_POST['EditSelected']) && is_array($_POST['product']) && !empty($_POST['MoveTo'])) {
	$i=0;
	foreach ($_POST['product'] as $key => $product_id) {

		if ($_POST['current'][$product_id] == $_POST['MoveTo']) break;

		## Update primary category
		$query = sprintf("UPDATE %sCubeCart_inventory SET `cat_id`='%d' WHERE `productId`='%d';", $glob['dbprefix'], $_POST['MoveTo'], $product_id);
		$db->misc($query);

		## Check if target category was added as an extra for product before
		$query = sprintf("SELECT `cat_id` FROM %sCubeCart_cats_idx WHERE `cat_id` = '%d' AND productId = '%d' LIMIT 1;", $glob['dbprefix'], $_POST['MoveTo'], $product_id);
		$previous = $db->select($query);

		if ($previous) {

			## Delete old primary category record only, new category already there
			$query = sprintf("DELETE FROM %sCubeCart_cats_idx WHERE `cat_id` = '%d' AND `productId` = '%d'", $glob['dbprefix'], $_POST['current'][$product_id], $product_id );
			$db->misc($query);

			## Product counted before we dont need it for target category product count
			unset($_POST['product'][$key]);
		} else {
			## Update secondary categories
			$query = sprintf("UPDATE %1\$sCubeCart_cats_idx SET `cat_id` = '%2\$d' WHERE `productId` = '%3\$d' AND `cat_id` = '%4\$d' LIMIT 1;", $glob['dbprefix'], $_POST['MoveTo'], $product_id, $_POST['current'][$product_id]);
			$db->misc($query);
		}
		## Update category product count
		$db->categoryNos($_POST['current'][$product_id], '-', 1);
		$db->categoryNos($_POST['MoveTo'], '+', 1);
		++$i;
	}

	$cache = new cache();
	$cache->clearCache();
}


if(isset($_POST['normPer']) || isset($_POST['salePer']))
{

	$cache = new cache();
	$cache->clearCache();

	$sqlUpdateWhere = '';

	if(is_array($_POST['cat_id_price']))
	{
		for ($n = 0, $maxn = count($_POST['cat_id_price']); $n < $maxn; ++$n)
		{
			if($_POST['cat_id_price'][$n]>0)
			{
				if($n==0)
				{
					$sqlUpdateWhere .= ' WHERE `cat_id` = '.$db->mySQLSafe($_POST['cat_id_price'][$n]);
				}
				else
				{
					$sqlUpdateWhere .= ' OR `cat_id` = '.$db->mySQLSafe($_POST['cat_id_price'][$n]);
				}
			}
		}
	}

	if (is_numeric($_POST['normPer'])) {

		if($_POST['normPerMethod']=='percent' && $_POST['normPer']>0){
			$sum = '`price` * '.($_POST['normPer']/100);
		} elseif($_POST['normPerMethod']=='value' && $_POST['normPer']<0) {
			$sum = '`price` '.$_POST['normPer'];
		} elseif($_POST['normPerMethod']=='value') {
			$sum = '`price` + '.$_POST['normPer'];
		} elseif($_POST['normPerMethod']=='actual' && $_POST['normPer']>0) {
			$sum = $_POST['normPer'];
		} else {
			$sum = '`price`';
		}

		$query = 'UPDATE '.$glob['dbprefix'].'CubeCart_inventory SET `price` = '.$sum.$sqlUpdateWhere;
		$result = $db->misc($query);
	}

	if (is_numeric($_POST['salePer'])) {

		if ($_POST['salePerMethod']=='percent' && $_POST['salePer']>0) {
			$sum = '`sale_price` * '.($_POST['salePer']/100);
		} else if ($_POST['salePerMethod']=='value' && $_POST['salePer']<0) {
			$sum = '`sale_price` '.$_POST['salePer'];
		} elseif ($_POST['salePerMethod']=='value') {
			$sum = '`sale_price` + '.$_POST['salePer'];
		} elseif ($_POST['salePerMethod']=='actual' && $_POST['salePer']>0) {
			$sum = $_POST['salePer'];
		} else {
			$sum = '`sale_price`';
		}

		$query = 'UPDATE '.$glob['dbprefix'].'CubeCart_inventory SET `sale_price` = '.$sum.$sqlUpdateWhere;
		$result = $db->misc($query);
	}

	if ($result) {
		$msg2 = "<p class='infoText'>".$lang['admin']['products_price_upd_successful']."</p>";
	} else {
		$msg2 = "<p class='warnText'>".$lang['admin']['products_price_upd_fail']."</p>";
	}

} else if (isset($_GET['delete']) && $_GET['delete']>0) {
	$cache = new cache();
	$cache->clearCache();

	// delete product
	$where = '`productId` = '.$db->mySQLSafe($_GET['delete']);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_inventory', $where);
	
	// delete reviews/comments
	$where = '`productId` = '.$db->mySQLSafe($_GET['delete']);
	$deleteIdx = $db->delete($glob['dbprefix'].'CubeCart_reviews', $where);

	// set categories -1
	$cats = $db->select('SELECT `cat_id` FROM '.$glob['dbprefix'].'CubeCart_cats_idx WHERE `productId` = '.$db->mySQLSafe($_GET['delete']));
	if ($cats) {
		for ($i = 0, $maxi = count($cats); $i < $maxi; ++$i) {
			$db->categoryNos($cats[$i]['cat_id'], '-');
		}
	}

	// delete category index
	$where = '`productId` = '.$db->mySQLSafe($_GET['delete']);
	$deleteIdx = $db->delete($glob['dbprefix'].'CubeCart_cats_idx', $where);
	unset($record);

	// delete product options
	$record['product'] = $db->mySQLSafe($_GET['delete']);
	$where = '`product` = '.$db->mySQLSafe($_GET['delete']);
	$deleteOps = $db->delete($glob['dbprefix'].'CubeCart_options_bot', $where);
	unset($record);

	if ($delete) {
		$msg = "<p class='infoText'>".$lang['admin']['products_delete_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products_delete_fail']."</p>";
	}

} else if (isset($_POST['productId'])) {

	$cache = new cache();
	$cache->clearCache();

	$allowedFields = $db->getFields($glob['dbprefix'].'CubeCart_inventory');

	foreach ($_POST as $name => $value) {
		if (in_array($name, $allowedFields)) { // && (!empty($value) || $value == '0')) {
			$record[$name] = $db->mySQLSafe($value);
		}
	}

	## Custom field translation
	$record['image']			= $db->mySQLSafe(imgPath($_POST['imageName'], false, ''));
	$record['tax_inclusive']	= ($_POST['tax_inclusive'] == 1) ? '1' : '0';
    $record['disabled']     	= ($_POST['disabled'] == 1) ? '1' : '0';
	$record['cat_id']			= $db->mySQLSafe($_POST['cat_id']);

	$description				= (detectSSL() && !$config['force_ssl'] && $GLOBALS['rootRel'] != CC_DS) ?  str_replace($config['rootRel_SSL'], $glob['rootRel'], $_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record['description']		= (!empty($description)) ? $db->mySQLSafe($description) : 'NULL';

	## Generate product code
	if (empty($_POST['productCode'])) {
		$product_code = unique_product_code($_POST['name'],$_POST['cat_id']);
		$record['productCode'] = $db->mySQLSafe($product_code);
	} else {
		$record['productCode'] = $db->mySQLSafe($_POST['productCode']);
	}

	$record['productCode'] = preg_replace('/[^a-z0-9-]/i','',$record['productCode']);
	$record['productCode'] = $db->mySQLSafe($record['productCode']);
	// if image is a JPG check thumbnail doesn't exist and if not make one

	/*
	$imageFormat = strtoupper(ereg_replace(".*\.(.*)$","\\1",$_POST['imageName']));
	if($imageFormat == "JPG" || $imageFormat == "JPEG" || $imageFormat == "PNG" || ($imageFormat == "GIF" && $config['gdGifSupport']==1)) {
		$rootThumbFile = imgPath($_POST['imageName'],$thumb=1,$path="root");
		$rootMasterFile = imgPath($_POST['imageName'],$thumb=0,$path="root");
		if(file_exists($rootThumbFile)) {
			@unlink($rootThumbFile);
		}
		$img = new gd($rootMasterFile);
		$img->size_auto($config['gdthumbSize']);
		$img->save($rootThumbFile);
	}
	*/

	if (isset($_POST['productId']) && $_POST['productId']>0) {

		$disable_sql = sprintf('SELECT `disabled` FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE `productId` = '.$_POST['productId']);
		$disabled = $db->select($disable_sql);

		$disable_flag = $disabled_flag = false;

		if ($disabled) {
			if ($disabled[0]['disabled'] && !$_POST['disabled']) {
				$db->categoryNos($_POST['cat_id'], '+');
				$disabled_flag = true;
			} else if (!$disabled[0]['disabled'] && $_POST['disabled']) {
				$db->categoryNos($_POST['cat_id'], '-');
				$disable_flag = true;
			}
		}

		$where = '`productId` = '.$db->mySQLSafe($_POST['productId']);
		$product_id = $_POST['productId'];

		$update = $db->update($glob['dbprefix'].'CubeCart_inventory', $record, $where);
		unset($record, $where);

		// update category count
		if($_POST['oldCatId']!=$_POST['cat_id']) {

				## set old category -1 IF IT WAS IN THERE BEFORE
				$numOldCat = $db->numrows('SELECT * FROM '.$glob['dbprefix'].'CubeCart_cats_idx WHERE `cat_id` = '.$db->mySQLSafe($_POST['oldCatId']).' AND `productId` = '.$db->mySQLSafe($_POST['productId']));
				if ($numOldCat>0 && !$disabled_flag) {
					$db->categoryNos($_POST['oldCatId'], '-');
				}
				## set new category +1 IF IT WAS NOT IN THERE BEFORE
				$numNewCat = $db->numrows('SELECT * FROM '.$glob['dbprefix'].'CubeCart_cats_idx WHERE `cat_id` = '.$db->mySQLSafe($_POST['cat_id']).' AND `productId` = '.$db->mySQLSafe($_POST['productId']));
				if($numNewCat == 0 && !$disabled_flag && !$disable_flag) {
					$db->categoryNos($_POST['cat_id'], '+');
				}

				## delete old index
				$where = '`productId` = '.$db->mySQLSafe($_POST['productId']).' AND `cat_id` = '.$db->mySQLSafe($_POST['oldCatId']);
				$deleteIdx = $db->delete($glob['dbprefix'].'CubeCart_cats_idx', $where);
				unset($record);

				// delete new index if it was added as an extra before
				$where = '`productId` = '.$db->mySQLSafe($_POST['productId']).' AND `cat_id` = '.$db->mySQLSafe($_POST['cat_id']);
				$deleteIdx = $db->delete($glob['dbprefix'].'CubeCart_cats_idx', $where);
				unset($record);

				// add new idx
				$record['productId'] = $db->mySQLSafe($_POST['productId']);
				$record['cat_id'] = $db->mySQLSafe($_POST['cat_id']);
				$insertIdx = $db->insert($glob['dbprefix'].'CubeCart_cats_idx', $record);
				unset($record);

		}
		// replace into cat_idx for missing values bug fix from v3 upgrade
		$query = 'DELETE FROM '.$glob['dbprefix'].'CubeCart_cats_idx WHERE `cat_id` = '.$db->mySQLSafe($_POST['cat_id']).' AND `productId` = '.$db->mySQLSafe($_POST['productId']);
		$db->misc($query);
		$query = 'INSERT INTO '.$glob['dbprefix'].'CubeCart_cats_idx SET `cat_id` = '.$db->mySQLSafe($_POST['cat_id']).', `productId` = '.$db->mySQLSafe($_POST['productId']);
		$db->misc($query);

		if ($update) {
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_update_successful']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_update_fail']."</p>";
		}

	} else {

		$insert = $db->insert($glob['dbprefix'].'CubeCart_inventory', $record);
		unset($record);

		$record['cat_id']		= $db->mySQLSafe($_POST['cat_id']);
		$record['productId']	= $db->insertid();
		$product_id				= $db->insertid();

		$db->insert($glob['dbprefix'].'CubeCart_cats_idx', $record);
		unset($record);

		if ($insert) {
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_add_success']."</p>";
			// notch up amount of products in category
			if ($_POST['disabled'] == 0) {
				$db->categoryNos($_POST['cat_id'], '+');
			}
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_add_fail']."</p>";
		}
	}

	## Option manager
	if (isset($_POST['option_add']) && is_array($_POST['option_add']) && !empty($_POST['option_add'])) {
		foreach ($_POST['option_add'] as $option) {
			$value		= explode('|', $option);
			$value[2] 	= preg_replace("#[^\d+\.\-]#", "", $value[2]);
			$data	= array(
				'product'		=> ($product_id) ? $product_id : $_POST['productId'],
				'option_id'		=> is_numeric($value[0]) ? $value[0] : $value[1],
				'value_id'		=> is_numeric($value[0]) ? $value[1] : 0,
				'option_price'	=> preg_replace('#[^0-9\.]#i', '', $value[2]),
				'option_symbol'	=> "'".(($value[2] >= 0) ? '+' : '-')."'",
			);
			$opt_insert = $db->insert($glob['dbprefix'].'CubeCart_options_bot', $data);
		}
		if ($opt_insert) {
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_update_successful']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_update_fail']."</p>";
		}
	}
	if (isset($_POST['option_edit']) && is_array($_POST['option_edit']) && !empty($_POST['option_edit'])) {
		# Update existing product options
		$product_id = ($product_id) ? $product_id : $_POST['productId'];

		foreach ($_POST['option_edit'] as $assign_id => $option) {
			$value	= explode('|', $option);
			$value[2] 	= preg_replace("#[^\d+\.\-]#", "", $value[2]);
			$data	= array(
				'product'		=> $product_id,
				'option_id'		=> is_numeric($value[0]) ? $value[0] : $value[1],
				'value_id'		=> is_numeric($value[0]) ? $value[1] : 0,
				'option_price'	=> preg_replace('#[^0-9\.]#i', '', $value[2]),
				'option_symbol'	=> "'".(($value[2] >= 0) ? '+' : '-')."'",
			);
			$opt_update = $db->update($glob['dbprefix'].'CubeCart_options_bot', $data, array('assign_id' => $assign_id, 'product' => $product_id));
		}
		if ($opt_update) {
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_update_successful']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_update_fail']."</p>";
		}
	}

	if (isset($_POST['option_remove']) && is_array($_POST['option_remove']) && !empty($_POST['option_remove'])) {
		foreach ($_POST['option_remove'] as $option) {
			$opt_delete = $db->delete($glob['dbprefix'].'CubeCart_options_bot', array('assign_id' => $option, 'product' => ($product_id) ? $product_id : $_POST['productId']));
		}
		if ($opt_delete) {
			$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_update_successful']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_update_fail']."</p>";
		}
	}
}

if (!isset($_GET['mode'])) {
	## Build the SQL Query
	if (isset($_GET['edit']) && (int)$_GET['edit']>0) {
		$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE `productId` = %s', $db->mySQLSafe((int)$_GET['edit']));
	} else {
		if (isset($_GET['orderCol']) && preg_match('#^([a-z0-9_]+)$#i',$_GET['orderCol']) && isset($_GET['orderDir'])) {
			$orderBy =  'I.'.$_GET['orderCol'].' '.$_GET['orderDir'];
		} else {
			$orderBy = 'I.productId ASC';
		}

		$whereClause = '';

		if (isset($_GET['searchStr']) && !empty($_GET['searchStr'])) {
			$searchwords = preg_split( '/[ ,]/', trim($_REQUEST['searchStr']));
			foreach ($searchwords as $word) {
				$searchArray[]=$word;
			}

			$noKeys = count($searchArray);
			for ($i = 0; $i < $noKeys; ++$i) {

				$ucSearchTerm = strtoupper($searchArray[$i]);
				if (($ucSearchTerm != 'AND') && ($ucSearchTerm != 'OR')) {
					$like .= "(I.name LIKE '%".$searchArray[$i]."%' OR I.description LIKE '%".$searchArray[$i]."%' OR  I.productCode LIKE '%".$searchArray[$i]."%') OR ";
				} else {
					$like = substr($like,0,strlen($like)-3);
					$like .= $ucSearchTerm;
				}
			}
			$like = substr($like,0,strlen($like)-3);
			$whereClause .= 'WHERE '.$like;
		}

		if (isset($_GET['category']) && $_GET['category']>0) {
			$whereClause .= (isset($like)) ? ' AND ' : ' WHERE ';
			$whereClause .= 'CI.cat_id = '.$_GET['category'];
			$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_cats_idx AS CI INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory AS I ON CI.productId = I.productId INNER JOIN '.$glob['dbprefix'].'CubeCart_category AS C ON I.cat_id = C.cat_id '.$whereClause.' ORDER BY '.$orderBy;
		} else {
			$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory AS I INNER JOIN '.$glob['dbprefix'].'CubeCart_category AS C ON I.cat_id = C.cat_id '.$whereClause.' ORDER BY '.$orderBy;
		}
	}

	// query database
	$page = (isset($_GET['page'])) ? $_GET['page'] : 0;

	$results = $db->select($query, $productsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $productsPerPage, $page, "page", "txtLink", 7, array('delete'));

}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap' class="pageTitle"><?php echo $lang['admin']['products_prod_inventory'];?></td>
     <?php if(!isset($_GET['mode'])){ ?><td align="right" valign="middle"><a <?php if(permission('products','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/index&amp;mode=new" class="txtLink" <?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" /><?php echo $lang['admin_common']['add_new'];?></a></td><?php } ?>
  </tr>
</table>

<?php
if (isset($msg)) echo msg($msg);

if (!isset($_GET['mode']) && !isset($_GET['edit'])) {
	if ($results) {
?>
<p class="copyText"><?php echo $lang['admin']['products_current_prods_in_db'];?></p>
<form name="filter" method="get" action="<?php echo $glob['adminFile']; ?>">
<input type="hidden" name="_g" value="products/index" />
<p align="right" class="copyText">
  <select name="category" class="textbox">
	<option value="All" <?php if(isset($_GET['category']) && $_GET['category']=='All') echo 'selected="selected"'; ?>><?php echo $lang['admin']['products_all_cats'];?></option>

	<?php echo showCatList(isset($_GET['category']) ? $_GET['category'] : null); ?>

	</select>
	<?php echo $lang['admin']['products_by']; ?>
	<select name="orderCol" class="textbox">
		<option value="name" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='name') echo 'selected="selected"';?>><?php echo $lang['admin']['products_prod_name'];?></option>
		<option value="productId" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='productId') echo 'selected="selected"';?>><?php echo $lang['admin']['products_prod_id'];?></option>
		<option value="productCode" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='productCode') echo 'selected="selected"';?>><?php echo $lang['admin']['products_prod_code'];?></option>
		<option value="cat_id" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='cat_id') echo 'selected="selected"';?>><?php echo $lang['admin']['products_master_cat2'];?></option>
		<option value="stock_level" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='stock_level') echo 'selected="selected"';?>><?php echo $lang['admin']['products_stock_level'];?></option>
		<option value="price" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='price') echo 'selected="selected"';?>><?php echo $lang['admin']['products_normal_price'];?></option>
		<option value="sale_price" <?php if(isset($_GET['orderCol']) && $_GET['orderCol']=='sale_price') echo 'selected="selected"';?>><?php echo $lang['admin']['products_sale_price'];?></option>
	</select>
	<?php echo $lang['admin']['products_in']; ?>
    <select name="orderDir" class="textbox">
		<option value="ASC" <?php if(isset($_GET['orderDir']) && $_GET['orderDir']=='ASC') echo 'selected="selected"';?>><?php echo $lang['admin']['products_asc'];?></option>
		<option value="DESC" <?php if(isset($_GET['orderDir']) && $_GET['orderDir']=='DESC') echo 'selected="selected"';?>><?php echo $lang['admin']['products_desc'];?></option>
	</select>
	<?php echo $lang['admin']['products_containing_text'];?>

	  <input type="text" name="searchStr" class="textbox" value="<?php if(isset($_GET['searchStr']))echo $_GET['searchStr']; ?>" />
  <input name="submit" type="submit" class="submit" value="<?php echo $lang['admin']['products_filter'];?>" />
  <input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=products/index');return document.returnValue" value="<?php echo $lang['admin']['products_reset'];?>" class="submit" />
</p>
</form>
<?php } ?>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>
<form method="post" id="moveProducts" enctype="multipart/form-data" action="<?php echo $glob['adminFile']; ?>?_g=products/index">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
	<td align="center" nowrap="nowrap" class="tdTitle">&nbsp;</td>
    <td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_id'];?></td>
    <td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_type'];?></td>
    <td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_prod_code'];?></td>
	<td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_name'];?></td>
    <td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_master_cat2'];?></td>
	<td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_image'];?></td>
	<td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_price_sale_price'];?></td>
    <td align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_in_stock'];?></td>
    <td width="20%" colspan="3" align="center" nowrap="nowrap" class="tdTitle"><?php echo $lang['admin']['products_action'];?></td>
  </tr>
  <?php
  if ($results) {

  	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

	$cellColor = '';
	$cellColor = cellColor($i);
  ?>
  <tr>
    <td align="center" class="<?php echo $cellColor; ?>">
	  <input type="checkbox" name="product[]" class="productCheckbox" id="product_<?php echo $results[$i]['productId']; ?>" value="<?php echo $results[$i]['productId']; ?>" />
	  <input type="hidden" name="current[<?php echo $results[$i]['productId']; ?>]" value="<?php echo $results[$i]['cat_id']; ?>" />
	</td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['productId']; ?></span></td>
	<td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $glob['adminFolder']; ?>/images/productIcon<?php echo $results[$i]['digital'];?>.gif" alt="" width="16" height="16" title="" /></td>
	<td align="left" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['productCode']; ?></span></td>
	<td align="left" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['name']; ?></span></td>
    <td class="<?php echo $cellColor; ?>"><span class="txtDir"><?php echo getCatDir($results[$i]['cat_name'], $results[$i]['cat_father_id'], $results[$i]['cat_id']);?></span><br />
	  <a href="javascript:;" <?php if (permission('products','edit')){ ?>onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=products/extraCats&amp;productId=<?php echo $results[$i]['productId']; ?>&amp;cat_id=<?php echo $results[$i]['cat_id']; ?>&amp;cat_father_id=<?php echo $results[$i]['cat_father_id']; ?>&amp;cat_name=<?php echo urlencode(html_entity_decode(stripslashes($results[$i]['cat_name']))); ?>&amp;name=<?php echo urlencode($results[$i]['name']); ?>','extraCats',500,450,1);" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin']['products_manage_cats'];?></a></td>

	<td align="center" valign="middle"  class="<?php echo $cellColor; ?>">
	<?php
	$thumbPathRoot = imgPath($results[$i]['image'],1,'root');
	$thumbPathRel = imgPath($results[$i]['image'],1,'rel');

	$masterPathRoot = imgPath($results[$i]['image'],0,'root');
	$masterPathRel = imgPath($results[$i]['image'],0,'rel');

	if (file_exists($thumbPathRoot) && !empty($results[$i]['image'])) {
		$imgSize = getimagesize($thumbPathRoot);
		$imgFile = $thumbPathRel;
	} else if (file_exists($masterPathRoot) && !empty($results[$i]['image'])) {
		$imgSize = getimagesize($masterPathRoot);
		$imgFile = $masterPathRel;
	}

	if (isset($imgFile) && !empty($imgFile)) {
	?>
	<img src="<?php echo $imgFile; ?>" alt="<?php echo $results[$i]['name']; ?>" title="" height="50" />
	<div>
	<?php
		if (permission('products','edit')) {
			$link = 'javascript:openPopUp(\'?_g=products/extraImgs&amp;productId='.$results[$i]['productId'].'\',\'extraImgs\',550,450,1);" class="txtLink';
		} else {
			$link = $link401;
		}
		echo sprintf('<a href="%s">%s</a>', $link, $lang['admin']['products_manage_images']);
		/*
		<a href="#" <?php if(permission("products","edit")==true){ ?>onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=products/extraImgs&amp;productId=<?php echo $results[$i]['productId']; ?>&amp;img=<?php echo urlencode($results[$i]['image']); ?>','extraImgs',550,450,1);" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['products_manage_images'];?></a></div>
		<?php
		*/
		unset($imgFile);
	} else {
		echo '&nbsp;';
	}
	?>
	</div>
	</td>
	<td align="center" class="<?php echo $cellColor; ?>">
	<span class="copyText"><?php echo priceFormat($results[$i]['price'], true); ?></span>

	<?php
	$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);
	if ($salePrice) { ?>
	<br />
	<span class="txtRed">
	<?php
	echo priceFormat($salePrice,true);
	?>
	</span>
	<?php } ?>
	</td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php if($results[$i]['useStockLevel']==1) { echo $results[$i]['stock_level']; } else { echo "n/a"; }?></span></td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>"><a <?php if(permission('products','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/index&amp;edit=<?php echo $results[$i]['productId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['edit'];?></a></td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>"><a <?php if(permission('products','delete')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/index&amp;delete=<?php echo $results[$i]['productId']; ?>&amp;cat_id=<?php echo $results[$i]['cat_id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q']));?>')" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['delete'];?></a></td>
    <td align="center" width="5%" class="<?php echo $cellColor; ?>"><a <?php if(permission('products','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/languages&amp;prod_master_id=<?php echo (int)$results[$i]['productId']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin']['products_languages'];?></a></td>
  </tr>
  <?php } // end loop ?>
  <tr>
  	<td><img src="<?php echo $glob['adminFolder'];?>/images/selectAll.gif" alt="" width="16" height="11" /></td>
  	<td colspan="9" class="tdText">
	  <a href="#" class="txtLink" onclick="return checkUncheck('moveProducts', 'productCheckbox');"><?php echo $lang['admin']['products_check_uncheck_all'];?></a> &nbsp;
	  <?php
	 if(permission('products','edit')) {
	 ?>
	  <select name="MoveTo" class="textbox">
	    <option value="" selected="selected"><?php echo $lang['admin']['products_move_selected_to'];?></option>
		<?php echo showCatList($results[0]['cat_id']); ?>
	  </select>
	  <input type="submit" name="EditSelected" class="submit" value="<?php echo $lang['admin']['products_go'];?>" />
	 <?php
	 }
	 if(permission('products','delete')) {
	 ?>
	  <input type="submit" name="DeleteSelected" class="submit" value="Delete Selected" />
	 <?php
	 }
	 ?>
	</td>
  </tr>
  <?php
  } else { ?>
   <tr>
    <td colspan="10" class="tdText"><?php echo $lang['admin']['products_no_products_exist'];?></td>
  </tr>
  <?php } ?>
</table>

</form>

<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>

<?php
if(isset($msg2))
{
	echo msg($msg2);
}
?>
<form id="update_prices" name="update_prices" method="post" action="<?php echo $glob['adminFile']; ?>?_g=products/index">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="3" class="tdTitle"><?php echo $lang['admin']['products_update_prices_by_cat']; ?></td>
  </tr>
  <tr>
    <td align="left" class="tdText" width="100"><strong><?php echo $lang['admin']['products_normal_price2']; ?></strong></td>
    <td colspan="2" align="left" class="tdText"><strong>
      <input name="normPer" type="text" size="5" maxlength="5" />
      <select name="normPerMethod">
        <option value="percent"><?php echo $lang['admin']['products_val_percent']; ?></option>
        <option value="value"><?php echo $lang['admin']['products_val_amount']; ?></option>
		<option value="actual"><?php echo $lang['admin']['products_val_actual']; ?></option>
      </select>
      <?php echo $lang['admin']['products_sale_price2']; ?></strong>
      <input name="salePer" type="text" size="5" maxlength="5" />      <select name="salePerMethod">
        <option value="percent"><?php echo $lang['admin']['products_val_percent']; ?></option>
        <option value="value"><?php echo $lang['admin']['products_val_amount']; ?></option>
		<option value="actual"><?php echo $lang['admin']['products_val_actual']; ?></option>
          </select></td>
    </tr>
  <tr>
    <td align="left" valign="top" class="tdText" width="100"><strong><?php echo $lang['admin']['products_category']; ?></strong><br />

      <?php echo $lang['admin']['products_hold_ctrl']; ?></td>
    <td align="left" valign="top" class="tdText"><select name="cat_id_price[]" size="5" multiple="multiple" class="textbox">
      <option value="0">-- <?php echo $lang['admin_common']['all']; ?> --</option>
		<?php echo showCatList($results[0]['cat_id']); ?>
    </select></td>
    <td align="right" valign="bottom" class="tdText">
	<input type="submit" name="submit_prices" value="<?php echo $lang['admin']['products_update_prices']; ?>" <?php if(!permission('products','edit')){ echo 'disabled="disabled" class="submitDisabled"'; } else { echo "class='submit'"; } ?> />
	</td>
  </tr>
  <tr>
    <td colspan="3" align="left" class="tdText">
	<?php echo $lang['admin']['products_eg_1']; ?><br />
    <?php echo $lang['admin']['products_eg_2']; ?></td>
    </tr>
</table>
</form>

<?php
} else if ($_GET['mode'] == 'new' || (int)$_GET['edit']>0) {

if(isset($_GET['edit']) && (int)$_GET['edit']>0){ $modeTxt = $lang['admin_common']['edit']; } else { $modeTxt = $lang['admin_common']['add']; }
?>
<p class="copyText"><?php echo $lang['admin']['products_add_prod_desc'];?></p>
<form action="<?php echo $glob['adminFile']; ?>?_g=products/index" method="post" enctype="multipart/form-data" name="form1">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET['edit']) && $_GET['edit']>0){ echo $modeTxt; } else { echo $modeTxt; } echo " ".$lang['admin']['products_product'];?> </td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_prod_name2'];?></strong></td>
    <td>
      <input name="name" type="text" class="textbox" value="<?php if(isset($results[0]['name'])) echo validHTML($results[0]['name']); ?>" maxlength="255" />
    </td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_disable'];?></strong></td>
    <td>
	  <select name="disabled" class="textbox">
        <option value="1" <?php if (isset($results[0]['disabled']) && $results[0]['disabled']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
        <option value="0" <?php if (!isset($results[0]['disabled']) || $results[0]['disabled']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
      </select>
    </td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_prod_stock_no'];?></strong> <br />
<?php echo $lang['admin']['products_auto_generated'];?>
</td>
    <td><input name="productCode" type="text" class="textbox" value="<?php if(isset($results[0]['productCode'])) echo $results[0]['productCode']; ?>" maxlength="255" /></td>
  </tr>
  <tr>
    <td colspan="2" class="tdRichText"><span class="tdText"><strong><?php echo $lang['admin']['products_description'];?></strong> <?php //echo $lang['admin']['products_primary_lang'];?></span>
	  </td>
    </tr>
  <tr>
    <td colspan="2" class="tdRichText">
<?php
		require($glob['adminFolder'].'/includes'.CC_DS.'rte'.CC_DS.'fckeditor.php');

		$oFCKeditor				= new FCKeditor('FCKeditor');
		$oFCKeditor->BasePath	= $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/' ;
		$oFCKeditor->Value		= (isset($results[0]['description'])) ? $results[0]['description'] : '';

		if (!$config['richTextEditor']) $oFCKeditor->off = true;
		$oFCKeditor->Create();
?>
	</td>
    </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_category'];?></strong></td>
    <td>
	<select name="cat_id" class="textbox">
	  <?php echo showCatList($results[0]['cat_id']); ?>
	</select>
	</td>
  </tr>
  <tr>
    <td width="25%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['products_image2'];?></strong> <br />
	<?php echo $lang['admin']['products_opt_and_thumbs'];?></td>
    <td valign="top">
	<?php
	$imgSrc = (!empty($results[0]['image'])) ? imgPath($results[0]['image'], 0, 'rel') : $glob['rootRel'].'images/general/px.gif';
	?>
	<img src="<?php echo $imgSrc; ?>" alt="" id="previewImage" title="" />
	<div>

<input name="upload" class="submit" type="button" id="upload" onclick="openPopUp('<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/includes/rte/editor/filemanager/browser/default/browser.html?Type=uploads&amp;Connector=<?php echo urlencode($GLOBALS['rootRel'].$glob['adminFolder']); ?>%2Fincludes%2Frte%2Feditor%2Ffilemanager%2Fconnectors%2Fphp%2Fconnector.php','filemanager',700,600)" value="<?php echo $lang['admin']['products_browse_upload_image'];?>" />
<input type="button" class="submit" value="<?php echo $lang['admin']['product_remove_image'];?>" onclick="findObj('previewImage').src='<?php echo $glob['rootRel']; ?>/images/general/px.gif';findObj('imageName').value = '';" />
<input type="hidden" name="imageName" id="imageName" value="<?php if(isset($results[0]['image'])) echo $results[0]['image']; ?>" />
</div>
</td>

  </tr>
<?php
	// Flexible Taxes, by Estelle Winterflood
	$config_tax_mod = fetchDbConfig('Multiple_Tax_Mod');
?>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_normal_price2']; if ($config_tax_mod['status']) echo $lang['admin']['settings_excl_tax']; ?> </strong>
	</td>
    <td><input name="price" value="<?php if(isset($results[0]['price'])) echo $results[0]['price']; ?>" type="text" class="textbox" size="10" /></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_sale_price2']; if ($config_tax_mod['status']) echo $lang['admin']['settings_excl_tax']; ?> </strong><br />
      <?php echo $lang['admin']['products_sale_mode_desc'];?> </td>
    <td><input name="sale_price" value="<?php if(isset($results[0]['sale_price'])) echo $results[0]['sale_price']; ?>" type="text" class="textbox" size="10" /></td>
  </tr>
  <tr>
	<td><strong><?php echo $lang['admin']['products_tax_inclusive'];?></strong></td>
	<td><input name="tax_inclusive" value="1" type="checkbox" <?php if ($results[0]['tax_inclusive']) echo 'checked="checked" '; ?>/></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['products_tax_class'];?></strong></td>
    <td class="tdText">
	<select name="taxType">
	  <option value="0">Please Select</option>
    <?php
	$taxTypes = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_taxes');
	 for($i = 0, $maxi = count($taxTypes); $i < $maxi; ++$i){ ?>
	<option value="<?php echo $taxTypes[$i]['id']; ?>" <?php if(isset($results[0]['taxType']) && $taxTypes[$i]['id'] == $results[0]['taxType']) echo 'selected="selected"'; ?>><?php echo $taxTypes[$i]['taxName'];  if (! $config_tax_mod['status']) echo "(".$taxTypes[$i]['percent']."%)"; ?></option>
	<?php } ?>
	</select>
	</td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['products_prod_weight'];?></strong></td>
    <td class="tdText"><input name="prodWeight" type="text" class="textbox" size="10" value="<?php if(isset($results[0]['prodWeight'])) echo $results[0]['prodWeight']; ?>" /> <?php echo $config['weightUnit']; ?></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['products_stock_level2'];?><br />
      </strong><?php echo $lang['admin']['products_reduce_stock_level'];?>      </td>
    <td class="tdText"><input name="stock_level" value="<?php if(isset($results[0]['stock_level'])) echo $results[0]['stock_level']; ?>" type="text" class="textbox" size="10" /></td>
  </tr>
  <?php
  if ($config['stock_warn_type']) {
  ?>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['products_stock_level_warn'];?><br />
      </strong>      </td>
    <td class="tdText"><input name="stockWarn" value="<?php if(isset($results[0]['stockWarn'])) echo $results[0]['stockWarn']; ?>" type="text" class="textbox" size="10" /></td>
  </tr>
  <?php
  }
  ?>

  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['products_use_stock_q'];?></strong></td>
    <td class="tdText">
	<?php echo $lang['admin_common']['yes'];?>
	<input name="useStockLevel" type="radio" value="1" <?php if(isset($results[0]['useStockLevel']) && $results[0]['useStockLevel'] || !isset($results[0]['useStockLevel'])) echo 'checked="checked"'; ?> />
	<?php echo $lang['admin_common']['no'];?>
	<input name="useStockLevel" type="radio" value="0" <?php if(isset($results[0]['useStockLevel']) && !$results[0]['useStockLevel']) echo 'checked="checked"'; ?> /></td>
  </tr>
  <tr>
    <td class="tdText" valign="top"><strong><?php echo $lang['admin']['products_product_type'];?></strong>
      </td>
    <td class="tdText"><?php echo $lang['admin']['products_tangible'];?><span class="tdText">
      <input name="digital" onclick="digitalDir.className='hiddenTextbox';digitalDir.value=''" type="radio" value="0" <?php if(isset($results[0]['digital']) && !$results[0]['digital'] || !isset($results[0]['digital'])) echo 'checked="checked"'; ?> />
    </span> <?php echo $lang['admin']['products_digital'];?><span class="tdText">
    <input name="digital" onclick="digitalDir.className='dirTextbox'" type="radio" value="1" <?php if(isset($results[0]['digital']) && $results[0]['digital']) echo 'checked="checked"'; ?> />

    <input name="digitalDir" type="text" id="digitalDir" value="<?php if(isset($results[0]['digitalDir'])) echo $results[0]['digitalDir']; ?>" maxlength="255" <?php if(isset($results[0]['digitalDir']) && $results[0]['digital']==1) { echo "class='dirTextbox'"; } else { ?>class="hiddenTextbox" <?php } ?> />
    <br />
    <?php echo $lang['admin']['products_digi_path'];?></span></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['products_show_featured'];?></strong></td>
    <td class="tdText">
	<select name="showFeatured" class="textbox">
        <option value="1" <?php if(isset($results[0]['showFeatured']) && $results[0]['showFeatured']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
        <option value="0" <?php if(isset($results[0]['showFeatured']) && $results[0]['showFeatured']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
      </select>
	</td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_upc'];?></strong>
</td>
    <td><input name="upc" type="text" class="textbox" value="<?php if(isset($results[0]['upc'])) echo $results[0]['upc']; ?>" maxlength="12" /></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_ean'];?></strong>
</td>
    <td><input name="ean" type="text" class="textbox" value="<?php if(isset($results[0]['ean'])) echo $results[0]['ean']; ?>" maxlength="14" /></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_jan'];?></strong>
</td>
    <td><input name="jan" type="text" class="textbox" value="<?php if(isset($results[0]['jan'])) echo $results[0]['jan']; ?>" maxlength="13" /></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['products_isbn'];?></strong>
</td>
    <td><input name="isbn" type="text" class="textbox" value="<?php if(isset($results[0]['isbn'])) echo $results[0]['isbn']; ?>" maxlength="13" /></td>
  </tr>
  <tr>
    <td width="25%">&nbsp;</td>
    <td>
	<input type="hidden" name="oldCatId" value="<?php if(isset($results[0]['cat_id'])) echo $results[0]['cat_id']; ?>" />
	<input type="hidden" name="productId" value="<?php if(isset($results[0]['productId'])) echo $results[0]['productId']; ?>" />
	<input name="Submit" type="submit" class="submit" value="<?php echo $modeTxt." ".$lang['admin']['products_product'];?>" /></td>
  </tr>
  </table>
<?php
$optquery		= sprintf('SELECT * FROM %sCubeCart_options_top WHERE 1', $glob['dbprefix']);
$options_result = $db->select($optquery);

if ($options_result) {

?>
<br />
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
	  <tr>
		<td colspan="2" class="tdTitle"><strong>Product Options</strong></td>
	  </tr>
	  <tr>
		<td width="25%" class="tdText">&nbsp;</td>
		<td align="left">
		<div id="options_added" style="width: 450px;">
		<?php
		## Product Options (Current - Select)
		if (!empty($_GET['edit']) && is_numeric($_GET['edit'])) {
			$optionsc_query		= sprintf("SELECT T.option_name, M.value_name, B.* FROM %1\$sCubeCart_options_top AS T, %1\$sCubeCart_options_mid AS M, %1\$sCubeCart_options_bot AS B WHERE B.option_id = T.option_id AND B.value_id = M.value_id AND B.product = %2\$s AND T.option_type = '0' ORDER BY T.option_name, M.value_name ASC", $glob['dbprefix'], $results[0]['productId']);
			$optionsc_result	= $db->select($optionsc_query);
			if ($optionsc_result) {
				foreach ($optionsc_result as $option) {
					$option['option_data'] = sprintf('%d|%d|%s', $option['option_id'], $option['value_id'], $option['option_price']);

					?>
				<div id="option_<?php echo $option['assign_id']; ?>" style="clear: right;">
				  <span style="float: right; text-align: right;">
					<a href="#" onclick="optionEdit('<?php echo $option['assign_id']; ?>', '<?php echo $option['option_data']; ?>'); return false;"><img src="images/icons/edit.png" alt="edit" /></a>
					<a href="#" onclick="optionRemove('<?php echo $option['assign_id']; ?>'); return false;"><img src="images/icons/delete.png" alt="delete" /></a>
				  </span>
				  <strong><?php echo $option['option_name']; ?></strong>: <?php echo $option['value_name']; ?>
				  <?php if($option['option_price'] > 0) {  ?>
				  (<?php echo $option['option_symbol'].$option['option_price']; ?>)
				  <?php
				  }
				  ?>
				</div>
		<?php
				}
			}
		}
		## Product Options (Current - Text)
		$optionsct_query	= sprintf("SELECT B.*, T.* FROM %1\$sCubeCart_options_bot AS B, %1\$sCubeCart_options_top AS T WHERE B.option_id = T.option_id AND B.product = '%2\$d' AND T.option_type != '0' ORDER BY T.option_name ASC", $glob['dbprefix'], $results[0]['productId']);
		$optionsct_result	= $db->select($optionsct_query);

		if ($optionsct_result) {
			foreach ($optionsct_result as $key => $option) {
				$option['option_data'] = sprintf('%d|%d|%s', $option['option_id'], 0, $option['option_price']);
				?>
			<div id="option_<?php echo $option['assign_id']; ?>" style="clear: right;">
			  <span style="float: right; text-align: right;">
				<a href="#" onclick="optionEdit('<?php echo $option['assign_id']; ?>', '<?php echo $option['option_data']; ?>'); return false;"><img src="images/icons/edit.png" alt="edit" /></a>
				<a href="#" onclick="optionRemove('<?php echo $option['assign_id']; ?>'); return false;"><img src="images/icons/delete.png" alt="delete" /></a>
			  </span>
			  <strong>Custom</strong>: <?php echo $option['option_name']; ?> (<?php echo $option['option_symbol'].$option['option_price']; ?>)
			</div>
		<?php
			}
		}

		?>
		</div>
		</td>
	  </tr>
	  <tr>
	  	<td colspan="2">&nbsp;</td>
	  </tr>
	  <tr>
		<td width="25%" align="left" valign="top" class="tdText"><strong>&nbsp;</strong></td>
		<td align="left">

		<select id="opt_mid" class="textbox">
		<option value="">Select Option</option>
		<?php
			## Product Options (Additional)
			if ($options_result) {
				foreach ($options_result as $option) {
					if ($option['option_type'] == '0') {
						$valquery = sprintf("SELECT * FROM %sCubeCart_options_mid WHERE `father_id` = '%d' ORDER BY `value_name` ASC;", $glob['dbprefix'], $option['option_id']);
						$values_result = $db->select($valquery);

						if ($values_result) {
							echo sprintf('<optgroup id="%d" label="%s">', $option['option_id'], $option['option_name']);
							foreach ($values_result as $value) {
								echo sprintf('<option value="%d" class="sub">%s</option>', $value['value_id'], $value['value_name']);
							}
							echo '</optgroup>';
						}
					} else {
						## New textbox/textarea options
						echo sprintf('<option value="%d" class="top">%s</option>', $option['option_id'], $option['option_name']);
					}
				}
			}
		?>
		</select>
		<input type="hidden" id="opt_assign_id" value="0" />
		<input type="text" id="opt_price" value="0.00" class="textbox" />
		<input type="submit" value="Add Option" onclick="optionAdd(); return false;" class="submit" />
		</td>
	  </tr>
	  <tr>
	    <td width="25%">&nbsp;</td>
    	<td><input name="Submit" type="submit" class="submit" value="<?php echo $modeTxt." ".$lang['admin']['products_product'];?>" /></td>
  	  </tr>
</table>

<?php
}
if ($config['seftags']) {
?>
<br />
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
	  <tr>
		<td colspan="2" class="tdTitle"><strong><?php echo $lang['admin']['products_meta_data'];?></strong></td>
	  </tr>
	  <tr>
		<td width="30%" class="tdText"><strong><?php echo $lang['admin']['products_custom_url'];?>:</strong></td>
		<td align="left"><input name="seo_custom_url" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['seo_custom_url'])) echo $results[0]['seo_custom_url']; ?>" /></td>
	  </tr>
	  <tr>
		<td width="30%" class="tdText"><strong><?php echo $lang['admin']['products_browser_title'];?></strong></td>
		<td align="left"><input name="prod_metatitle" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['prod_metatitle'])) echo htmlentities($results[0]['prod_metatitle'],ENT_QUOTES,'UTF-8'); ?>" /></td>
	  </tr>
	  <tr>
		<td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['products_meta_desc'];?></strong></td>
		<td align="left"><textarea name="prod_metadesc" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['prod_metadesc'])) echo $results[0]['prod_metadesc']; ?></textarea></td>
	  </tr>
	  <tr>
		<td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['products_meta_keywords'];?></strong> <?php echo $lang['admin']['settings']['comma_separated'];?></td>
		<td align="left"><textarea name="prod_metakeywords" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['prod_metakeywords'])) echo $results[0]['prod_metakeywords']; ?></textarea></td>
	  </tr>
	  <tr>
    <td width="25%">&nbsp;</td>
    <td>
	<input name="Submit" type="submit" class="submit" value="<?php echo $modeTxt." ".$lang['admin']['products_product'];?>" /></td>
  </tr>
</table>
<?php
}
?>

<br />

<div class="tdText"><em><u><strong><?php echo $lang['admin']['products_digi_info'];?></strong></u></em>
<?php echo $lang['admin']['products_digi_desc'];?>
</div>
</form>
<?php
}
?>