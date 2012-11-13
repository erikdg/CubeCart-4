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
|	languages.inc.php
|   ========================================
|	Add/Edit/Delete Products in Other Languages
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_products.inc.php');

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'rte'.CC_DS.'fckeditor.php');

permission('products','read',true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

$path = CC_ROOT_DIR.CC_DS.'language';

// delete document
if (isset($_GET['delete']) && $_GET['delete']>0) {

	$cache = new cache();
	$cache->clearCache();

	$where = '`id` = '.$db->mySQLSafe($_GET['delete']);

	$delete = $db->delete($glob['dbprefix'].'CubeCart_inv_lang', $where);

	if($delete){
		$msg = "<p class='infoText'>".$lang['admin']['products_delete_success']."</p>";
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['products_delete_fail']."</p>";
	}

} elseif(isset($_POST['id']) && $_POST['id']>0) {

	$cache = new cache();
	$cache->clearCache();

	$record['name'] = $db->mySQLSafe($_POST['name']);
	$record['prod_lang'] = $db->mySQLSafe($_POST['prod_lang']);
	$record['prod_master_id'] = $db->mySQLSafe((int)$_GET['prod_master_id']);
	$fckEditor = (detectSSL() && !$config['force_ssl']) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record['description'] = $db->mySQLSafe($fckEditor);

	$where = '`id` = '.$db->mySQLSafe($_POST['id']);

	$update = $db->update($glob['dbprefix']."CubeCart_inv_lang", $record, $where);

	if($update) {
		$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_update_successful']."</p>";
	} else {
		$msg = "<p class='infoText'>'".$lang['admin']['products_update_fail']."</p>";
	}

} elseif(isset($_POST['id']) && empty($_POST['id'])) {

	$cache = new cache();
	$cache->clearCache();

	$record['name'] = $db->mySQLSafe($_POST['name']);
	$record['prod_lang'] = $db->mySQLSafe($_POST['prod_lang']);
	$record['prod_master_id'] = $db->mySQLSafe((int)$_GET['prod_master_id']);
	$fckEditor = (detectSSL() && !$config['force_ssl']) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['FCKeditor']) : $_POST['FCKeditor'];
	$record['description'] = $db->mySQLSafe($fckEditor);

	$insert = $db->insert($glob['dbprefix'].'CubeCart_inv_lang', $record);

	if($insert){
		$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['products_add_success']."</p>";
	} else {
		$msg = "<p class='infoText'>".$lang['admin']['products_add_fail']."</p>";
	}

}

// retrieve current documents
if($_GET['mode']!='new') {

	$cache = new cache();
	$cache->clearCache();

	// make sql query
	if(isset($_GET['edit']) && (int)$_GET['edit']>0){
		$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inv_lang WHERE `id` = %s', $db->mySQLSafe((int)$_GET['edit']));
	} else {
		$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inv_lang WHERE `prod_master_id` = %s ORDER BY `name` ASC', $db->mySQLSafe((int)$_GET['prod_master_id']));
	}

	// query database
	$results = $db->select($query);
} // end if mode is not new
$options = '';
foreach (glob($path.CC_DS.'*') as $langpath) {
	$folder = basename($langpath);
	if (is_dir($langpath) && preg_match('#^[a-z]{2}(\_[A-Z]{2})?$#iuU', $folder) && $folder != $config['defaultLang']) {
		if (file_exists($langpath.CC_DS.'config.php')) {
			include $langpath.CC_DS.'config.php';
			if($results[0]['prod_lang']==$folder) {
				$selected = ' selected="selected"';
			} elseif($_GET['mode']=='new' && $config['defaultLang']==$folder) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$options .= sprintf('<option value="%s"%s>%s</option>', $folder, $selected, $langName);
		}
	}
}

?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle"><?php echo $lang['admin']['products_prod_inv_langs'];?></p></td>
    <?php if($_GET['mode']!="new"){ ?><td align="right" valign="middle"><a <?php if(permission('documents','write')){?>href="<?php echo $glob['adminFile']; ?>?_g=products/languages&amp;mode=new&amp;prod_master_id=<?php echo (int)$_GET['prod_master_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?>><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" /><?php echo $lang['admin_common']['add_new'];?></a></td><?php } ?>
  </tr>
</table>
<?php
if((isset($_GET['edit']) && (int)$_GET['edit']>0) || $_GET['mode']=='new'){

	if(empty($options)){
		echo "<p class='copyText'>".$lang['admin']['products_no_langs']."</p>";
	} else {

	if($_GET['mode']=='new'){
		// get recordset of old doc to translate
		$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE `productId` = %s', $db->mySQLSafe((int)$_GET['prod_master_id']));
		$results = $db->select($query);
	}
?>

<form action="<?php echo $glob['adminFile']; ?>?_g=products/languages&amp;prod_master_id=<?php echo (int)$_GET['prod_master_id']; ?>" target="_self" method="post" language="javascript">
<p class="copyText"><?php echo $lang['admin']['products_use_rich_oth_lang'];?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['products_prod_inv_langs'];?></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong><?php echo $lang['admin']['products_prod_name2'];?></strong></span> <input name="name" class="textbox" value="<?php echo $results[0]['name']; ?>" type="text" maxlength="255" /></td>
  </tr>
  <tr>
    <td class="tdRichText"><span class="copyText"><strong><?php echo $lang['admin']['products_language'];?></strong></span><?php
		?>
		<select class="textbox" name="prod_lang">
		<?php
			echo $options;
		?>
		</select>

	</td>
  </tr>
  <tr>
    <td class="tdRichText">
<?php
$oFCKeditor = new FCKeditor('FCKeditor');
$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
$oFCKeditor->Value = $results[0]['description'];
if($config['richTextEditor']==0) {
	$oFCKeditor->off = true;
}
$oFCKeditor->Create();
?></td>
  </tr>
  <tr>
    <td class="tdRichText">
	<input type="hidden" value="<?php echo (int)$_GET['edit']; ?>" name="id" />
	<input name="submit" type="submit" class="submit" id="submit" <?php if($_GET['mode']!="new"){ ?>value="<?php echo $lang['admin']['products_update_prod_lang'];?>"<?php } else { echo "value=\"".$lang['admin']['products_save_prod_lang']."\""; } ?> /></td>
  </tr>
</table>
</form>
<?php
 	}

} else {
	if(isset($msg)){
		echo msg($msg);
	} else {
	?>
	<p class="copyText"><?php echo $lang['admin']['products_other_langs_exdefault'];?></p>
	<?php } ?>
	<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
	  <tr>
		<td class="tdTitle" width="80%"><?php echo $lang['admin']['products_product_name'];?></td>
		<td class="tdTitle" colspan="2" align="center" width="20%"><?php echo $lang['admin']['products_action'];?></td>
	  </tr>
	  <?php
	  if($results){

	  	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

		$cellColor = '';
		$cellColor = cellColor($i);

	  ?>
	  <tr>
		<td width="80%" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['name']; ?></span> <img src="language/<?php echo $results[$i]['prod_lang']; ?>/flag.gif" alt="" title="" /></td>

		<td align="center" width="10%" class="<?php echo $cellColor; ?>">
		<a <?php if(permission('documents','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/languages&amp;edit=<?php echo $results[$i]['id']; ?>&amp;prod_master_id=<?php echo (int)$_GET['prod_master_id']; ?>" class="txtLink"<?php } else { echo $link401; } ?> ><?php echo $lang['admin_common']['edit'];?></a>
		</td>

		<td align="center" width="5%" class="<?php echo $cellColor; ?>">
		<a <?php if(permission('documents','delete')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/languages&amp;delete=<?php echo $results[$i]['id']; ?>&amp;prod_master_id=<?php echo (int)$_GET['prod_master_id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q'])); ?>')" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['delete'];?></a>
		</td>

	  </tr>
	  <?php } // end loop

	  } else { ?>

	   <tr>
		<td colspan="3" class="tdText"><?php echo $lang['admin']['products_no_lang_exist'];?></td>
	  </tr>

	  <?php } ?>

	</table>

	<?php
}
?>