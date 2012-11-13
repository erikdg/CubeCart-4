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
|	export.inc.php
|   ========================================
|	Export Catalogue
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");
$lang = getLang('admin'.CC_DS.'admin_products.inc.php');

$whereClause = '';
if(!isset($_GET['quan'])) {
	$_GET['quan'] = 500;
}
$download_part = ($_GET['page']+1);

include('includes'.CC_DS.'sef_urls.inc.php');

if (isset($_GET['format']) && strtolower($_GET['format']) == 'googlebase') {

	$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory INNER JOIN '.$glob['dbprefix'].'CubeCart_category on '.$glob['dbprefix'].'CubeCart_inventory.cat_id = '.$glob['dbprefix'].'CubeCart_category.cat_id WHERE `disabled` = 0 ORDER BY `name` ASC';
	$results = $db->select($query, $_GET['quan'], $_GET['page']);

	if ($results) {
		$googleBaseContent = "id\tlink\ttitle\tdescription\timage_link\tprice\tcurrency\tcondition\tupc\tean\tjan\tisbn\r\n";

		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
			$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);
			$price = ($salePrice > 0) ? $salePrice : $price = $results[$i]['price'];

			$name = fix_export_string($results[$i]['name']);
			$desc = fix_export_string($results[$i]['description']);

			$googleBaseContent .= $results[$i]['productId']."\t";

			if ($config['sef'] == 0) {
				$googleBaseContent .= $glob['storeURL']."/index.php?_a=viewProd&productId=".$results[$i]['productId']."\t".$name."\t".$desc;
			} else {
				
				$googleBaseContent .= $glob['storeURL']. "/" .generateProductUrl($results[$i]['productId'])."\t".$name."\t".$desc;
			}

			if ($results[$i]['image']) {
				$googleBaseContent .= "\t".$glob['storeURL']."/images/uploads/".str_replace(" ","%20",$results[$i]['image']);
			} else {
				$googleBaseContent .= "\t".$glob['storeURL']."/skins/".$config['skinDir']."/styleImages/nophoto.gif";
			}

			$googleBaseContent .= "\t".$price."\t".$config['defaultCurrency']."\tnew";
			
			$googleBaseContent .= "\t".$results[$i]['upc']."\t".$results[$i]['ean']."\t".$results[$i]['jan']."\t".$results[$i]['isbn']."\r\n";
		}
		

		$filename = 'GoogleBaseFeed_'.date('Ymd').'_'.$download_part.'.txt';
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-type: text/plain');
		header('Content-length: '.strlen($googleBaseContent));
		header('Content-Transfer-Encoding: binary');
		echo $googleBaseContent;
		exit;
	}
} else if (isset($_GET['format']) && strtolower($_GET['format']) == 'shopzilla') {

	$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory INNER JOIN '.$glob['dbprefix'].'CubeCart_category on '.$glob['dbprefix'].'CubeCart_inventory.cat_id = '.$glob['dbprefix'].'CubeCart_category.cat_id WHERE `disabled` = 0 ORDER BY `name` ASC';
	$results = $db->select($query, $_GET['quan'], $_GET['page']);

	if ($results) {
		$shopzillaContent = "Category\tManufacturer\tTitle\tProduct Description\tLink\tImage\tSKU\tStock\tCondition\tShipping Weight\tShipping Cost\tBid\tPromotional Description\tEAN/UPC\tPrice\n";

		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
			$row = $results[$i];

			$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);
			$price = ($salePrice > 0) ? $salePrice : $price = $results[$i]['price'];
			
				if($price>0) {				
					$name = fix_export_string($results[$i]['name']);
					$name = substr($name, 0, 100);
		
					$desc = fix_export_string($results[$i]['description']);
					$desc = substr($desc, 0, 1000);
		
					$cat_name = fix_export_string($results[$i]['cat_name']);
		
					if (!empty($name)) {
						if ($config['sef'] == 0) {
		                    $shopzillaContent .= $cat_name."\t\t".$name."\t\"".$desc."\"\t".$glob['storeURL']."/index.php?_a=viewProd&productId=".$results[$i]['productId'];
		                } else {
		                    $shopzillaContent .= $cat_name."\t\t".$name."\t\"".$desc."\"\t".$glob['storeURL'].'/'.generateProductUrl($results[$i]['productId']);
		                }
		
		                if ($results[$i]['image']) {
		                    $shopzillaContent .= "\t".$glob['storeURL']."/images/uploads/".str_replace(" ","%20",$results[$i]['image']);
		                } else {
		                    $shopzillaContent .= "\t".$glob['storeURL']."/skins/".$config['skinDir']."/styleImages/nophoto.gif";
		                }
		                
		                $shopzillaContent .= "\t".$results[$i]['productCode']."\tIn Stock\tNew\t".$results[$i]['prodWeight']."\t\t\t\t".$results[$i]['upc']."\t".$price."\n";
				}
      		}
		}

		$filename = 'ShopZilla_'.date('Ymd').'_'.$download_part.'.txt';

		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-type: text/plain');
		header('Content-length: '.strlen($shopzillaContent));

		//header("Content-Transfer-Encoding: binary");
		echo $shopzillaContent;
		exit;
	}
} elseif (isset($_GET['format']) && strtolower($_GET['format']) == 'shopping.com') {

	$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE `disabled` = 0 ORDER BY name ASC';
	$results = $db->select($query, $_GET['quan'], $_GET['page']);

	if ($results) {
		#$shoppingContent = "mpn,upc,manufacturer,product name,product description,price,stock,stock description,product url,image url,category\n";
		$shoppingContent = "Unique Merchant SKU,upc,product name,product description,Current Price,stock,stock description,product url,image url,category,Shipping Rate\n";

		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
			$row = $results[$i];

			$salePrice = salePrice($results[$i]['price'], $results[$i]['sale_price']);
			$price = ($salePrice > 0) ? $salePrice : $price = $results[$i]['price'];

			$name = fix_export_string($results[$i]['name']);
			$desc = fix_export_string($results[$i]['description']);

			if (!empty($name) && $price > 0) {
				if ($config['sef'] == 0) {
					$url = $glob['storeURL']."/index.php?_a=viewProd&productId=".$results[$i]['productId'];
				} else {
					$url = $glob['storeURL'].'/'.generateProductUrl($results[$i]['productId']);
				}
				if ($results[$i]['image']) {
					$image = $glob['storeURL']."/images/uploads/".str_replace(" ","%20",$results[$i]['image']);
				} else {
					$image = $glob['storeURL']."/skins/".$config['skinDir']."/styleImages/nophoto.gif";
				}
				#$shoppingContent .= sprintf("%s,%s,%s,\"%s\",\"%s\",%s,%s,%s,%s,%s,%s\n", $row['productCode'], $row['upc'], '', $name, addslashes(html_entity_decode_utf8($desc, ENT_QUOTES)), $price, $stock, '', $url, $image, '');
				$shoppingContent .= sprintf("%s,%s,%s,\"%s\",\"%s\",%s,%s,%s,%s,%s,%s\n", $row['productCode'], $row['upc'], $name, addslashes(html_entity_decode_utf8($desc, ENT_QUOTES)), $price, $stock, '', $url, $image, '','');
			}
		}

		$filename = 'Shopping.com_'.date('Ymd').'_'.$download_part.'.txt';
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-type: text/plain');
		header('Content-length: '.strlen($shoppingContent));
		//header('Content-Transfer-Encoding: binary');
		echo $shoppingContent;
		exit;
	}
} else if (isset($_GET['format']) && strtolower($_GET['format']) == 'cubecart') {

	$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE `disabled` = 0';
	$results = $db->select($query, $_GET['quan'], $_GET['page']);

	if ($results) {

	$ccContent = "Product Name,Product Code,Product Description,Price,Sale Price,Image,Stock Level,Use Stock,Master Category ID,UPC,EAN,JAN,ISBN\r\n";

		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

			$name = fix_export_string($results[$i]['name']);
			$desc = fix_export_string($results[$i]['description']);

			$ccContent 	.= 	"\"".$name.
							"\",\"".$results[$i]['productCode'].
							"\",\"".$desc.
							"\",\"".$results[$i]['price'].
							"\",\"".$results[$i]['sale_price'].
							"\",\"".str_replace(" ","%20",$results[$i]['image']).
							"\",\"".$results[$i]['stock_level'].
							"\",\"".$results[$i]['useStockLevel'].
							"\",\"".$results[$i]['cat_id'].
							"\",\"".$results[$i]['upc'].
							"\",\"".$results[$i]['ean'].
							"\",\"".$results[$i]['jan'].
							"\",\"".$results[$i]['isbn'].
							"\"\r\n";

		}

		$filename = 'CubeCart_Products_'.date('Ymd').'_'.$download_part.'.csv';
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-type: text/plain');
		header('Content-length: '.strlen($ccContent));
		header('Content-Transfer-Encoding: binary');
		echo $ccContent;
		exit;
	}

} else if (isset($_GET['format']) && strtolower($_GET['format']) == 'csv') {

	$query		= 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory INNER JOIN '.$glob['dbprefix'].'CubeCart_category on '.$glob['dbprefix'].'CubeCart_inventory.cat_id = '.$glob['dbprefix'].'CubeCart_category.cat_id WHERE `disabled` = 0 ORDER BY `name` ASC';
	$results	= $db->select($query, $_GET['quan'], $_GET['page']);

	if ($results) {

		$output[] = '';
	}

}

###################################################

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");


/*
if (isset($_GET['export'])) {
	$msg = 'Creating catalogue backup...';

	$query		= sprintf("SELECT * FROM `%sCubeCart_inventory`", $glob['dbprefix']);
	$results	= $db->select($query);

	function formatstrings(&$value, $key, $separator) {
		$value = str_replace(array("\n","\r"), '', $value);
		$value = str_replace($separator, '\\'.$separator, $value);
	}

	$csvfile = '/tmp/exportfile';

	$separator = (isset($_GET['separator'])) ? $_GET['separator'] : ',';
	for ($i=0;$i<count($results);$i++) {
		if ($_GET['format'] == 'csv') {
			$row = $results[$i];
			if ($i==0) {
				foreach ($row as $key => $val) {
					$fields[] = $key;
				}
				$output[] = implode($separator, $fields);
			}
			// create CSV file with headers
			array_walk($row, 'formatstrings', $separator);
			$output[] = implode($separator, $row);
		}
		// write to file?
	}
	$fp = fopen($csvfile, 'w+');
	fwrite($fp, implode("\n", $output));
	fclose($fp);

	// Create the archive
	include "/home/martin/cubecart/classes/misc/zip.inc.php";
	$createZip = new createZip;

	$createZip->addDirectory("catalog/");

	$zipArray[] = $csvfile;

	$createZip->addFiles($zipArray);

	$fileContents = file_get_contents("/tmp/exportfile");
	$createZip->addFile($fileContents, "dir/exportfile");

	$createZip->saveArchive('archive.zip');
//	$fileName = "archive.zip";


	$fd = fopen ($fileName, "wb");
	$out = fwrite ($fd, $createZip->getZippedfile());
	fclose ($fd);

	//@unlink($csvfile);
}
*/


?>
<p class="pageTitle"><?php echo $lang['admin']['products_export_cat']?></p>
<?php
$lang = getLang('admin'.CC_DS.'admin_orders.inc.php');
$numrows = $db->numrows('SELECT `productId` from '.$glob['dbprefix'].'CubeCart_inventory');
if(!$pagination = paginate($numrows, $_GET['quan'], $page, 'page', 'txtLink', 1000, false, true)) {
	$pagination = '<a class="txtLink" href="?_g=products%2Fexport&amp;page=0&amp;quan='.$_GET['quan'].'">1</a>';
}
?>
Number of products per export <select name="productsPerPage" class="dropDown" onchange="jumpMenu('parent',this,0)">
  <?php
  $range = array(
  	50, 100, 250, 500, 1000, 5000, 10000, 25000, 50000
  );
  foreach($range as $value) {
	 ?>
	 <option value="?_g=products/export&amp;quan=<?php echo $value; ?>" <?php if($value == $_GET['quan']) echo 'selected="selected"'; ?>><?php echo number_format($value); ?></option>
	 <?php
  }
  ?>

</select>
<h4>Google Base</h4>
<?php
echo $lang['admin']['orders_download_link'].' '.str_replace('export','export&amp;format=googlebase',$pagination);
?>
<h4>Shopping.com</h4>
<?php
echo $lang['admin']['orders_download_link'].' '.str_replace('export','export&amp;format=shopping.com',$pagination);
?>
<h4>ShopZilla</h4>
<?php
echo $lang['admin']['orders_download_link'].' '.str_replace('export','export&amp;format=shopzilla',$pagination);
?>
<h4>CubeCart CSV (Comma Separated Value)</h4>
<?php
echo $lang['admin']['orders_download_link'].' '.str_replace('export','export&amp;format=CubeCart',$pagination);
?>
