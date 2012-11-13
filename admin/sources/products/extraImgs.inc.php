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
|	extraImgs.inc.php
|   ========================================
|	Add/Edit/Delete Unlimited Extra Product Images
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$skipFooter	= true;
$lang		= getLang('admin'.CC_DS.'admin_products.inc.php');

if (isset($_GET['add']) && !empty($_GET['add'])) {
	$sql = sprintf("SELECT * FROM %sCubeCart_filemanager WHERE `file_id` = '%d' LIMIT 1", $glob['dbprefix'], $_GET['add']);
	$result = $db->select($sql);
	if ($result) {
		$record = array(
			'img' => $db->mySQLSafe(imgPath($result[0]['filepath'], false, '')),
			'productId' => $db->mySQLSafe($_GET['productId']),
		);
		if ($db->insert($glob['dbprefix'].'CubeCart_img_idx', $record)) {
			$msg = '<p class="infoText">'.$lang['admin']['products_img_added_to_prod'].'';
			// Only update count if image were added
			$count['noImages'] = 'noImages + 1';
			$db->update($glob['dbprefix'].'CubeCart_inventory', $count, '`productId` = '.$db->mySQLSafe($_GET['productId']));
		} else {
			$msg = '<p class="warnText">'.$lang['admin']['products_img_not_added_to_prod'].'';
		}
	} else {
		$msg = '<p class="warnText">'.$lang['admin']['products_img_not_added_to_prod'].'';
	}
} else if (isset($_GET['remove']) && !empty($_GET['remove'])) {
	$where = sprintf("`id` = '%d' AND `productId` = '%d'", $_GET['remove'], $_GET['productId']);
	if ($db->delete($glob['dbprefix'].'CubeCart_img_idx', $where)) {
		$msg = "<p class='infoText'>".$lang['admin']['products_img_removed']."</p>";
		// Only update count if image were removed
		$count['noImages'] = "noImages - 1";
		$db->update($glob['dbprefix'].'CubeCart_inventory', $count, '`productId` = '.$db->mySQLSafe($_GET['productId']));
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products_img_not_removed']."</p>";
	}
}

$query		= sprintf('SELECT `id`, `img` FROM %sCubeCart_img_idx WHERE `productId` = %d', $glob['dbprefix'], $_GET['productId']);
$imgArray	= $db->select($query);

if ($imgArray) {
	foreach ($imgArray as $img) {
		$imgIndex[$img['img']] = $img['id'];
	}
}

## Get the main image from the inventory table
$query		= sprintf('SELECT `image` FROM %sCubeCart_inventory WHERE `productId` = %d LIMIT 1;', $glob['dbprefix'], $_GET['productId']);
$product	= $db->select($query);

if ($product) {
	$main_image = $product[0]['image'];
} else if (isset($_GET['img']) && !empty($_GET['img'])) {
	$main_image = $_GET['img'];
}

$currentPage = $_SERVER['PHP_SELF']."?productId=".$_GET['productId']."&amp;img=".$_GET['img'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
	<head>
		<title><?php echo $lang['admin']['products_image_management'];?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $glob['adminFolder']; ?>/styles/style.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>
	<p class="pageTitle"><?php echo $lang['admin']['products_manage_images'];?></p>
	<?php

	$page = (isset($_GET['page']) && $_GET['page']>0)? $_GET['page'] : 0;
	$thumbsPerPage	= 20;

	include CC_ROOT_DIR.CC_DS.'classes'.CC_DS.'filemanager.class.php';
	$filemanager	= new Filemanager();

	$dirArray		= $filemanager->showFileList(FM_FILETYPE_IMG, $page, $thumbsPerPage);

	$totalRows		= $db->select('SELECT COUNT(`file_id`) as count FROM '.$glob['dbprefix'].'CubeCart_filemanager WHERE `type` = '.FM_FILETYPE_IMG.' AND `disabled` = 0');
	$exclude		= array('add' => 1, 'remove' => 1);
	$pagination		= paginate($totalRows[0]['count'], $thumbsPerPage, $page, 'page', 'txtLink', 10, $exclude);

	if (isset($msg)) echo msg($msg);

	echo '<p class="copyText">'.$pagination.'</p>';
?>
	<table border="0" width="100%" cellspacing="1" cellpadding="3" class="mainTable">
      <tr>
        <td class="tdTitle"><?php echo $lang['admin']['products_image'];?></td>
		<td class="tdTitle">&nbsp;</td>
        <td align="center" class="tdTitle"><?php echo $lang['admin']['products_action'];?></td>
      </tr>
        <?php

	if (is_array($dirArray)) {
		foreach ($dirArray as $fileData) {

		$fileRoot = $fileData['filepath'];
		// win switch path
		$imageRootRel	= str_replace('\\','/', imgPath($fileRoot, false, 'root'));
		$imageSize	= @getimagesize($fileRoot);

		$image		= imgPath($fileRoot, false, '');

		$thumbRoot	= imgPath($fileRoot, true, 'root');
		$thumbImg	= imgPath($fileRoot, true, 'rel');


		if (checkImgExt(strtolower($fileRoot)) && !strstr($fileRoot, 'thumb_') && $image!=$main_image){

		++$i;
		$cellColor = cellColor($i);
	?>
	  <tr>
        <td class="<?php echo $cellColor; ?>">
		<?php if (file_exists($thumbRoot)) { ?>
			<img src="<?php echo $thumbImg; ?>" alt="" title="" />
		<br />
		<?php
		}
		?>
		<a href="javascript:;" onClick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=filemanager/preview&amp;file_id=<?php echo $fileData['file_id']."&amp;x=".$size[0]."&amp;y=".$size[1]; ?>','filemanager',<?php echo $size[0]+12; ?>,<?php echo $size[1]+12; ?>)" class="txtDir"><?php echo $fileData['filepath']; ?></a>
		</td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($fileData['filesize']); ?></span></td>
        <td align="center" class="<?php echo $cellColor; ?>">
		<?php
		if (isset($imgIndex) && is_array($imgIndex) && isset($imgIndex[$image])) {
		?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=products/extraImgs&amp;productId=<?php echo $_GET['productId']?>&amp;remove=<?php echo $imgIndex[$image]; ?>&amp;page=<?php echo $_GET['page'];?>" class="txtLink"><?php echo $lang['admin_common']['remove'];?></a>
		<?php
		} else {
		?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=products/extraImgs&amp;productId=<?php echo $_GET['productId']?>&amp;add=<?php echo $fileData['file_id']; ?>&amp;page=<?php echo $_GET['page'];?>" class="txtLink"><?php echo $lang['admin_common']['add'];?></a>
		<?php
		}
		?>
		</td>
      </tr>

	  <?php
	  		}
	  	}
	}

	if(!isset($i)){
	?>

	<tr>
	    <td colspan="3" class="copyText"><?php echo $lang['admin']['products_no_images_avail']; ?>
	    <a href="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/includes/rte/editor/filemanager/browser/default/browser.html?Type=uploads&Connector=<?php echo urlencode($GLOBALS['rootRel'].$glob['adminFolder']); ?>%2Fincludes%2Frte%2Feditor%2Ffilemanager%2Fconnectors%2Fphp%2Fconnector.php" class="txtLink"><?php echo $lang['admin']['products_upload_new_images']; ?>
	    </a>
	    </td>
    </tr>
	<?php
	}
	?>
    </table>
	<p class="copyText"><?php echo $pagination; ?>
	<p align="center"><a href="javascript:window.close();" class="txtLink"><?php echo $lang['admin']['products_close_window'];?></a></p>
	</body>
</html>