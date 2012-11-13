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
|	options.inc.php
|   ========================================
|	Product Options
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_products.inc.php');

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');

permission('products','read',true);

////////////////////////////////////////
// if master and slave productId is set
/////////
#:convict:# Mass Product Options Assignation >>
if(isset($_POST['masterProduct']) && $_POST['masterProduct']>0 && count($_POST['slaveProductMass'])>0) {

	$masterArray = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_bot WHERE `product` = '.$db->mySQLSafe($_POST['masterProduct']).' ORDER BY assign_id');
	for ($j = 0, $maxj = count($_POST['slaveProductMass']); $j < $maxj; ++$j) {

		for ($i = 0, $maxi = count($masterArray); $i < $maxi; ++$i){

			$data['product'] = $db->mySQLSafe($_POST['slaveProductMass'][$j]);
			$data['option_id'] = $db->mySQLSafe($masterArray[$i]['option_id']);
			$data['value_id'] = $db->mySQLSafe($masterArray[$i]['value_id']);
			$data['option_price'] = $db->mySQLSafe($masterArray[$i]['option_price']);
			$data['option_symbol'] = $db->mySQLSafe($masterArray[$i]['option_symbol']);

			$insertSlaveMast = $db->insert($glob['dbprefix'].'CubeCart_options_bot', $data);

			if (!$insertSlaveMast) $insertError = true;

			unset($data);

		}
	}

	if(!isset($insertError) && $insertSlaveMast) {
		$msg = "<p class='infoText'>".$lang['admin']['products_opt_mast_slave_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products_opt_mast_slave_fail']."</p>";
	}
}
#:convict:# Mass Product Options Assignation <<
////////////////////////////////////////
// if delete get variable is set
/////////

$delete = '';
$where = '';

if(isset($_GET['delOption']) && $_GET['delOption']>0){

		$where = '`option_id`  = '.$db->mySQLSafe($_GET['delOption']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_options_bot', $where);

		$where = '`father_id`  = '.$db->mySQLSafe($_GET['delOption']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_options_mid', $where);

		$where = '`option_id` = '.$db->mySQLSafe($_GET['delOption']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_options_top', $where);

		if($delete){
			$msg = "<p class='infoText'>".$lang['admin']['products_opt_opt_deleted']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_opt_opt_not_deleted']."</p>";
		}

		//httpredir(urldecode($_GET['redir']));
} elseif(isset($_GET['delAttribute']) && isset($_GET['optId']) && $_GET['delAttribute']>0 && $_GET['optId']>0) {

		$where = '`value_id` ='.$db->mySQLSafe($_GET['delAttribute']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_options_bot', $where);

		$where = '`value_id` ='.$db->mySQLSafe($_GET['delAttribute']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_options_mid', $where);

		if($delete){
			$msg = "<p class='infoText'>".$lang['admin']['products_opt_att_deleted']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_opt_att_not_deleted']."</p>";
		}

		//httpredir(urldecode($_GET['redir']));

} elseif(isset($_GET['delAssigned']) && $_GET['delAssigned']>0) {

		$where = '`assign_id` = '.$db->mySQLSafe($_GET['delAssigned']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_options_bot', $where);

		if($delete){
			$msg = "<p class='infoText'>".$lang['admin']['products_opt_assigned_deleted']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_opt_assigned_not_deleted']."</p>";
		}

		//httpredir(urldecode($_GET['redir']));

}
////////////////////////////////////////
// if add post variable is set
/////////
if(isset($_POST['add']) && $_POST['add']=='option') {

	$record['option_name'] = $db->mySQLSafe($_POST['option']);
	$record['option_type'] = $db->mySQLSafe($_POST['option_type']);

	$insert = $db->insert($glob['dbprefix'].'CubeCart_options_top', $record);

		if($insert){
			$msg = "<p class='infoText'>".$lang['admin']['products_opt_add_success']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_opt_add_fail']."</p>";
		}

} elseif(isset($_POST['add']) && $_POST['add']=='attribute') {

	$record['value_name'] = $db->mySQLSafe($_POST['attribute']);
	$record['father_id'] = $db->mySQLSafe($_POST['option']);

	$insert = $db->insert($glob['dbprefix'].'CubeCart_options_mid', $record);

		if($insert){
			$msg = "<p class='infoText'>".$lang['admin']['products_att_add_success']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_att_add_fail']."</p>";
		}

} elseif(isset($_POST['add']) && $_POST['add']=='assign'){

	$record['product'] = $db->mySQLSafe($_POST['productId']);
	$record['option_id'] = $db->mySQLSafe($_POST['option']);
	$record['value_id'] = $db->mySQLSafe($_POST['attribute']);
	$record['option_price'] = $db->mySQLSafe($_POST['price']);
	$record['option_symbol'] = $db->mySQLSafe($_POST['sign']);

	$insert = $db->insert($glob['dbprefix'].'CubeCart_options_bot', $record);

		if($insert){
			$msg = "<p class='infoText'>".$lang['admin']['products_assign_success']."</p>";
		} else {
			$msg = "<p class='warnText'>".$lang['admin']['products_assign_fail']."</p>";
		}

}
////////////////////////////////////////
// if edit post variable is set
/////////
if(isset($_POST['edit']) && $_POST['edit']=='option') {

	$record['option_name'] = $db->mySQLSafe($_POST['option']);
	$record['option_type'] = $db->mySQLSafe($_POST['option_type']);
	$where = '`option_id` = '.$db->mySQLSafe($_POST['option_id']);

	$update = $db->update($glob['dbprefix'].'CubeCart_options_top', $record, $where);

	if($update){
		$msg = "<p class='infoText'>".$lang['admin']['products_option_edit_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products_option_edit_fail']."</p>";
	}

	//httpredir(urldecode($_POST['redir']));

} elseif(isset($_POST['edit']) && $_POST['edit']=='attribute') {

	$record['value_name'] = $db->mySQLSafe($_POST['attribute']);
	$record['father_id'] = $db->mySQLSafe($_POST['option']);

	$where = '`value_id` = '.$db->mySQLSafe($_POST['value_id']);

	$update = $db->update($glob['dbprefix'].'CubeCart_options_mid', $record, $where);

	if($update){
		$msg = "<p class='infoText'>".$lang['admin']['products_attribute_edit_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products_attribute_edit_fail']."</p>";
	}

	//httpredir(urldecode($_POST['redir']));

} elseif(isset($_POST['edit']) && $_POST['edit']=='assigned'){

	$record['product'] = $db->mySQLSafe($_POST['productId']);
	$record['option_id'] = $db->mySQLSafe($_POST['option']);
	$record['value_id'] = $db->mySQLSafe($_POST['attribute']);
	$record['option_price'] = $db->mySQLSafe($_POST['price']);
	$record['option_symbol'] = $db->mySQLSafe($_POST['sign']);

	$where = '`assign_id` = '.$db->mySQLSafe($_POST['assign_id']);

	$update = $db->update($glob['dbprefix'].'CubeCart_options_bot', $record, $where);

	if($update){
		$msg = "<p class='infoText'>".$lang['admin']['products_assign_edit_success']."</p>";
	} else {
		$msg = "<p class='warnText'>".$lang['admin']['products_assign_edit_fail']."</p>";
	}

	//httpredir(urldecode($_POST['redir']));

}

////////////////////////////////////////
// get recordsets for all required data
/////////

	$optionsPerPage = 15;
	$attributesPerPage = 15;
	$existingOptionsPerPage = 20;

	// products
	$query = 'SELECT productId,  '.$glob['dbprefix'].'CubeCart_inventory.cat_id, `name`, `cat_name` FROM '.$glob['dbprefix'].'CubeCart_inventory INNER JOIN  '.$glob['dbprefix'].'CubeCart_category ON  '.$glob['dbprefix'].'CubeCart_inventory.cat_id =  '.$glob['dbprefix'].'CubeCart_category.cat_id ORDER BY `cat_name` ASC';
	$products = $db->select($query);

	// options
	if(isset($_GET['editOption']) && $_GET['editOption']>0) {
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_top WHERE `option_id` = '.$db->mySQLSafe($_GET['editOption']).' ORDER BY `option_name` ASC';
	} else {
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_top ORDER BY `option_name` ASC';
	}

	if(isset($_GET['optPage'])){
		$optPage = $_GET['optPage'];
	} else {
		$optPage = 0;
	}

	$options = $db->select($query, $optionsPerPage, $optPage);
	$fullOptions = $db->select($query);
    for ($i = 0, $maxi = count($fullOptions); $i < $maxi; ++$i){
        $idKey = $fullOptions[$i]['option_id'];
        $optionNames[$idKey] = $fullOptions[$i]['option_name'];
    }
	$optionsPaginate = paginate($db->numrows($query), $optionsPerPage, $optPage, 'optPage');

	// attributes
	if(isset($_GET['editAttribute']) && $_GET['editAttribute']>0){
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_options_mid AS M INNER JOIN  ".$glob['dbprefix']."CubeCart_options_top AS T ON M.father_id = T.option_id WHERE M.value_id = ".$db->mySQLSafe($_GET['editAttribute'])." AND T.option_type = '0' ORDER BY option_name, value_name ASC";
	} else {
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_options_mid AS M INNER JOIN  ".$glob['dbprefix']."CubeCart_options_top AS T ON M.father_id = T.option_id WHERE T.option_type = '0' ORDER BY T.option_name, M.value_name ASC";
	}


	if(isset($_GET['attPage'])) {
		$attPage = $_GET['attPage'];
	} else {
		$attPage = 0;
	}

	$attributes = $db->select($query, $attributesPerPage, $attPage);
	$fullAttributes = $db->select($query);

	for ($i = 0, $maxi = count($fullAttributes); $i < $maxi; ++$i){
		$idKey = $fullAttributes[$i]['value_id'];
		$optionValues[$idKey] = $fullAttributes[$i]['value_name'];
	}

	$attributesPaginate = paginate($db->numrows($query), $attributesPerPage, $attPage, 'attPage');

	if($_GET['prodIdFilter']>0){
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_bot INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON `product` = `productId` WHERE `productId` = '.$db->mySQLSafe($_GET['prodIdFilter']).' ORDER BY `name`, `value_id` ASC';
	} elseif(isset($_GET['editAssigned']) && $_GET['editAssigned']>0) {
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_bot INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON `product` = `productId` WHERE `assign_id` = '.$db->mySQLSafe($_GET['editAssigned']).' ORDER BY `name`, `value_id` ASC';
	} else{
		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_bot INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON `product` = `productId` ORDER BY `name`, `option_id`, `value_id` ASC';
	}

	if(isset($_GET['exiPage'])){
		$exiPage = $_GET['exiPage'];
	} else {
		$exiPage = 0;
	}

	$existingOptions = $db->select($query, $existingOptionsPerPage, $exiPage);
	$existingOptionsPaginate = paginate($db->numrows($query), $existingOptionsPerPage, $exiPage, 'exiPage');

	$excluded = array(
		'editOption' => 1,
		'delOption' => 1,
		'editAttribute' => 1,
		'delAttribute' => 1,
		'editAssigned' => 1,
		'delAssigned' => 1,
	);
	$currentPage = urlencode(currentPage($excluded));

	require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
?>

<p class="pageTitle"><?php echo $lang['admin']['products_product_options'];?></p>
<?php
if(isset($msg)){
	echo msg($msg);
}
 if($products){ ?>

  <?php
  // get master products
	$masterProducts = $db->select('SELECT DISTINCT `product`, `name` FROM '.$glob['dbprefix'].'CubeCart_options_bot INNER JOIN '.$glob['dbprefix'].'CubeCart_inventory ON `product` = `productId` ORDER BY `name` ASC');

  //get array of product ID's to miss out of slaves
  for ($i = 0, $maxi = count($masterProducts); $i < $maxi; ++$i){
  	$key = $masterProducts[$i]['product'];
	$masterKey[$key] = $masterProducts[$i]['product'];
  }

  // get slave products
	$slaveProducts = $db->select('SELECT DISTINCT `productId`, `name` FROM '.$glob['dbprefix'].'CubeCart_options_bot RIGHT JOIN '.$glob['dbprefix'].'CubeCart_inventory ON `product` = `productId` ORDER BY `name` ASC');

  #:convict:# Mass Product Options Assignation >>
  $slaveCounts = 0;
  for ($i = 0, $maxi = count($slaveProducts); $i < $maxi; ++$i){
	$key = $slaveProducts[$i]['productId'];
	if(!isset($masterKey[$key])) ++$slaveCounts;
  }
  $select_size = $slaveCounts>10 ? 10 : ($slaveCounts==1 ? $slaveCounts+1 : $slaveCounts);
  if($masterProducts && $slaveProducts && $slaveCounts) {
  ?>

<form name="quickMassAssign" method="post" action="<?php echo urldecode($currentPage); ?>">
	<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td class="tdTitle" colspan="4"><strong><?php echo $lang['admin']['products_quick_assign'];?></strong></td>
  </tr>
  <tr>
    <td class="copyText"><?php echo $lang['admin']['products_prod_opts_of'];?></td>
    <td>
		<select name="masterProduct">
		<?php for ($i = 0, $maxi = count($masterProducts); $i < $maxi; ++$i){ ?>
		<option value="<?php echo $masterProducts[$i]['product']; ?>"><?php echo $masterProducts[$i]['name']; ?></option>
		<?php } ?>
		</select>
	</td>
    <td class="copyText"><?php echo $lang['admin']['products_to'];?></td>
    <td>
		<select multiple size="<?php echo $select_size;?>" name="slaveProductMass[]">
		<?php
		$noOpts = 0;
		for ($i = 0, $maxi = count($slaveProducts); $i < $maxi; ++$i){
			$key = $slaveProducts[$i]['productId'];
			if(!isset($masterKey[$key])){

			++$noOpts; ?>
		<option value="<?php echo $slaveProducts[$i]['productId']; ?>"><?php echo $slaveProducts[$i]['name']; ?></option>
		<?php
			}
		}
		if ($noOpts == 0){ ?>
		<option value="0"><?php echo $lang['admin_common']['na']; ?></option>
		<?php } ?>
		</select>
	</td>
  </tr>
  <tr>
    <td colspan="4" align="center"><input name="submit" type="submit" value="<?php echo $lang['admin']['products_go'];?>" class="submit" /></td>
  </tr>
</table>
</form>

  <?php } ?>
<!-- #:convict:# Mass Product Options Assignation << -->
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="25"><strong><?php echo $lang['admin']['products_opt_step1'];?></strong></td>
        <td height="25" align="right"><?php echo $optionsPaginate; ?></td>
      </tr>
    </table>
      <form name="form1" method="post" action="<?php echo urldecode($currentPage); ?>">
	 <table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
      <tr align="center">
        <td class="tdTitle" width="40%"><?php echo $lang['admin']['products_opt_name'];?></td>
		<td class="tdTitle"><?php echo $lang['admin']['products_type'];?></td>
        <td class="tdTitle" colspan="2" width="100"><?php echo $lang['admin']['products_action'];?></td>
      </tr>
      <?php if($options) {

	  $defaultOptions = false;

	  for ($i = 0, $maxi = count($options); $i < $maxi; ++$i){
	   $cellColor = '';
	   $cellColor = cellColor($i);



	  $idKey = $options[$i]['option_id'];
	  //$optionNames[$idKey] = $options[$i]['option_name'];
	   if(!isset($_GET['editOption'])) { ?>
	  <tr>
        <td class="<?php echo $cellColor; ?>"><?php echo stripslashes($options[$i]['option_name']); ?></td>

		<td class="<?php echo $cellColor; ?>">
		<?php
		switch ($options[$i]['option_type']) {
			case '2':
				echo 'Textarea';
				break;
			case '1':
				echo 'Textbox';
				break;
			default:
			 	$defaultOptions = true;
				echo 'Default';
		}
		?></td>

        <td class="<?php echo $cellColor; ?>" align="center" width="50">
		<?php if(permission('products','edit')){ ?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=products/options&amp;editOption=<?php echo $options[$i]['option_id'];?>&amp;redir=<?php echo $currentPage; ?>" class="txtLink">
		<?php } else { echo '<a '.$link401.'>'; } ?>
		<?php echo $lang['admin_common']['edit'];?></a>
		</td>
		<td class="<?php echo $cellColor; ?>" align="center" width="50">
		<?php if(permission('products','delete')){ ?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=products/options&amp;delOption=<?php echo $options[$i]['option_id']; ?>&amp;redir=<?php echo $currentPage; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', $lang['admin']['products_warn_remove_opt']);?>');" class="txtLink">
		<?php } else { echo "<a ".$link401.">"; } ?>
		<?php echo $lang['admin_common']['delete'];?></a>
		</td>
      </tr>
	  <?php } // end if get variable not set
	  }
	  } else { ?>
	  <tr align="left">
        <td colspan="3"><?php echo $lang['admin']['products_no_options_made'];?></td>
      </tr>
	  <?php } ?>
	  <tr align="center">
        <td><input name="option" type="text" id="option" <?php if(isset($_GET['editOption'])){ ?>value="<?php echo htmlspecialchars($options[0]['option_name']); ?>"<?php } ?> class="textbox" /></td>
		<td>
		  <select name="option_type" id="option_type" class="textbox">
		    <?php
			$types = array('0' => 'Default', '1' => 'Textbox', '2' => 'Textarea');
			foreach ($types as $key => $type) {
				$current	= (is_numeric($options[0]['option_type'])) ? $options[0]['option_type'] : 0;
				$selected	= (!empty($_GET['editOption']) && $options[0]['option_type'] == $key) ? ' selected="selected"' : '';
				echo sprintf('<option value="%d"%s>%s</option>', $key, $selected, $type);
			}
			?>
		  </select>
        </td>
        <td colspan="2"><input name="Submit" type="submit" class="submit" value="<?php if(isset($_GET['editOption'])){ echo $lang['admin_common']['edit']; } else {  echo $lang['admin_common']['add']; } echo " ".$lang['admin']['products_option'];?>" /></td>
      </tr>
    </table>

	<?php if(isset($_GET['editOption'])){ ?>
	<input type="hidden" name="option_id" value="<?php echo $_GET['editOption']; ?>" />
	<input type="hidden" name="edit" value="option" />
	<input type="hidden" name="redir" value="<?php echo $_GET['redir']; ?>" />
	<?php } else { ?>
	<input type="hidden" name="add" value="option" />
	<?php } ?>
	</form>
	<?php if($options && $defaultOptions) { ?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="25"><strong><?php echo $lang['admin']['products_opt_step2'];?></strong></td>
        <td height="25" align="right"><?php echo $attributesPaginate; ?></td>
      </tr>
    </table>
	<form name="form2" method="post" action="<?php echo urldecode($currentPage); ?>">
	<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
      <tr align="center">
		<td class="tdTitle" width="40%"><?php echo $lang['admin']['products_opt_name'];?></td>
		<td class="tdTitle"><?php echo $lang['admin']['products_option_attribute'];?></td>
		<td class="tdTitle" colspan="2" width="100"><?php echo $lang['admin']['products_action'];?></td>
      </tr>
      <?php
	  if($attributes) {

	  for ($i = 0, $maxi = count($attributes); $i < $maxi; ++$i){
		  $cellColor = '';
		  $cellColor = cellColor($i);

		  $idKey = $attributes[$i]['value_id'];
	  	//$optionValues[$idKey] = $attributes[$i]['value_name'];
	   if(!isset($_GET['editAttribute'])){
	   ?>
	  <tr>
        <td class="<?php echo $cellColor; ?>"><?php echo $attributes[$i]['option_name']; ?></td>
		<td class="<?php echo $cellColor; ?>"><?php echo stripslashes($attributes[$i]['value_name']); ?></td>
        <td class="<?php echo $cellColor; ?>" align="center" width="50">
		<a <?php if(permission('products','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/options&editAttribute=<?php echo $attributes[$i]['value_id']; ?>&redir=<?php echo $currentPage; ?>"<?php } else { echo $link401; } ?> class="txtLink"><?php echo $lang['admin_common']['edit'];?></a> </td>
		<td class="<?php echo $cellColor; ?>" align="center" width="50">
		<?php if(permission('products','delete')){ ?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=products/options&amp;delAttribute=<?php echo $attributes[$i]['value_id']; ?>&amp;optId=<?php echo $attributes[$i]['father_id']; ?>&amp;redir=<?php echo $currentPage; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', $lang['admin']['products_warn_remove_att']); ?>');" class="txtLink">
		<?php } else { echo '<a '.$link401.'>'; } ?>
		<?php echo $lang['admin_common']['delete'];?></a> </td>
      </tr>
	  <?php } // end if not set edit attribute
	  }
	  } else { ?>
	  <tr align="left">
        <td colspan="5"><?php echo $lang['admin']['products_no_attributes_made'];?></td>
      </tr>
	  <?php } ?>
      <tr align="center">
        <td>
		  <select name="option" id="option" class="textbox">
		  <?php
		  	for ($i = 0, $maxi = count($options); $i < $maxi; ++$i){
		  		if ($options[$i]['option_type'] == '0') { ?>
		  <option value="<?php echo $options[$i]['option_id'];?>" <?php if($options[$i]['option_id']==$attributes[0]['father_id']){ echo 'selected="selected"';}?>><?php echo $options[$i]['option_name'];?></option>
		  <?php } } ?>
          </select>
        </td>
        <td><input name="attribute" type="text" id="attribute" class="textbox" value="<?php if(isset($attributes[0]['value_name']) && isset($_GET['editAttribute'])) echo htmlspecialchars($attributes[0]['value_name']);?>" /></td>
        <td colspan="2"><input name="Submit" type="submit" class="submit" value="<?php if(isset($_GET['editAttribute'])){ echo $lang['admin_common']['edit']; } else {  echo $lang['admin_common']['add']; } echo " ".$lang['admin']['products_attribute'];?>" /></td>
      </tr>
    </table>
	<?php if(isset($_GET['editAttribute'])){ ?>
	<input type="hidden" name="edit" value="attribute" />
	<input type="hidden" name="value_id" value="<?php echo $attributes[0]['value_id']; ?>" />
	<input type="hidden" name="redir" value="<?php echo $_GET['redir']; ?>" />
	<?php } else { ?>
	<input type="hidden" name="add" value="attribute" />
	<?php } ?>
	</form>
	<?php } ?>
	<?php
	if($attributes){
	?>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="25"><strong><?php echo $lang['admin']['products_opt_step3'];?></strong>
		  </td>
          <td height="25" align="right"><?php echo $existingOptionsPaginate; ?></td>
        </tr>
      </table>
	  <p><?php echo $lang['admin']['products_filter_by_prod']; ?>
	<select name="prodIdFilter" id="prodIdFilter" class="textbox" onchange="jumpMenu('parent',this,0)">

   <option value="<?php echo str_replace("&amp;prodIdFilter=".$_GET['prodIdFilter'], "", urldecode($currentPage)); ?>"><?php echo $lang['admin_common']['na']; ?></option>
   <?php
   for ($i = 0, $maxi = count($products); $i < $maxi; ++$i){
   	if($products[$i]['cat_id']!=$products[$i-1]['cat_id']){
	?>
	<optgroup label="<?php echo $products[$i]['cat_name']; ?>"></optgroup>
	<?php
	}

	$jumpPage = str_replace("&amp;prodIdFilter=".$_GET['prodIdFilter'],"",urldecode($currentPage))."&amp;prodIdFilter=".$products[$i]['productId'];
	?>

	<option value="<?php echo $jumpPage ?>" <?php if($products[$i]['productId']==$_GET['prodIdFilter']) echo 'selected="selected"'; ?>><?php echo $products[$i]['name']; ?></option>
	<?php
	}
	?>
	</select></p>
	<form name="form3" method="post" action="<?php echo urldecode($currentPage); ?>">
	<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
      <tr align="center">
        <td class="tdTitle" nowrap='nowrap'><?php echo $lang['admin']['products_product'];?></td>
        <td class="tdTitle" nowrap='nowrap'><?php echo $lang['admin']['products_opt_name'];?></td>
        <td class="tdTitle" nowrap='nowrap'><?php echo $lang['admin']['products_option_attribute'];?></td>
        <td class="tdTitle" nowrap='nowrap'><?php echo $lang['admin']['products_option_price'];?></td>
        <td class="tdTitle" nowrap='nowrap'><?php echo $lang['admin']['products_add_subtract'];?></td>
        <td class="tdTitle" colspan="2" nowrap='nowrap' width="100"><?php echo $lang['admin']['products_action'];?></td>
      </tr>
	   <?php
	   if($existingOptions){
		   for ($i = 0, $maxi = count($existingOptions); $i < $maxi; ++$i){
			   $cellColor = '';
			   $cellColor = cellColor($i);

			   if(!isset($_GET['editAssigned'])){
			   ?>
			  <tr>
				<td class="<?php echo $cellColor; ?>"><?php echo stripslashes($existingOptions[$i]['name']); ?></td>
				<td class="<?php echo $cellColor; ?>">
					<?php
					$idKey = $existingOptions[$i]['option_id'];
					echo stripslashes($optionNames[$idKey]);
					?>
				</td>
				<td class="<?php echo $cellColor; ?>">
					<?php
					$idKey = $existingOptions[$i]['value_id'];
					echo  stripslashes($optionValues[$idKey]);
					?>
				</td>
				<td class="<?php echo $cellColor; ?>">
				<?php
				if($existingOptions[$i]['option_price']>0){
					echo priceFormat($existingOptions[$i]['option_price'],true);
				} else {
					echo $lang['admin_common']['na'];
				}
				?></td>
				<td class="<?php echo $cellColor; ?>" align="center">
				<?php
				if($existingOptions[$i]['option_symbol']=='~') {
					echo $lang['admin_common']['na'];
				} else {
					echo $existingOptions[$i]['option_symbol'];
				}
				?>
				</td>
				<td class="<?php echo $cellColor; ?>" align="center" width="50"><a <?php if(permission('products','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/options&amp;editAssigned=<?php echo $existingOptions[$i]['assign_id']; ?>&amp;redir=<?php echo $currentPage; ?>"<?php } else { echo $link401; } ?> class="txtLink"><?php echo $lang['admin_common']['edit'];?></a> </td>
				<td class="<?php echo $cellColor; ?>" align="center" width="50">
				<?php if(permission('products','delete')){ ?>
				<a href="<?php echo $glob['adminFile']; ?>?_g=products/options&amp;delAssigned=<?php echo $existingOptions[$i]['assign_id']; ?>&amp;redir=<?php echo $currentPage; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', $lang['admin']['products_remove_opt_prod']); ?>');" class="txtLink">
				<?php } else { echo '<a '.$link401.'>'; } ?>
				<?php echo $lang['admin_common']['delete'];?></a></td>
			  </tr>
			  <?php
			  } //end if not set edit assigned
		} // end loop

	  } else {
	  ?>
	  <tr>
	  	<td colspan="8"><?php echo $lang['admin']['products_no_assigned_opts'];?></td>
	</tr>
	  <?php
	  }
	  ?>
      <tr align="center">
        <td>
		<select name="productId" id="productId" class="textbox">
        <?php for ($i = 0, $maxi = count($products); $i < $maxi; ++$i){  if($products[$i]['cat_id']!=$products[$i-1]['cat_id']){ ?>
		<optgroup label="<?php echo $products[$i]['cat_name']; ?>"></optgroup>
		<?php } ?>

		<option value="<?php echo $products[$i]['productId']; ?>" <?php if($products[$i]['productId']==$existingOptions[0]['product']) echo 'selected="selected"'; ?>><?php echo stripslashes($products[$i]['name']); ?></option>
		<?php } ?>

		</select>
        </td>
        <td>
		<select name="option" id="option" class="textbox">
        <?php
		for ($i = 0, $maxi = count($fullOptions); $i < $maxi; ++$i) {
        	if($fullOptions[$i]['option_type']==0) {
        ?>
          <option value="<?php echo $fullOptions[$i]['option_id'];?>" <?php if($fullOptions[$i]['option_id']==$existingOptions[0]['option_id']) echo 'selected="selected"'; ?>><?php echo stripslashes($fullOptions[$i]['option_name']);?></option>
		 <?php
		 	}
		 }
		 ?>
		</select>
		</td>
        <td>
			<select name="attribute" id="attribute" class="textbox">
        	<?php for ($i = 0, $maxi = count($fullAttributes); $i < $maxi; ++$i){ ?>
		  <option value="<?php echo $fullAttributes[$i]['value_id'];?>" <?php if($fullAttributes[$i]['value_id']==$existingOptions[0]['value_id']) echo 'selected="selected"'; ?>><?php echo stripslashes($fullAttributes[$i]['value_name']);?></option>
		  <?php } ?>
			</select>
		</td>
        <td><input name="price" type="text" class="textbox" id="price" size="7" <?php if(isset($_GET['editAssigned'])){ ?>value="<?php echo $existingOptions[0]['option_price'];?>"<?php } ?> /></td>
        <td><select name="sign" id="sign" class="textbox">
          <option value="+" <?php if($existingOptions[0]['option_symbol']=='+') echo 'selected="selected"'; ?>>+</option>
          <option value="~" <?php if($existingOptions[0]['option_symbol']=='~') echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['na']; ?></option>
		  <option value="-" <?php if($existingOptions[0]['option_symbol']=='-') echo 'selected="selected"'; ?>>-</option>
                </select></td>
        <td colspan="2"><input name="Submit" type="submit" class="submit" value="<?php if(isset($_GET['editAssigned'])){  echo $lang['admin_common']['edit']; } else {  echo $lang['admin_common']['add']; }   echo " ".$lang['admin']['products_product_option'];?>" /></td>
      </tr>
    </table>
	<?php
	if(isset($_GET['editAssigned'])){
	?>
	<input type="hidden" name="edit" value="assigned" />
	<input type="hidden" name="assign_id" value="<?php echo $_GET['editAssigned']; ?>" />
	<input type="hidden" name="redir" value="<?php echo $_GET['redir']; ?>" />
	<?php
	} else {
	?>
	<input type="hidden" name="add" value="assign" />
	<?php
	}
	?>
	</form>
	<?php
	}

	?>
<?php
} else {
?>
<p class="copyText"><?php echo $lang['admin']['products_prods_made_1st'];?></p>
<?php
}
?>
