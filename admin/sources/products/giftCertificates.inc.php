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
|	giftCertificates.inc.php
|   ========================================
|	Gift Certificate Settings
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_products.inc.php');

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'currencyVars.inc.php');
require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

if(isset($_POST['gc'])){
	
	if(!is_numeric($_POST['gc']['min'])) {
		$_POST['gc']['min'] = '';
	}
	
	if(!is_numeric($_POST['gc']['max'])) {
		$_POST['gc']['max'] = '';
	}
	
	if(!is_numeric($_POST['gc']['weight'])) {
		$_POST['gc']['weight'] = '';
	} 
	
	if(!preg_match('/^([a-z0-9_-]+)$/i',$_POST['gc']['productCode'])) {
		$_POST['gc']['productCode'] = '';
	} 
	
	$cache = new cache();
	$cache->clearCache();
	$gc = fetchDbConfig('gift_certs');
	$msg = writeDbConf($_POST['gc'], 'gift_certs', $_POST['gc']);
}
$gc = fetchDbConfig('gift_certs');

?>
<p class='pageTitle'><?php echo $lang['admin']['gc_title'] ;?></p>
<?php
if(isset($msg)){
	echo msg($msg);
}
?>
<form id="gc" name="gc" method="post" action="<?php echo $glob['adminFile']; ?>?_g=products/giftCertificates">

	<table  border="0" cellspacing="1" cellpadding="3" class="mainTable">
	  <tr>
		<td colspan="2" class="tdTitle"><?php echo $lang['admin']['gc_title'] ;?></td>
	  </tr>
	  <tr>
		<td class="tdText"><strong><?php echo $lang['admin']['gc_title'] ;?></strong></td>
		<td class="tdText">
		<select name="gc[status]">
			<option value="0" <?php if($gc['status']==0) { echo 'selected="selected"'; }?>>Disabled</option>
			<option value="1" <?php if($gc['status']==1) { echo 'selected="selected"'; }?>>Enabled</option>
		</select>		</td>
	  </tr>
  	  <tr>
		<td class="tdText"><strong><?php echo $lang['admin']['products_tax_inclusive'] ;?></strong></td>
		<td class="tdText"><input name="gc[tax]" type="checkbox" value="1" <?php if ($gc['tax']) { echo 'checked="checked"'; } ?> /></td>
	  </tr>

	  <tr>
		<td class="tdText"><strong><?php echo $lang['admin']['gc_max_amount'] ;?></strong>  </td>
		<td class="tdText"><input name="gc[max]" type="text" class="textbox" value="<?php echo $gc['max']; ?>" size="10" maxlength="10" /></td>
	  </tr>
	  <tr>
		<td class="tdText"><strong><?php echo $lang['admin']['gc_min_amount'] ;?></strong>  </td>
		<td class="tdText"><input name="gc[min]" type="text" class="textbox" value="<?php echo $gc['min']; ?>" size="10" maxlength="10" /></td>
	  </tr>

	  <tr>
	    <td class="tdText"><strong><?php echo $lang['admin']['gc_delivery'] ;?></strong></td>
	    <td class="tdText"><select name="gc[delivery]">
          <option value="1" <?php if($gc['delivery']==1) { echo 'selected="selected"'; }?>><?php echo $lang['admin']['gc_email_only']; ?></option>
          <option value="2" <?php if($gc['delivery']==2) { echo 'selected="selected"'; }?>><?php echo $lang['admin']['gc_paper_only']; ?></option>
		  <option value="3" <?php if($gc['delivery']==3) { echo 'selected="selected"'; }?>><?php echo $lang['admin']['gc_email_and_paper']; ?></option>
        </select></td>
      </tr>

	  <tr>
	    <td class="tdText"><strong><?php echo $lang['admin']['gc_paper_weight'] ;?></strong><br />
<?php echo $lang['admin']['gc_paper_weight_desc'] ;?>
</td>
	    <td class="tdText"><input name="gc[weight]" type="text" class="textbox" value="<?php echo $gc['weight']; ?>"  /></td>
      </tr>

	  <tr>
	    <td class="tdText"><strong><?php echo $lang['admin']['gc_product_code'] ;?></strong></td>
	    <td class="tdText"><input name="gc[productCode]" type="text" class="textbox" value="<?php echo $gc['productCode']; ?>"  /></td>
      </tr>

	  <tr>
	    <td class="tdText"><strong><?php echo $lang['admin']['gc_tax_code'] ;?></strong></td>
	    <td class="tdText">
		<select name="gc[taxType]">
    <?php
	$taxTypes = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_taxes');
	 for($i = 0, $maxi = count($taxTypes); $i < $maxi; ++$i){ ?>
	<option value="<?php echo $taxTypes[$i]['id']; ?>" <?php if($taxTypes[$i]['id'] == $gc['taxType']) echo 'selected="selected"'; ?>><?php echo $taxTypes[$i]['taxName'];  if (! $config_tax_mod['status']) echo "(".$taxTypes[$i]['percent']."%)"; ?></option>
	<?php } ?>
	</select>
	</td>
      </tr>

	  <tr>
		<td class="tdText">&nbsp;</td>
		<td class="tdText">
		  <input name="Submit" type="submit" class="submit" value="<?php echo $lang['admin_common']['update'];?>" />		</td>
	  </tr>
	</table>
</form>