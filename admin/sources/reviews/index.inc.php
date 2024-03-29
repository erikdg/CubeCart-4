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
|	Manage Product Reviews/Comments
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$_GET['searchStr'] = filter_var($_GET['searchStr'], FILTER_SANITIZE_STRING);

$lang = getLang('admin'.CC_DS.'admin_reviews.inc.php');

permission('reviews','read', true);

if(isset($_POST['edit']) && (int)$_POST['edit']>0){

	$data['productId'] = $db->mySQLSafe($_POST['productId']);
	$data['name'] = $db->mySQLSafe(htmlentities(stripslashes($_POST['name']),ENT_QUOTES,'UTF-8'));
	$data['email'] = $db->mySQLSafe($_POST['email']);
	$data['title'] = $db->mySQLSafe(htmlentities(stripslashes($_POST['title']),ENT_QUOTES,'UTF-8'));
	$data['review'] = $db->mySQLSafe(htmlentities(stripslashes($_POST['review']),ENT_QUOTES,'UTF-8'));
	if(isset($_POST['rating_val'])){
		$data['rating'] = $db->mySQLSafe($_POST['rating_val']);
	}
	$data['approved'] = $db->mySQLSafe($_POST['approved']);

	$update = $db->update($glob['dbprefix'].'CubeCart_reviews',$data,'`id` = '.(int)$_POST['edit']);

	if($update){
		$msg = "<p class='infoText'>".$lang['admin']['reviews_update_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['reviews_update_fail']."</p>";
	}

}

if($_GET['delete']>0){

	$where = '`id` = '.$db->mySQLSafe($_GET['delete']);
	$delete = $db->delete($glob['dbprefix'].'CubeCart_reviews', $where);

	if($delete){
		$msg = "<p class='infoText'>".sprintf($lang['admin']['reviews_deleted'],$_GET['delete'])."</p>";
	}
}
if (is_numeric($_GET['approved']) && $_GET['id']>0){
	$where = '`id` = '.$db->mySQLSafe($_GET['id']);
	$record['approved'] = $db->mySQLSafe($_GET['approved']);
	$update = $db->update($glob['dbprefix'].'CubeCart_reviews', $record, $where);

	if($_GET['approved']==1){
		$msg = "<p class='infoText'>".sprintf($lang['admin']['reviews_update_published'],$_GET['id'])."</p>";
	} else {
		$msg = "<p class='infoText'>".sprintf($lang['admin']['reviews_update_unpublished'],$_GET['id'])."</p>";
	}
}


if((int)$_GET['edit']>0) {

	$query = 'SELECT '.$glob['dbprefix'].'CubeCart_inventory.name as `prodName`, `id`, `approved`, '.$glob['dbprefix'].'CubeCart_reviews.productId, `type`, `rating`, '.$glob['dbprefix'].'CubeCart_reviews.name, `email`, `title`, `review`, `ip`, `time` FROM '.$glob['dbprefix'].'CubeCart_reviews INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON '.$glob['dbprefix'].'CubeCart_reviews.productId = '.$glob['dbprefix'].'CubeCart_inventory.productId WHERE `id`='.$db->mySQLSafe((int)$_GET['edit']);

} else {

	// get comments / reviews
	if(isset($_GET['column']) && isset($_GET['direction'])) {
		$orderBy = $_GET['column'].' '.$_GET['direction'];
	} else {
		$orderBy = 'time DESC';
	}

	if (isset($_GET['productId']) && is_numeric($_GET['productId'])) {
		$whereArray[] = 'R.productId = '.$_GET['productId'];
	} else if (isset($_GET['searchStr']) && !empty($_GET['searchStr'])) {
		$whereArray[] = "R.review LIKE '%".$_GET['searchStr']."%' OR R.title LIKE '%".$_GET['searchStr']."%'";
	}

	$where = (is_array($whereArray)) ? 'AND '.implode(' AND ', $whereArray) : '';
	$query = "SELECT I.name as `prodName`, I.productId, R.* FROM `".$glob['dbprefix']."CubeCart_reviews` AS R INNER JOIN `".$glob['dbprefix']."CubeCart_inventory` AS I WHERE R.productId = I.productId ".$where." ORDER BY ". $orderBy;
}

if(isset($_GET['page'])){
	$page = $_GET['page'];
} else {
	$page = 0;
}
// query database
$reviewsPerPage = 20;
$results = $db->select($query, $reviewsPerPage, $page);
$numrows = $db->numrows($query);
$pagination = paginate($numrows, $reviewsPerPage, $page, 'page');

if(!$results){
$msg = "<p class='warnText'>".$lang['admin']['reviews_no_reviews']."</p>";
}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if(isset($msg)){
	echo msg($msg);
}
?>
<p class="pageTitle"><?php echo $lang['admin']['reviews_page_title']; ?></p>
<?php
if(permission('reviews','edit') && isset($_GET['edit']) && (int)$_GET['edit']>0 && $results){
?>
<form action="<?php echo $glob['adminFile']; ?>?_g=reviews/index" target="_self" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle" colspan="2"><?php echo $lang['admin']['reviews_edit_below']; ?></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['reviews_review_of']; ?></strong></td>
    <td>
	<select name="productId" class="textbox">
	<?php
	$products = $db->select('SELECT `productId`, `name` FROM '.$glob['dbprefix'].'CubeCart_inventory');

	for($n = 0, $maxn = count($products); $n < $maxn; ++$n){
	?>
	<option value="<?php echo $products[$n]['productId']; ?>" <?php if($results[0]['productId']==$products[$n]['productId']) { echo 'selected="selected"'; } ?>><?php echo $products[$n]['name']; ?></option>
	<?php
	}
	?>
	</select>
	</td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['reviews_author_name'];?></strong></td>
    <td><input type="textbox" name="name" value="<?php echo stripslashes($results[0]['name']); ?>" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['reviews_author_email'];?></strong></td>
    <td><input type="textbox" name="email" value="<?php echo $results[0]['email']; ?>" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['reviews_title'];?></strong></td>
    <td><input type="textbox" name="title" value="<?php echo stripslashes($results[0]['title']); ?>" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText" valign="top"><strong><?php echo $lang['admin']['reviews_review'];?></strong> <br />
    <?php echo $lang['admin']['reviews_no_html'];?></td>
    <td><textarea name="review" cols="40" rows="5"><?php echo stripslashes($results[0]['review']); ?></textarea></td>
  </tr>
  <?php
  if($results[0]['type']==0){
  ?>
  <tr>
    <td class="tdText">
	<span style="float: right;">
	<?php echo "<img src='".$GLOBALS['rootRel']."images/general/px.gif' name='star0' width='15' height='15' id='star0' onclick='stars(0,\"".$glob['adminFolder']."/images/rating/\");' style='cursor: pointer; cursor: hand;' />\n"; ?>
	</span>
	<strong><?php echo $lang['admin']['reviews_rating'];?></strong></td>
    <td>
	<?php

	for($j=0;$j<5;++$j) {

		echo "<img src='".$glob['adminFolder']."/images/rating/".starImg($j,$results[0]['rating']).".gif' name='star".($j+1)."' width='15' height='15' id='star".($j+1)."' onclick='stars(".($j+1).",\"".$glob['adminFolder']."/images/rating/\");' style='cursor: pointer; cursor: hand;' />\n";

	}

	?>
	<input type="hidden" value="<?php echo $results[0]['rating']; ?>" name="rating_val" id="rating_val" />
	</td>
  </tr>
  <?php
  }
  ?>

  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['reviews_status'];?></strong></td>
	<td>
	<select name="approved" class="textbox">
		<option value="0" <?php if($results[0]['approved']==0) { echo 'selected="selected"'; } ?>><?php echo $lang['admin']['reviews_unpublished'];?></option>
		<option value="1" <?php if($results[0]['approved']==1) { echo 'selected="selected"'; } ?>><?php echo $lang['admin']['reviews_published'];?></option>
	</select>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<input type="hidden" name="edit" value="<?php echo $results[0]['id']; ?>" />
	<input name="submit" type="submit" value="<?php echo $lang['admin']['reviews_btn_update'];?>" class="submit" />
	</td>
  </tr>
</table>
</form>
<?php
} else {
?>

<form action="<?php echo $glob['adminFile']; ?>" target="_self" method="get" class="tdText"><?php echo $lang['admin']['reviews_order_by']; ?>
<input type="hidden" name="_g" value="reviews/index" />
<select name="column" class="textbox">
	<option value="name" <?php if($_GET['column']=='name') echo 'selected="selected"' ?>><?php echo $lang['admin']['reviews_filter_name']; ?></option>
	<option value="email" <?php if($_GET['column']=='email') echo 'selected="selected"' ?>><?php echo $lang['admin']['reviews_filter_email']; ?></option>
	<option value="title" <?php if($_GET['column']=='title') echo 'selected="selected"' ?>><?php echo $lang['admin']['reviews_filter_title']; ?></option>
	<option value="time" <?php if($_GET['column']=='time') echo 'selected="selected"' ?>><?php echo $lang['admin']['reviews_filter_date']; ?></option>
	<option value="prodName" <?php if($_GET['column']=='prodName') echo 'selected="selected"' ?>><?php echo $lang['admin']['reviews_filter_prod_name']; ?></option>
	<option value="rating" <?php if($_GET['column']=='rating') echo 'selected="selected"' ?>><?php echo $lang['admin']['reviews_filter_rating']; ?></option>
</select>
<select name="direction" class="textbox">
	<option value="ASC" <?php if($_GET['direction']=='ASC') { echo "selected = 'selected'"; } ?>><?php echo $lang['admin']['reviews_filder_asc']; ?></option>
	<option value="DESC" <?php if($_GET['direction']=='DESC') { echo "selected = 'selected'"; } ?>><?php echo $lang['admin']['reviews_filder_desc']; ?></option>
</select>
<?php echo $lang['admin']['reviews_cont_text']; ?> <input name="searchStr" type="text" class="textbox" value="<?php echo sanitizeVar($_GET['searchStr']); ?>" />
<input name="submit" type="submit" value="<?php echo $lang['admin']['reviews_filter_go']; ?>" class="submit" />

<input name="Button" type="button" onclick="goToURL('parent','<?php echo $glob['adminFile']; ?>?_g=reviews/index');return document.returnValue" value="<?php echo $lang['admin']['reviews_filter_reset']; ?>" class="submit" />
</form>

<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>

  <?php
  if($results){

  	for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
	$cellColor = '';
	$cellColor = cellColor($i);
  ?>
	<div class="<?php echo $cellColor;?> tdText" style="border: 1px black solid; margin-bottom: 10px;">
	<p style="padding: 3px; margin: 0px;">
	<span style="float: right;">
	<?php
	if($results[$i]['type']==0) {
		for($j=0;$j<5;++$j) {

			echo "<img src='".$glob['adminFolder']."/images/rating/".starImg($j,$results[$i]['rating']).".gif' alt='' />\n";
		}
	}
	?></span>
	<strong><?php echo $lang['admin']['reviews_name_2']; ?></strong> <?php echo $results[$i]['name']; ?> | <strong><?php echo $lang['admin']['reviews_email_2']; ?></strong> <a href="mailto:<?php echo $results[$i]['email']; ?>" class="txtLink"><?php echo $results[$i]['email']; ?></a>
	|  <strong><?php echo $lang['admin']['reviews_ip']; ?></strong> <a href="javascript:;" class="txtLink" onclick="openPopUp('<?php echo $glob['adminFile']; ?>?_g=misc/lookupip&amp;ip=<?php echo $results[$i]['ip']; ?>','misc',300,130,'yes,resizable=yes')"><?php echo $results[$i]['ip']; ?></a> </p>
    <p style="padding: 3px; margin: 0px;">
<span style="font-weight:bold; text-transform:uppercase;"><?php echo $results[$i]['title']; ?></span>
</p>
<p style="padding: 3px; margin: 0px;">&quot;<?php echo $results[$i]['review']; ?>&quot;</p>
<p style="border-top: 1px black solid; padding: 2px; margin: 0px; font-size:10px">
<span style="float: right;">
	<?php
	$currentPage = currentPage();

	if($results[$i]['approved']==1){
	?>
	<a <?php if(permission('reviews','edit')) { ?>href="<?php echo $currentPage; ?>&amp;approved=0&amp;id=<?php echo $results[$i]['id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['sure_q'])); ?>')"<?php } else { echo $link401; } ?> class="txtRed"><?php echo $lang['admin']['reviews_unpublish'];?></a>
	<?php
	} else {
	?>
	<a <?php if(permission('reviews','edit')) { ?>href="<?php echo $currentPage; ?>&amp;approved=1&amp;id=<?php echo $results[$i]['id']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['sure_q'])); ?>')"<?php } else { echo $link401; } ?> class="txtGreen"><?php echo $lang['admin']['reviews_publish'];?></a>
	<?php
	}
	?>
	/
	<a <?php if(permission('reviews','edit')) { ?>href="<?php echo $glob['adminFile']; ?>?_g=reviews/index&amp;edit=<?php echo $results[$i]['id'];?>"<?php } else { echo $link401; } ?>  class="txtLink"><?php echo $lang['admin_common']['edit'];?></a>
	/
	<a <?php if(permission('reviews',"delete")) { ?>href="<?php echo $glob['adminFile']; ?>?_g=reviews/index&amp;delete=<?php echo $results[$i]['id'];?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q'])); ?>')"<?php } else { echo $link401; } ?> class="txtLink"><?php echo $lang['admin_common']['delete'];?></a>
	</span>

<strong><?php echo $lang['admin']['reviews_date_2']; ?></strong> <?php echo formatTime($results[$i]['time']); ?>
   | <strong><?php echo $lang['admin']['reviews_type']; ?></strong> <?php if($results[$i]['type']==1) { echo $lang['admin']['reviews_type_comment']; } else { echo $lang['admin']['reviews_type_review']; } ?>
   | <strong><?php echo $lang['admin']['reviews_product'];?></strong> <a href="<?php echo $glob['adminFile']; ?>?_g=products/index&amp;edit=<?php echo $results[$i]['productId']; ?>" class="txtLink"><?php echo $results[$i]['prodName']; ?></a></p>
</div>
  <?php
  	}
  }
  ?>
<p class="copyText" style="text-align: right;"><?php echo $pagination; ?></p>
<?php
}
?>