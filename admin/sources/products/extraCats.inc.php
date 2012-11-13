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
|	extraCats.inc.php
|   ========================================
|	Add/Edit/Delete Products in Multiple Categories
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_products.inc.php');
$skipFooter = true;

	// add
	if(isset($_GET['add']) && $_GET['add']>0)
	{

		$cache = new cache();
		$cache->clearCache();

		$record['cat_id'] = $db->mySQLSafe($_GET['add']);
		$record['productId'] = $db->mySQLSafe($_GET['productId']);

		$insert = $db->insert($glob['dbprefix'].'CubeCart_cats_idx', $record);
		unset($record);

		if($insert)
		{
			$msg = "<p class='infoText'>".$lang['admin']['products_prod_added_to_cat']."</p>";

			// set category +1
			$db->categoryNos($_GET['add'], "+");

		}
		else
		{
			$msg = "<p class='warnText'>".$lang['admin']['products_prod_not_added_to_cat']."</p>";
		}

	}
	elseif(isset($_GET['remove']) && $_GET['remove']>0)
	{

		$cache = new cache();
		$cache->clearCache();

		$where = '`cat_id` = '.$db->mySQLSafe($_GET['remove']).' AND `productId` = '.$db->mySQLSafe($_GET['productId']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_cats_idx', $where);

		if ($delete) {
			$msg = "<p class='infoText'>".$lang['admin']['products_prod_removed_from_cat']."</p>";
			// set category - 1
			$db->categoryNos($_GET['remove'], '-');
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_prod_not_removed_from_cat']."</p>";
		}

	}


	// get array of existing categories product relation ships
	$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_cats_idx WHERE `productId` = %s', $db->mySQLSafe($_GET['productId']));
	$assocArray = $db->select($query);

	for ($i = 0, $maxi = count($assocArray); $i < $maxi; ++$i)
	{
		$catKey = $assocArray[$i]['cat_id'];
		$catIndex[$catKey] = $assocArray[$i]['cat_id'];
	}

	// make sql query
	$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_category WHERE `cat_id` <> %s', $db->mySQLSafe((int)$_GET['cat_id']));
	// query database
	$results = $db->select($query, 15, $_GET['page']);
	$excluded = array('add' => 1, 'remove' => 1);
	$pagination = paginate($db->numrows($query), 15, $_GET['page'], 'page', 'txtLink', 5, $excluded);

	$currentPage = currentPage($excluded);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
	<head>
		<title><?php echo $lang['admin']['products_title_extraCats'];?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $glob['adminFolder']; ?>/styles/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>
	<p class="pageTitle"><?php echo $lang['admin']['products_manage_cats'];?> - <?php echo $_GET['name']; ?></p>
	<p class="copyText"><strong><?php echo $lang['admin']['products_master_cat'];?></strong> <span class="txtDir"><?php echo getCatDir(html_entity_decode(urldecode($_GET['cat_name'])),sanitizeVar($_GET['cat_father_id']), sanitizeVar((int)$_GET['cat_id']));?></span></p>
	<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
	<p align="right" class="copyText"><?php echo $pagination; ?></p>
	<table border="0" width="100%" cellspacing="1" cellpadding="3" class="mainTable">
      <tr>
        <td class="tdTitle"><?php echo $lang['admin']['products_category2'];?></td>
        <td align="center" class="tdTitle"><?php echo $lang['admin']['products_action'];?></td>
      </tr>
        <?php
  	if($results){

  		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

		$cellColor = '';
		$cellColor = cellColor($i);
	?>
	  <tr>
        <td class="<?php echo $cellColor; ?>"><span class="txtDir"><?php echo getCatDir($results[$i]['cat_name'],$results[$i]['cat_father_id'], $results[$i]['cat_id']);?></span></td>
        <td align="center" class="<?php echo $cellColor; ?>">

		<?php
		$currentCat = $results[$i]['cat_id'];
		if(isset($catIndex[$currentCat])){ ?>
		<a href="<?php echo $currentPage; ?>&amp;remove=<?php echo $results[$i]['cat_id']; ?>" class="txtLink">Remove</a>
		<?php } else { ?>
		<a href="<?php echo $currentPage; ?>&amp;add=<?php echo $results[$i]['cat_id']; ?>" class="txtLink">Add</a>
		<?php } ?>
		</td>
      </tr>
	  <?php }
	 }
	 ?>
    </table>
	<p align="right" class="copyText"><?php echo $pagination; ?></p>
	<p align="center"><a href="javascript:window.close();" class="txtLink"><?php echo $lang['admin']['products_close_window'];?></a></p>
	</body>
</html>
