<?php 
## if old version is 3.x.x add these to the global file
if (!array_key_exists('adminFolder', $glob) && !array_key_exists('adminFile', $glob) && !array_key_exists('license_key', $glob)) {
	
	$globAddition['adminFolder'] 	= 'admin';
	$globAddition['adminFile'] 		= 'admin.php';
	$globAddition['license_key']	= trim($_SESSION['license_key']);
	$glob['encoder']				= (has_zend_optimizer()) ? 'zend' : 'ioncube';
	
	## rootDir is no longer used in v4
	unset($glob['rootDir']);
	
	$newGlob = array_merge($glob, $globAddition);
	
	if (cc_is_writable("..".CC_DS."includes".CC_DS."global.inc.php")) {
		writeConf($newGlob,CC_ROOT_DIR.CC_DS."includes".CC_DS."global.inc.php", $glob, "glob", true);
		echo "<p>".$lang['setup']['global_file_updated']."</p>";
	} else {
		echo "<p>".$lang['setup']['global_unwritable_1']."</p>";
		
		$globCont="<?php
\$glob['dbdatabase'] 	= '".$newGlob['dbdatabase']."';
\$glob['dbhost'] 		= '".$newGlob['dbhost']."';
\$glob['dbpassword'] 	= '".$newGlob['dbpassword']."';
\$glob['dbprefix'] 		= '".$newGlob['dbprefix']."';
\$glob['dbusername'] 	= '".$newGlob['dbusername']."';
\$glob['installed'] 	= true;
\$glob['rootRel'] 		= '".$newGlob['rootRel']."';
\$glob['storeURL'] 		= '".$newGlob['storeURL']."';
\$glob['adminFolder'] 	= 'admin';
\$glob['adminFile'] 		= 'admin.php';
\$glob['license_key']	= '".trim($_SESSION['license_key'])."';
\$glob['encoder']		= '".$newGlob['encoder']."';
?>";
		
		echo "<p>includes".CC_DS."global.inc.php<br>
<textarea rows='13' cols='50'>".$globCont."</textarea></p><p>".$lang['setup']['try_again_to_complete']."</p>";
		$nextStep = 3;
		$buttonText = $lang['setup']['upgrade_try_again'];

	}
} 

## Edit config database rows to be 
$config = fetchDbConfig("config");
## Fix for langs in v4 that don't exist
$config['defaultLang'] = $_GET['l'];

## see if any of our required config values exist... if not the config must be 3.0 and we need to update them
if(	!array_key_exists('siteCountry', $config) || 
	!array_key_exists('defaultCurrency', $config) || 
	!array_key_exists('storeAddress', $config)) {

	$resultOldConfig = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_config");
	
	if($resultOldConfig==TRUE){
	
		for($i=0;$i<count($resultOldConfig);$i++){
	
			$base64Encoded = unserialize($resultOldConfig[$i]['array']);
			
			if(is_array($base64Encoded)){
			
				foreach($base64Encoded as $key => $value){
					## fix for bug #147
					$decodedKey = base64_decode($key);
					if($decodedKey=="offLineContent") {
						$base64Decoded[$decodedKey] = $value;
					} else {
						$base64Decoded[$decodedKey] = stripslashes(base64_decode($value));
					}
				}
				
				
				$fresh = serialize($base64Decoded);
				
				$data['array'] = $db->mySQLSafe($fresh);
				
				$update = $db->update($glob['dbprefix']."CubeCart_config", $data,"name=".$db->mySQLSafe($resultOldConfig[$i]['name']));
				## Unset to stop config in config
				unset($date, $fresh, $base64Encoded, $base64Decoded);
			
			}
			
		}
	
	}
	
	echo "<p>".$lang['setup']['config_files_updated']."</p>";
	## unset base64 config and get nice clean one bye bye skank
	unset($config);
	$config = fetchDbConfig("config");
	## Change the skin back to KitaBlue
	$newConf['skinDir'] = "KitaBlue";
	## Added in 4.0.0 
	$newConf['cat_tree'] = '1';
	$newConf['hide_prices'] = '0';
	$newConf['pop_products_source'] = '0';
	$newConf['cache'] = '1';
	$newConf['show_empty_cat'] = 1;
	$newConf['disable_alert_email']=0;
	$newConf['latestNewsRRS'] = 'http://forums.cubecart.com/index.php?act=rssout&id=1';
	$newConf['richTextEditor'] = true;
	$newConf['rteHeight'] = '350';
	$newConf['rteHeightUnit'] = '';
	$newConf['add_to_basket_act'] = '0';
	$newConf['stock_warn_type'] = '0';
	$newConf['stock_warn_level'] = '5';
	$newConf['changeskin'] = '0';
	$newConf['priceTaxDelInv'] = '0';
	$newConf['currecyAuto'] = '0';
	$newConf['proxy'] = '0';
	$newConf['proxyHost'] = '';
	$newConf['proxyPort'] = '';
	$newConf['sef'] = '0';
	$newConf['seftags'] = '0';
	$newConf['sefprodnamefirst'] = '0';
	$newConf['noRelatedProds'] = '3';
	
	$newConf['stock_change_time'] = '0';
	$newConf['stock_replace_time'][1] = '1';
	$newConf['stock_replace_time'][2] = '1';
	$newConf['stock_replace_time'][3] = '1';
	$newConf['stock_replace_time'][5] = '1';
	$newConf['stock_replace_time'][6] = '1';
	
	$config['orderExpire'] = '0';
	
	################################ 
	## Start Upload Folder SIze
	################################
	$dirArray = walkDir(CC_ROOT_DIR .CC_DS."images".CC_DS."uploads", true, 0, 0, false, $int = 0);
	$size = 0;
	
	if (is_array($dirArray)) {
		foreach($dirArray as $file) {
			if (file_exists($file)) {
				$size = filesize($file) + $size;
			}
		
		}
		echo "<p>Image upload folder size calculated.</p>";
	}

	$newConf['uploadSize'] = $size;
	################################ 
	## End Upload Folder SIze
	################################
	
	writeDbConf($newConf, 'config', $config, false);
	
	################################
	## Start Count products in Categories
	################################
	
	
	## Lets override the default execution time with error supression for safe mode
	@set_time_limit(0);
	$success = false;
	
	if ($config['cache']) {
		## Purge the Cache
		$cache = new cache();
		$cache->clearCache();
		$msg = "<p class='infoText'>".$lang['admin']['misc_cache_cleared']."</p>";
	}
	
	## Set the number of products in all categories to 0
	$record['noProducts'] = 0;
	$update = $db->update($glob['dbprefix'].'CubeCart_category', $record, '');
	unset($record);
	
	## Count primary categories of products
#	$prodquery	= sprintf("SELECT COUNT(productId) as count, cat_id FROM %sCubeCart_inventory WHERE disabled = '0' GROUP BY cat_id", $glob['dbprefix']);
#	$products	= $db->select($prodquery);
#	if ($products) {
#		foreach ($products as $product) {
#			$db->categoryNos($product['cat_id'], '+', $product['count']);
#		}
#		$success = true;
#	}
	
	## Delete records from cats_idx if the productId isn't in the inventory
	$idxquery = sprintf("DELETE FROM %1\$sCubeCart_cats_idx WHERE productId NOT IN (SELECT DISTINCT productId FROM %1\$sCubeCart_inventory WHERE disabled = '0')", $glob['dbprefix']);
	$db->misc($idxquery);
		
	## Count the number of products in the cats_idx table by category
	$countQuery	= sprintf("SELECT COUNT(cat_id) as count, cat_id FROM %1\$sCubeCart_cats_idx WHERE cat_id IN(SELECT DISTINCT cat_id FROM %1\$sCubeCart_cats_idx WHERE 1) GROUP BY cat_id", $glob['dbprefix']);
	$catCount	= $db->select($countQuery);
	
	if ($catCount) {
		foreach ($catCount as $category) {
			## Set the number of products in each category
			$db->categoryNos($category['cat_id'], '+', $category['count']);
		}
		$success = true;
	}
		
	if ($success) {
		echo "<p>Number of products by category counted.</p>";
	}
	
	## ----------------------------------------
	/*
	## set noProducts in all categories to 0
	$record['noProducts'] = $db->mySQLSafe(0);
	$update = $db->update($glob['dbprefix']."CubeCart_category", $record, $where="");
	unset($record);
	
	## empty cats_idx
	$empty = $db->truncate($glob['dbprefix']."CubeCart_cats_idx");
	
	## get all products
	$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory");
	
	## for each product add 1 to suitable category count and add cat_idx
	if($products==TRUE){
		for ($i=0; $i<count($products); $i++){ 
		
			## insert index for master category
			$idx_data['productId'] = $db->mySQLSafe($products[$i]['productId']);
			$idx_data['cat_id'] = $db->mySQLSafe($products[$i]['cat_id']);
			
			$cat_idx = $db->insert($glob['dbprefix']."CubeCart_cats_idx", $idx_data);
			
			## update category count
			$db->categoryNos($products[$i]['cat_id'], "+",1);
			
		}
		echo "<p>Number of products per category counted.</p>";
	
	}*/
	################################ 
	## End Count products in Categories
	################################
	
	################################ 
	## Start Count Orders
	################################
	$record['noOrders'] = $db->mySQLSafe(0);
	$update = $db->update($glob['dbprefix']."CubeCart_customer", $record, $where="");
	unset($record);
	
	// get all customers
	$customers = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_customer");
	
	if($customers==TRUE){
		for ($i=0; $i<count($customers); $i++){
			
			$noOrders = $db->numrows("SELECT * FROM ".$glob['dbprefix']."CubeCart_order_sum WHERE customer_id=".$db->mySQLSafe($customers[$i]['customer_id']));
			
			$record['noOrders'] = $noOrders;
			$result = $db->update($glob['dbprefix']."CubeCart_customer", $record, "customer_id=".$db->mySQLSafe($customers[$i]['customer_id']));
				
		}
		
		echo "<p>Orders recounted per customer.</p>";
		
	}
	################################ 
	## End Count Orders
	################################ 
	
	## Insert hidden category
	$db->misc("INSERT INTO `".$glob['dbprefix']."CubeCart_category` (`cat_name`, `cat_desc`, `cat_id`, `cat_father_id`, `cat_image`, `per_ship`, `item_ship`, `item_int_ship`, `per_int_ship`, `noProducts`, `hide`, `cat_metatitle`, `cat_metadesc`, `cat_metakeywords`, `priority`) VALUES 
('Imported Products', '##HIDDEN##', '', 0, '', 0.00, 0.00, 0.00, 0.00, 0, 1, '', '', '', NULL);");
	
}
?>