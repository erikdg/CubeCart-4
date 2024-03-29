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
|	Manage Categories
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_categories.inc.php');

permission('categories', 'read', true);

$catsPerPage = 25;

if (isset($_POST['saveOrder']) && !empty($_POST['priority'])) {
	## Handler for drag/drop reordering
	foreach ($_POST['priority'] as $index => $cat_id) {
		$sql = sprintf("UPDATE %sCubeCart_category SET `priority` = '%d' WHERE `cat_id` = '%d' LIMIT 1;", $glob['dbprefix'], $index+1, $cat_id);
		$db->misc($sql);
	}
	$cache = new cache();
	$cache->clearCache();

	## Rebuild the cached list
	buildCatList();
}

if (isset($_GET['move']) && isset($_GET['to'])) {
	## Handler for roundtrip reordering
	switch ($_GET['dir']) {
		case 'up':
			$db->update($glob['dbprefix'].'CubeCart_category', array('priority' => (int)$_GET['to']+2), array('priority' => $_GET['to']+1, 'cat_father_id' => $_GET['parent']));
			break;
		case 'down':
			$db->update($glob['dbprefix'].'CubeCart_category', array('priority' => (int)$_GET['to']), array('priority' => $_GET['to']+1, 'cat_father_id' => $_GET['parent']));
			break;
	}

	$db->update($glob['dbprefix'].'CubeCart_category', array('priority' => (int)$_GET['to']+1), array('cat_id' => (int)$_GET['move']));
	$cache = new cache();
	$cache->clearCache();

	## Rebuild the cached list
	buildCatList();
	httpredir('?_g=categories/index&parent='.(int)$_GET['parent']);
}

if (isset($_GET['hide'])) {
	$cache = new cache();
	$cache->clearCache();

	$record['hide']	= sprintf("'%d'", $_GET['hide']);
	$where			= '`cat_id` = '.$db->mySQLSafe((int)$_GET['cat_id']);
	$update			= $db->update($glob['dbprefix'].'CubeCart_category', $record, $where);

	$msg	= ($update) ? "<p class='infoText'>".ucfirst($lang['admin']['categories_update_success'])."</p>" : "<p class='warnText'>".ucfirst($lang['admin']['categories_update_fail'])."</p>";

	## Rebuild the cached list
	buildCatList();

} else if (isset($_GET['delete']) && $_GET['delete']>0) {

	$cache = new cache();
	$cache->clearCache();

	// delete index
	$where = '`cat_id` = '.$db->mySQLSafe($_GET['delete']);
	$deleteIdx = $db->delete($glob['dbprefix'].'CubeCart_cats_idx', $where);

	// delete category
	$where = "cat_id=".$db->mySQLSafe($_GET['delete']);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_category', $where);

	$msg = ($delete) ? "<p class='infoText'>".$lang['admin']['categories_delete_success']."</p>" : "<p class='warnText'>".$lang['admin']['categories_delete_failed']."</p>";
	## Rebuild the cached list
	buildCatList();

} else if (isset($_POST['cat_id'])) {

	$cache = new cache();
	$cache->clearCache();

	$record['cat_name'] = $db->mySQLSafe($_POST['cat_name']);
	$record['cat_father_id'] = $db->mySQLSafe($_POST['cat_father_id']);
	$record['cat_image'] = $db->mySQLSafe(imgPath($_POST['imageName'], false, ''));
	$record['per_ship'] = $db->mySQLSafe($_POST['per_ship']);
	$record['item_ship'] = $db->mySQLSafe($_POST['item_ship']);
	$record['item_int_ship'] = $db->mySQLSafe($_POST['item_int_ship']);
	$record['per_int_ship'] = $db->mySQLSafe($_POST['per_int_ship']);

	$fckEditor = (detectSSL() && !$config['force_ssl']) ?  str_replace($config['rootRel_SSL'], $glob['rootRel'], $_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record['cat_desc'] = $db->mySQLSafe(str_replace('##HIDDEN##', '', $fckEditor));

	if ($config['seftags']) {
		$record['seo_custom_url']	= $db->mySQLSafe($_POST['seo_custom_url']);
		$record['cat_metatitle']	= $db->mySQLSafe($_POST['cat_metatitle']);
		$record['cat_metadesc']		= $db->mySQLSafe($_POST['cat_metadesc']);
		$record['cat_metakeywords']	= $db->mySQLSafe($_POST['cat_metakeywords']);
	}

	if (is_numeric($_POST['cat_id'])) {
		// update product count. This is gonna be tricky!!!
		if ($_POST['oldFatherId'] != $_POST['cat_father_id'] && $_POST['noProducts']>0) {
			// change old count
			$db->categoryNos($_POST['oldFatherId'], '-', $_POST['noProducts']);
			// update new count
			$db->categoryNos($_POST['cat_father_id'], '+', $_POST['noProducts']);
		}

		$where = '`cat_id` = '.$db->mySQLSafe($_POST['cat_id']);
		$update = $db->update($glob['dbprefix'].'CubeCart_category', $record, $where);

		$msg = ($update) ? "<p class='infoText'>'".$_POST['cat_name']."' ".$lang['admin']['categories_update_success']."</p>" : "<p class='warnText'>".$lang['admin']['categories_update_fail']."</p>";
	} else {
		$record['noProducts'] = 0;
		$insert = $db->insert($glob['dbprefix'].'CubeCart_category', $record);
		$db->update($glob['dbprefix'].'CubeCart_category', array('priority' => $db->insertid()), array('cat_id' => $db->insertid()));
		$msg = ($insert) ? "<p class='infoText'>'".$_POST['cat_name']."' ".$lang['admin']['categories_add_success']."</p>" : "<p class='warnText'>".$lang['admin']['categories_add_failed']."</p>";

	}
	## Rebuild the cached list
	buildCatList();
}

if (!isset($_GET['mode'])) {
	// make sql query
	if (isset($_GET['edit']) && (int)$_GET['edit'] > 0) {
		$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_category WHERE `cat_id` = %s', $db->mySQLSafe((int)$_GET['edit']));
	} else {
		$whereClause = (is_numeric($_GET['parent'])) ? sprintf("`cat_father_id` = '%d'", $_GET['parent']) : '`cat_father_id` = 0';
		$query = 'SELECT * FROM '.$glob['dbprefix']."CubeCart_category WHERE (`cat_name` != 'Imported Products' OR `cat_desc` != '##HIDDEN##') AND ".$whereClause.' ORDER BY priority, cat_name ASC';
	}

	// query database
	$results = $db->select($query);
}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
?>
<div>
<?php
if (!isset($_GET['mode'])) {
	$url = (permission('categories','write')) ? 'href="?_g=categories/index&amp;mode=new&amp;parent='.$_GET['parent'].'" class="txtLink"' : $link401;
	echo sprintf('<span style="float: right;"><a %s><img src="%s" alt="" hspace="4" />%s</a></span>', $url, $glob['adminFolder'].'/images/buttons/new.gif', $lang['admin_common']['add_new']);
}
?>
  <p class="pageTitle">
	<?php
	echo $lang['admin']['categories_categories'];
	?>
  </p>
</div>
<?php
if(isset($msg)) echo msg($msg);

if (!isset($_GET['mode']) && !isset($_GET['edit'])) {
?>
<p class="copyText"><?php echo $lang['admin']['categories_categories_desc']; ?></p>

<p class="copyText"><strong><?php echo $lang['admin']['categories_loc']; ?></strong> <a href="?_g=categories/index" class="txtLink"><?php echo $lang['admin']['categories_loc_home']; ?></a><?php if (is_numeric($_GET['parent'])) echo getCatDir('', $_GET['parent'], 0, true, false, true, true); ?></p>
<form method="post" id="reorderCategory" enctype="multipart/form-data" action="<?php echo $glob['adminFile']; ?>?_g=categories/index">

<div id="columnHeaders" class="tdTitle">
  <span class="actions"><?php echo $lang['admin']['categories_action']; ?></span>
  <span class="catName"><?php echo "ID / ".$lang['admin']['categories_cat_name']; ?></span>
  <?php echo $lang['admin']['categories_dir']; ?>
</div>

<?php
if ($results) {
	$count = count($results)-1;
	foreach ($results as $i => $result) {
		$cellColor	= cellColor($i);
		$sql		= sprintf("SELECT `cat_id` FROM %sCubeCart_category WHERE `cat_father_id` = '%d'", $glob['dbprefix'], $results[$i]['cat_id']);
		$subcat		= $db->numrows($sql);

?>
<div id="product_<?php echo $results[$i]['cat_id']; ?>" class="productRow <?php echo $cellColor; ?> tdText">
  <span class="actions">
  	<span class="action"><?php
		switch($results[$i]['hide']) {
			case 0:
				$url	= (permission('categories','edit')) ? 'href="?_g=categories/index&amp;hide=1&amp;cat_id='.$results[$i]['cat_id'].'" class="txtLink"' : $link401;
				$title	= $lang['admin_common']['hide'];
				break;
			case 1:
				$url	= (permission('categories','edit')) ? 'href="?_g=categories/index&amp;hide=0&amp;cat_id='.$results[$i]['cat_id'].'" class="txtLink"' : $link401;
				$title	= $lang['admin_common']['show'];
				break;
		}
		echo sprintf('<a %s>%s</a>', $url, $title);
	?></span>
	<span class="action"><?php
		$url = (permission('categories','edit'))? 'href="?_g=categories/languages&amp;cat_master_id='.$results[$i]['cat_id'].'" class="txtLink"' : $link401;
		echo sprintf('<a %s>%s</a>', $url, $lang['admin']['categories_languages']);
	?></span>
	<span class="action"><?php
		if (permission('categories','delete')) {
			if ($results[$i]['noProducts'] <= 0  && !$subcat) {
				$url = 'href="?_g=categories/index&amp;delete='.$results[$i]['cat_id'].'&amp;cat_father_id='.$results[$i]['cat_id'].'" onclick=" return confirm(\''.str_replace("\n", '\n', $lang['admin_common']['delete_q']).'\');" class="txtLink"';
			} else {
				$url = 'href="#" onclick="alert(\''.$lang['admin']['categories_cannot_del'].'\')" class="txtNullLink"';
			}
		} else {
			$url = $link401;
		}
		echo sprintf('<a %s>%s</a>', $url, $lang['admin_common']['delete']);
	?></span>
	<span class="action"><?php
		$url = (permission('categories','edit')) ? 'href="?_g=categories/index&amp;edit='.$results[$i]['cat_id'].'" class="txtLink"' : $link401;
		echo sprintf('<a %s>%s</a>', $url, $lang['admin_common']['edit']);
	?></span>
	<?php if (permission('categories','edit')) { ?>
	<span class="action">
	<?php if ($i >= 1) { ?>
	  <a href="?_g=categories/index&amp;move=<?php echo $results[$i]['cat_id']; ?>&amp;to=<?php echo $i-1; ?>&amp;parent=<?php echo $results[$i]['cat_father_id']; ?>&amp;dir=up">
	  <img src="<?php echo $glob['rootRel']; ?>images/admin/arrow_up.gif" border="0" /></a>
	<?php  } ?>
	<?php if ($i < $count) { ?>
	  <a href="?_g=categories/index&amp;move=<?php echo $results[$i]['cat_id']; ?>&amp;to=<?php echo $i+1; ?>&amp;parent=<?php echo $results[$i]['cat_father_id']; ?>&amp;dir=down">
	  <img src="<?php echo $glob['rootRel']; ?>images/admin/arrow_down.gif" /></a>
	<?php } ?>
	</span>
	<?php } ?>
  </span>


  <span class="catName">
  <?php
	echo ($subcat >= 1) ? sprintf('%s. <a href="?_g=categories/index&amp;parent=%s" class="txtLink" title="View Subcategories">%s</a>', $results[$i]['cat_id'], $results[$i]['cat_id'], $results[$i]['cat_name']) : $results[$i]['cat_id'].". ".$results[$i]['cat_name'];
  ?>
  </span>
  <span class="directory"><?php echo getCatDir($results[$i]['cat_name'],$results[$i]['cat_father_id'], $results[$i]['cat_id']);?> - (<?php echo $subcat; ?> child)</span>
  <input type="hidden" name="priority[]" value="<?php echo $results[$i]['cat_id']; ?>" />
  (<?php echo $results[$i]['priority']; ?>)
</div>
<?php
	} // end loop
} else {
?>
  <div class="tdText"><?php echo $lang['admin']['categories_no_cats_exist'];?></div>
<?php } ?>
  <p>To re-order the categories, drag and drop them into your prefered order, then save</p>
  <p><input type="submit" class="submit" name="saveOrder" value="Save Order" /></p>
</form>
<script type="text/javascript">
	Sortable.create('reorderCategory', {ghosting:true,constraint:false,tag:'div',only:'productRow'});
</script>

<?php
} else if ($_GET['mode']=='new' || (int)$_GET['edit'] > 0){

if(isset($_GET['edit']) && (int)$_GET['edit']>0){ $modeTxt = $lang['admin_common']['edit']; } else { $modeTxt = $lang['admin_common']['add']; }
?>
<p class="copyText"><?php echo $lang['admin']['categories_add_desc'];?></p>
<form action="?_g=categories/index" method="post" enctype="multipart/form-data" name="form1">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET['edit']) && (int)$_GET['edit']>0){ echo $modeTxt; } else { echo $modeTxt;  }  ?> <?php echo $lang['admin']['categories_category'];?></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_category_name'];?></td>
    <td>
      <input name="cat_name" type="text" class="textbox" value="<?php if(isset($results[0]['cat_name'])) echo $results[0]['cat_name']; ?>" maxlength="255" />
      <?php
      if($results[0]['cat_id']>0){
      	$currentDirectory = getCatDir($results[0]['cat_name'],$results[0]['cat_father_id'], $results[0]['cat_id'],$link=false);
      	echo $currentDirectory;
      }
      ?>

      </td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_category_desc'];?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" class="tdText">
	<?php

		require($glob['adminFolder'].'/includes'.CC_DS.'rte'.CC_DS.'fckeditor.php');
		$oFCKeditor = new FCKeditor('FCKeditor');
		$oFCKeditor->BasePath = $glob['rootRel'].$glob['adminFolder'].'/includes/rte/' ;
		$oFCKeditor->Value = (isset($results[0]['cat_desc'])) ? $results[0]['cat_desc'] : $oFCKeditor->Value = '';
		if (!$config['richTextEditor']) $oFCKeditor->off = true;
		$oFCKeditor->Create();

?>
</td>
    </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_category_level'];?></td>
    <td>
    <?php
	$query 			= "SELECT `cat_name`, `cat_father_id`, `cat_id`, `cat_image` FROM `".$glob['dbprefix']."CubeCart_category` WHERE `cat_desc` != '##HIDDEN##' OR `cat_desc` IS NULL"; ## ORDER BY NOT NEEDED DUE TO NATCASESORT WHICH IS MORE TRUST WORTHY TO GET ALL DATA
	$categoryArray	= $db->select($query);
	?>
	<select name="cat_father_id" class="textbox">
	<option value="0"><?php echo $lang['admin']['categories_top_level'];?></option>
	<?php

	## ADD CATEGORY CACHING HERE
	$catCache = new cache('menu.category.'.$config['defaultLang']);
	if ($catCache->cacheStatus) {
		$catListCached = $catCache->readCache();
		$catListCached = str_replace('selected="selected"', '', $catListCached);
		if($_GET['parent']) {
			$catListCached = str_replace('value="'.$_GET['parent'].'"', 'value="'.$_GET['parent'].'" selected="selected"', $catListCached);
		} else {
			$catListCached = str_replace('value="'.$results[0]['cat_father_id'].'"', 'value="'.$results[0]['cat_father_id'].'" selected="selected"', $catListCached);
		}
		$catList = explode("\n", $catListCached);
	} else {
		for ($i = 0, $maxi = count($categoryArray); $i < $maxi; ++$i){
			$cat_id		= $categoryArray[$i]['cat_id'];
			$selected	= ((isset($results[0]['cat_father_id']) && $categoryArray[$i]['cat_id'] == $results[0]['cat_father_id']) || ($_GET['parent'])) ? ' selected="selected"' : '';
			$cat_dir	= getCatDir($categoryArray[$i]['cat_name'], $categoryArray[$i]['cat_father_id'], $categoryArray[$i]['cat_id']);
			$catList[]	= '<!--'.$cat_dir.'--><option value="'.$cat_id.'"'.$selected.'>'.$cat_dir.'</option>';
		}
		natcasesort($catList);
		$cachText = implode("\n", $catList);
		$cachText = strip_tags($cachText,'<option>');
		$catCache->writeCache($cachText);
	}

	if (is_array($catList)) {
		foreach($catList as $value){
			echo stristr($value, $currentDirectory) ? "" : strip_tags($value,'<option>')."\n";
		}
	}

	?>
	</select>
	</td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdText"><?php echo $lang['admin']['categories_image_optional'];?></td>
    <td valign="top">
	<?php
	if(!empty($results[0]['cat_image'])) {
		$imgSrc = imgPath($results[0]['cat_image'],0,'rel');
	} else {
		$imgSrc = $GLOBALS['storeURL'].'/images/general/px.gif';
	}
	?>
	<img src="<?php echo $imgSrc; ?>" alt="" id="previewImage" title="" />
	<div>
	  <input name="upload" style="width: 200px;" class="submit" type="button" id="upload" onclick="openPopUp('<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/includes/rte/editor/filemanager/browser/default/browser.html?Type=uploads&amp;Connector=<?php echo urlencode($GLOBALS['rootRel'].$glob['adminFolder']); ?>%2Fincludes%2Frte%2Feditor%2Ffilemanager%2Fconnectors%2Fphp%2Fconnector.php','filemanager',700,600)" value="Browse / Upload Image" />
	  <input type="button" class="submit" value="Remove Image" onclick="findObj('previewImage').src='<?php echo $glob['adminFolder']; ?>/images/general/px.gif';findObj('imageName').value = '';" />
	  <input type="hidden" name="imageName" id="imageName" value="<?php if(isset($results[0]['cat_image'])) echo $results[0]['cat_image']; ?>" />
	</div>
	</td>
  </tr>
  <?php
  $module = fetchDbConfig('Per_Category');
  if($module['status'] == 1) {
  ?>
  <tr>
    <td colspan="2" class="tdTitle"><?php echo $lang['admin']['categories_ship_by_cat']; ?></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_per_ship']; ?></td>
    <td><input name="per_ship" value="<?php echo $results[0]['per_ship']; ?>" type="text" class="textbox" size="6" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_per_item']; ?></td>
    <td><input name="item_ship" value="<?php echo $results[0]['item_ship']; ?>" type="text" class="textbox" size="6" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_per_int_ship']; ?></td>
    <td><input name="per_int_ship" value="<?php echo $results[0]['per_int_ship']; ?>" type="text" class="textbox" size="6" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['categories_per_int_item']; ?></td>
    <td><input name="item_int_ship" value="<?php echo $results[0]['item_int_ship']; ?>" type="text" class="textbox" size="6" /></td>
  </tr>

  <?php } ?>
   <tr>
    <td>&nbsp;</td>
    <td>
	<input type="hidden" name="noProducts" value="<?php echo $results[0]['noProducts']; ?>" />
	<input type="hidden" name="oldFatherId" value="<?php echo $results[0]['cat_father_id']; ?>" />
	<input type="hidden" name="cat_id" value="<?php echo $results[0]['cat_id']; ?>" />
	<input name="Submit" type="submit" class="submit" value="<?php echo $modeTxt; ?> <?php echo $lang['admin']['categories_category'];?>" /></td>
  </tr>
  </table>

  <?php if ($config['seftags']) { ?><br />

                <table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
                  <tr>
                    <td colspan="2" class="tdTitle"><strong><?php echo $lang['admin']['categories_meta_data']; ?></strong></td>
                  </tr>
				  <tr>
					<td width="30%" class="tdText"><strong><?php echo $lang['admin']['category_custom_url'];?></strong></td>
					<td align="left"><input name="seo_custom_url" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['seo_custom_url'])) echo $results[0]['seo_custom_url']; ?>" /></td>
				  </tr>
                  <tr>
                    <td width="30%" class="tdText"><strong><?php echo $lang['admin']['categories_browser_title']; ?></strong></td>
                    <td align="left"><input name="cat_metatitle" type="text" size="35" class="textbox" value="<?php if(isset($results[0]['cat_metatitle'])) echo $results[0]['cat_metatitle']; ?>" /></td>
                  </tr>
                  <tr>
                    <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['categories_meta_desc'];?></strong></td>
                    <td align="left"><textarea name="cat_metadesc" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['cat_metadesc'])) echo $results[0]['cat_metadesc']; ?></textarea></td>
                  </tr>
                  <tr>
                    <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['categories_meta_keywords'];?></strong> <?php echo $lang['admin']['settings']['comma_separated'];?></td>
                    <td align="left"><textarea name="cat_metakeywords" cols="35" rows="3" class="textbox"><?php if(isset($results[0]['cat_metakeywords'])) echo $results[0]['cat_metakeywords']; ?></textarea></td>
                  </tr>
				  <tr>
    <td>&nbsp;</td>
    <td>
	<input name="Submit" type="submit" class="submit" value="<?php echo $modeTxt; ?> <?php echo $lang['admin']['categories_category'];?>" /></td>
  </tr>
                </table>
<?php } ?>
</form>
<?php
}

?>