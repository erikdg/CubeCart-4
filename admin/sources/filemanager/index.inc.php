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
|	Manage Images on Server
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$lang = getLang('admin'.CC_DS.'admin_filemanager.inc.php');

permission('filemanager', 'read', true);

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

## Include new Filemanager class
include CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'filemanager.class.php';
$filemanager	= new Filemanager();

if (isset($_GET['unlink']) && is_numeric($_GET['unlink'])) {

	## Get file data
	$sql		= sprintf('SELECT * FROM %sCubeCart_filemanager WHERE `file_id` = %d LIMIT 1;', $glob['dbprefix'], $_GET['unlink']);
	$files		= $db->select($sql);

	## Check for dependencies
	$fileName	= $files[0]['filename'];
	$file_id	= $files[0]['file_id'];

	$query		= sprintf("SELECT I.image, C.cat_image FROM %1\$sCubeCart_inventory AS I, %1\$sCubeCart_category AS C WHERE I.image LIKE '%%%2\$s' OR C.cat_image LIKE '%%%2\$s'", $glob['dbprefix'], $files[0]['filename']);
	$results	= $db->select($query);

	$query		= "SELECT `doc_id` FROM ".$glob['dbprefix']."CubeCart_docs WHERE `doc_content` LIKE '%".$files[0]['filename']."%'";
	$siteDocs	= $db->select($query);

	$homepage	= false;

	$homeDocs 	= $db->select("SELECT `langArray` FROM `".$glob['dbprefix']."CubeCart_lang` WHERE `identifier` LIKE '%home.inc.php';");

	if ($homeDocs) {
		foreach($homeDocs as $key) {
			if (stristr($key['langArray'], $files[0]['filename'])) {
				$homepage = true;
				break;
			}
		}
	}
	$idx_path 	= str_replace('images/uploads/','',$files[0]['filepath']);
	$query		= "SELECT img FROM ".$glob['dbprefix']."CubeCart_img_idx WHERE img = '".$idx_path."'";
	$extraImg	= $db->select($query);


	if ($results && !isset($_GET['confirmed'])){
		$msg	= "<p class='warnText'>".sprintf($lang['admin']['filemanager_prod_cat_use_img'],$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&confirmed=1\" onclick=\"return confirm('".str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q']))."')\" class='txtRed'>".$lang['admin']['filemanager_continue_q']."</a></p>";
		$fmhalt = true;

	} else if ($siteDocs && !isset($_GET['confirmed'])) {
		$msg	= "<p class='warnText'>".sprintf($lang['admin']['filemanager_site_doc_use_img'],$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&amp;confirmed=1\" onclick=\"return confirm('Are you sure you want to delete this?');\" class='txtRed'>".$lang['admin']['filemanager_continue_q']."</a></p>";
		$fmhalt = true;

	} else if ($homepage && !isset($_GET['confirmed'])) {
		$msg	= "<p class='warnText'>".sprintf($lang['admin']['filemanager_home_use_img'],$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&amp;confirmed=1\" onclick=\"return confirm('".str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q']))."')\" class='txtRed'>".$lang['admin']['filemanager_continue_q']."</a></p>";
		$fmhalt = true;

	} else if ($extraImg && !isset($_GET['confirmed'])) {
		$msg	= "<p class='warnText'>".sprintf($lang['admin']['filemanager_gallery_use_img'],$fileName)." <a href=\"".$glob['adminFile']."?_g=filemanager/index&amp;unlink=".$file_id."&amp;confirmed=1&amp;idx=1\" onclick=\"return confirm('".str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q']))."')\" class='txtRed'>".$lang['admin']['filemanager_continue_q']."</a></p>";
		$fmhalt = true;

	} else {
		$fmhalt = false;
	}


	## New Filemanager based delete method
	if (is_numeric($_GET['unlink']) && (!$fmhalt || isset($_GET['confirmed']))) {
		if ($filemanager->deleteFile($_GET['unlink'])) {
			$msg = "<p class='infoText'>".$lang['admin']['filemanager_image_deleted']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['filemanager_delete_failed']."</p>";
		}
	}
}
?>
<p class="pageTitle"><?php echo $lang['admin']['filemanager_image_manager']; ?></p>
<?php
if(isset($msg)){
	echo msg($msg);
} else { ?>
<p class="copyText"><?php echo $lang['admin']['filemanager_delete_from_server']; ?></p>
<?php }

if(isset($_GET['page']) && $_GET['page']>0){
	$page = $_GET['page'];
} else {
	$page = '';
}

$thumbsPerPage = 30;


/*
$dirArray = walkDir(CC_ROOT_DIR.CC_DS.'images'.CC_DS.'uploads', true, $thumbsPerPage, $page, false, $int = 0);
$pagination = paginate($dirArray['max'], $thumbsPerPage, $page, 'page', 'txtLink', 10);
*/

$dirArray		= $filemanager->showFileList(FM_FILETYPE_IMG, $page, $thumbsPerPage);

$totalRows		= $db->select("SELECT COUNT(`file_id`) as count FROM ".$glob['dbprefix']."CubeCart_filemanager WHERE `type` = ".FM_FILETYPE_IMG." AND disabled = 0");
$exclude		= array('add' => 1, 'remove' => 1);
$pagination		= paginate($totalRows[0]['count'], $thumbsPerPage, $page, 'page', 'txtLink', 10, $exclude);

?>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle"><?php echo $lang['admin']['filemanager_img_click_prev']; ?></td>
	<td class="tdTitle">&nbsp;</td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['filemanager_size']; ?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['filemanager_action']; ?></td>
  </tr>
<?php
$i = 0;
if (is_array($dirArray)) {
	foreach($dirArray as $fileData) {

		$file = $fileData['filepath'];
		## get root rel link
		$fileRoot	= imgPath($file, false, 'rel');
		$thumbRoot	= imgPath($file, true, 'root');
		$thumbRel	= imgPath($file, true, 'rel');

		if (file_exists($file)) {
			$size = getimagesize($file);
		}
		if (checkImgExt(strtolower($file)) && !stristr($file, 'thumb_')) {
			++$i;
			$cellColor = cellColor($i);

?>
  <tr>
    <td class="<?php echo $cellColor; ?>"><a href="javascript:;" onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=filemanager/preview&amp;file_id=<?php echo $fileData['file_id']; ?>','filemanager',<?php echo $size[0]+14; ?>,<?php echo $size[1]+12; ?>)" class="txtDir"><?php echo $fileData['filepath']; ?></a></td>
	<td align="center" class="<?php echo $cellColor; ?>">
	<?php
	if(file_exists($thumbRoot)) {
	?>
	<a href="javascript:;" onclick="openPopUp('<?php echo $glob['adminFile'];?>?_g=filemanager/preview&amp;file_id=<?php echo $fileData['file_id']; ?>','filemanager',<?php echo $size[0]+14; ?>,<?php echo $size[1]+12; ?>)" class="txtDir">
	<img src="<?php echo $thumbRel; ?>" border="0" alt="" />
	</a>
	<?php
	}
	?>
	</td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($fileData['filesize']); ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>">
	<a <?php if(permission('filemanager','delete')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index&amp;unlink=<?php echo $fileData['file_id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q'])); ?>')" class="txtLink" <?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['delete']; ?></a>

	<?php if($config['gdversion']>0){ ?>
	/

	<a <?php if(permission('filemanager','edit')){ ?>href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=filemanager/resize&amp;file=<?php echo $fileData['filepath']; ?>','filemanager',<?php echo $size[0]+14; ?>,<?php echo $size[1]+120; ?>)" <?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['resize']; ?></a>

	<?php } ?>
	</td>
  </tr>
<?php
			}

		}

	}
	if($i==0) {
	?>
	<tr>
    <td colspan="3" class="tdText"><?php echo $lang['admin']['filemanager_no_images_added'];?></td>
	</tr>
<?php } ?>
</table>
<p class="copyText"><?php echo $pagination; ?></p>