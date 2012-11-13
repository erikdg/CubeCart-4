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
|	functions.inc.php
|   ========================================
|	Core Frontend Functions
+--------------------------------------------------------------------------
*/

@ini_set('allow_call_time_pass_reference', true);

## is_writable
function cc_is_writable($path) {
	return (is_writable($path)) ? true : false;
}

## Get Client IP Address
function get_ip_address() {
    ## New line added for cluser/cloud type hosting e.g. Mosso
	$detected	= (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !detectSSL()) ? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
	## Otherwise use standard IP checks
	$address    = false;
	if (!empty($detected)) {
	    if (PHP51_MODE) {
	        if (preg_match('#(?:\d{1,3}\.){3}\d{1,3}#', $detected)) {
	            ## Valid IPv4 Address
	            $address    = $detected;
	            if (preg_match('#^(10\.[0-255]|169\.254|172\.(1[6-9]|2[0-9]|3[12])|192\.168)#', $address)) {
	                foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP') as $key) {
	                    if (isset($_SERVER[$key]) && preg_match('#^[0-255]\.[0-255]\.[0-255]\.[0-255]$#', $_SERVER[$key])) {
	                        $address = $_SERVER[$key];
	                        break;
	                    }
	                }
	            }
	        }
	    } else {
	        if (filter_var($detected, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
	            ## Valid IPv4 Address
	            $address    = $detected;
	            if (preg_match('#^(10\.[0-255]|169\.254|172\.(1[6-9]|2[0-9]|3[12])|192\.168)#', $address)) {
	                foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP') as $key) {
	                    if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
	                        $address = $_SERVER[$key];
	                        break;
	                    }
	                }
	            }
	        } elseif (filter_var($detected, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
	            ## Valid IPv6 Address
	            $address    = $detected;
	        }
		}
		return $address;
	} else {
		return false;
	}
}

## Macro Substitution
function macroSub($string, $macroArray) {
	if (is_array($macroArray)) {
		foreach ($macroArray as $key => $value) {
			$string = str_replace("{".$key."}",$value,$string);
		}
	}
	return $string;
}

## Page redirection
function httpredir($target, $meta_refresh = false) {
	$target = html_entity_decode(str_replace('amp;', '', $target));
	if($meta_refresh) {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Redirecting...</title>
		<meta http-equiv="Refresh" content="0;URL='.$target.'" />
		</head>
		<body>
		</body>
		</html>';
		exit;
	} else {
		## Possible IIS bugfix
		header('Content-Type: text/html');
		header('HTTP/1.0 200 OK');
	#	$target = preg_replace('#/+#', '/', urldecode($target));
		header('Location: '.$target);
		exit;
	}
}

## Detect if store is in SSL mode
function detectSSL() {
	if (!isset($_SERVER['HTTPS'])) {
		return false;
	}
	return (strtolower($_SERVER['HTTPS']) !== 'off' && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == true || $_SERVER['SERVER_PORT'] == 443)) ? true : false;
}

## Detect GD Image
function detectGD() {
	if (extension_loaded('gd') && function_exists('gd_info')) {
		# We only support GD2 now
		return 2;
	#	$gd = gd_info();
	#	$version = preg_replace('#[^0-9\.]#i', '', $gd['GD Version']);
	#	return sprintf('%d', $version);
	}
	return false;
}

## Create w3c compliant output
function validHTML($var) {
	## prevent double encoding
	$var = html_entity_decode($var, ENT_QUOTES, 'UTF-8');
	$var = htmlspecialchars($var);
	return str_replace("&amp;#39;", "&#39;", $var);
}

## Sanitize GET/POST variables to prevent XSS attacks
function sanitizeVar($text) {
	$text = htmlspecialchars($text, ENT_COMPAT);
	return $text;
}

function walkArray(&$input, $function = 'walkStripSlashes') {
	if (is_array($input)) {
		array_walk_recursive($_GET, $function);
	}
}

function walkStripSlashes(&$item, $key) {
	$item = stripslashes($item);
	return $item;
}

## Get current page
function currentPage($excluded = array()) {
	global $glob, $config;

	$storeURL		= str_replace('http://', '', $glob['storeURL']);
	$storeURL_SSL	= str_replace('https://', '', $config['storeURL_SSL']);
	$phpSelf		= sanitizeVar($_SERVER['PHP_SELF']);

	## Exception for lookback
	if ($config['sef'] && $config['sefserverconfig'] == 2 && !strstr($phpSelf, $glob['adminFile'])) {
		if ($glob['rootRel'] == '/' || !preg_match('#(/index.php|'.$glob['rootRel'].')#iu', $phpSelf)) {
			$phpSelf = '/index.php'.$phpSelf;
		}
	}

	if ($storeURL != $storeURL_SSL && $config['ssl'] && !$config['force_ssl']) {
		## For shared SSL full URL
		//$currentPage = $GLOBALS['storeURL'].str_replace($GLOBALS['rootRel'], '/', $phpSelf);
		// Changed below for Zeus webserver!! Seems to work ok for shared SSL on Zeus.
		$currentPage = $GLOBALS['storeURL'].str_replace(array($config['rootRel_SSL'],$glob['rootRel']), array('/','/'), $phpSelf);
	} else {
		## For dedicated SSL relative URL
		$currentPage = $phpSelf;
	}

	## If GET vars is an array and $params merge them together
	if (is_array($_GET)) walkArray($_GET, 'sanitizeVar');

	## If there are to be GET vars strip redir and rebuild query string
	if (!empty($_GET)) {
		$i = 1;
		foreach ($_GET as $key => $value) {
			if(is_array($_GET[$key])) {
				foreach($_GET[$key] as $arrayKey => $arrayValue) {
				$currentPage .= ($i == 1) ? '?' : '&amp;';
					$currentPage .= $key.'['.$arrayKey.']'.'='.urlencode(html_entity_decode(stripslashes($arrayValue)));
					++$i;
				}
			} else {
				$currentPage .= ($i == 1) ? '?' : '&amp;';
				if ($key != 'redir' && (!array_key_exists($key, $excluded))) { // || !in_array($key, $excluded))) {
					$currentPage .= $key.'='.urlencode(html_entity_decode(stripslashes($value)));
				}
				++$i;
			}
		}
	}
	return $currentPage;
}


## Format filesizes into something more friendly
function format_size($rawSize) {
	if ($rawSize / 1048576 > 1) {
		return round($rawSize/1048576, 1).' MB';
	} elseif ($rawSize / 1024 > 1) {
		return round($rawSize/1024, 1).' KB';
	} else {
		return round($rawSize, 1).' Bytes';
	}
}

## Get Category Directory
function getCatDir($catName, $cat_father_id, $catId, $link=false, $skipFirstSymbol=false, $reverseSort=true, $admin=false) {
	global $db, $config, $glob;

	// get category array for cat dir
	$cache		= new cache('misc.catArray');
	$catArray	= $cache->readCache();

	if (!$cache->cacheStatus) {
		$query		= 'SELECT `cat_id`, `cat_name`, `cat_father_id` FROM '.$glob['dbprefix'].'CubeCart_category ORDER BY `cat_id` DESC';
		$catArray	= $db->select($query);
		$cache->writeCache($catArray);
	}

	// get category array in foreign innit
	// get category array for cat dir
	$cache = new cache('misc.catArrayForeign.'.LANG_FOLDER);
	$catArrayForeign = $cache->readCache();

	if (!$cache->cacheStatus) {
		$catArrayForeign = $db->select("SELECT `cat_master_id` as cat_id, `cat_name` FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE `cat_lang` = '".LANG_FOLDER."'");
		$cache->writeCache($catArrayForeign);
	}

	if (empty($config['dirSymbol'])) $config['dirSymbol'] = '/';

	if ($link) {
		if (!$admin) {
			$dirArray[0] = $config['dirSymbol']."<a href='".$GLOBALS['rootRel']."index.php?_a=viewCat&amp;catId=".$catId."' class='txtLocation'>".$catName."</a>";
		} else {
			$dirArray[0] = $config['dirSymbol']."<a href='".$glob['adminFile']."?_g=categories/index&amp;parent=".$catId."' class='txtLink'>".$catName."</a>";
		}
	} else {
		$dirArray[] = $config['dirSymbol'].$catName;
	}

	foreach ($catArray as $i => $cat) {
		if (is_array($catArrayForeign) && !empty($catArrayForeign)) {
			foreach ($catArrayForeign as $k => $catForeign) {
				if ($catForeign['cat_id'] == $cat['cat_id']) {
					$catArray[$i]['cat_name'] = validHTML($catForeign['cat_name']);
				}
			}
		}

		if (isset($cat['cat_id']) && $cat['cat_id'] == $cat_father_id) {
			if ($link) {
				if ($admin) {
					$dirArray[$i+1] = $config['dirSymbol']."<a href='".$glob['adminFile']."?_g=categories/index&amp;parent=".$catArray[$i]['cat_id']."' class='txtLink'>".$catArray[$i]['cat_name']."</a>";
				} else {
					$dirArray[$i+1] = $config['dirSymbol']."<a href='".$GLOBALS['rootRel']."index.php?_a=viewCat&amp;catId=".$catArray[$i]['cat_id']."' class='txtLocation'>".$catArray[$i]['cat_name']."</a>";
				}
			} else {
				$dirArray[]	= $config['dirSymbol'].$catArray[$i]['cat_name'];
			}

			$cat_father_id = $cat['cat_father_id'];
		}
	}

	if ($reverseSort) {
		krsort($dirArray);
	} else {
		ksort($dirArray);
	}
	reset($dirArray);

	$dir = '';
	foreach ($dirArray as $key => $value){
	 	$dir .= $value;
	}

	if ($skipFirstSymbol) {
		$dir = substr($dir, strlen($config['dirSymbol']));
	}

	return $dir;
}

## alternate row colours
function cellColor($i, $tdEven = "tdEven", $tdOdd = "tdOdd") {
	return ($i%2) ? $tdOdd : $tdEven;
}

## Sale Price
function salePrice($normPrice, $salePrice = 0) {
	switch ($GLOBALS['config']['saleMode']) {
		case 1:
			## Individual sale price
			if (is_numeric($salePrice) && $salePrice > 0 && $salePrice != $normPrice) {
				return $salePrice;
			}
			break;
		case 2:
			# Global percentage discount
			$saleValue = $normPrice * ((100-$GLOBALS['config']['salePercentOff'])/100);
			if (is_numeric($saleValue) && $saleValue > 0 && $saleValue != $normPrice) {
				return $saleValue;
			}
			break;
		default:
			return false;
	}
	return false;
}

## Price formatting
function priceFormat($price, $dispNull = true) {
	global $currencyVars, $config, $lang, $cc_session;

	if ($dispNull && is_numeric($price)) {
		if ($config['hide_prices'] && !$cc_session->ccUserData['customer_id'] && !$GLOBALS[CC_ADMIN_SESSION_NAME]) {

			$hiddenTxt = (isset($lang['front']['misc_price_hidden'])) ? $lang['front']['misc_price_hidden'] : "???" ;
			return "<span onclick=\"alert('".$lang['front']['login_view_price']."');\" style=\"cursor: help;\">".$currencyVars[0]['symbolLeft'].$hiddenTxt.$currencyVars[0]['symbolRight']."</span>";
		} else {
			$price = ($price*$currencyVars[0]['value']);
			$decimalSymbol = (isset($currencyVars[0]['decimalSymbol']) && $currencyVars[0]['decimalSymbol'] == 1) ? ',' : '.';
			return $currencyVars[0]['symbolLeft'].number_format($price, $currencyVars[0]['decimalPlaces'], $decimalSymbol, '').$currencyVars[0]['symbolRight'];
		}
	} else {
		return false;
	}
}

## Walk through files and folders in directory
function walkDir($path, $transverse = true, $limit = 0, $page = 0, $folder = false, &$i) {

	if ($limit>0) {
		if ($page>0) {
			$endNode = ($page+1) * $limit;
			$startNode = $endNode - $limit;
		} else {
			$startNode = 0;
			$endNode = $limit;
		}
	} else {
		$buildAll = true;
	}

	$retval = array();
	$files 	= array();

	$path = str_replace('/', CC_DS, $path);
	if (substr($path, strlen($path)-1, 1) == CC_DS) $path = substr($path, 0, strlen($path)-1);

	if (($dir = opendir($path)) !== false) {
		while (false !== ($file = readdir($dir))) {
			if ($file[0] == '.') continue;

			if (!preg_match('#^\.+#', $file)) {
				if (is_dir($path.CC_DS.$file) && $file != 'thumbs') {
					++$i;
					if ($folder) {
						if ($buildAll || ($i>=$startNode && $i<$endNode)) {
							$dirs[] = $path.CC_DS.$file;
						}
					}
					if ($transverse) {
						++$i;
						$retValMerge = walkDir($path.CC_DS.$file,$transverse, $limit, $page, $folder, $i);
						if (is_array($retValMerge)) {
							$files = array_merge($files,$retValMerge);
						}
					}
				} else if (is_file($path.CC_DS.$file)) {
					++$i;
					if ($buildAll || ($i>=$startNode && $i<$endNode)) {
						$files[] = $path.CC_DS.$file;
					}
				}
			}
		}

		if (is_array($dirs) && is_array($files)) {
			natcasesort($files);
			natcasesort($dirs);
			$retval = array_merge($dirs, $files);
		} else if(is_array($dirs)) {
			natcasesort($dirs);
			$retval = $dirs;
		} else if(is_array($files)) {
			natcasesort($files);
			$retval = $files;
		}

		closedir($dir);
	}

	## max amount is only needed if it is paginated
	if ($limit>0) $retval['max'] = $i;
	return $retval;
}

function paginate($numRows, $maxRows, $pageNum=0, $pageVar='page', $class='txtLink', $limit=5, $excluded = array(),$download_links = false) {
	global $lang, $config;
	$navigation = '';

	## Removed flash basket variable
	$excluded['added'] = 1;

	## Get total pages
	$totalPages = ceil($numRows/$maxRows);

	## Zeus Web Server Like to do things differently! Work around below to get our Query String
	if(stripos($_SERVER['SERVER_SOFTWARE'], 'ZEUS') !== false) {
		if(is_array($_GET)) {
			$getVars = $_GET;
			unset($getVars['q']);
			$QUERY_STRING = '';
			foreach($getVars as $key => $value){
				$QUERY_STRING .= $key.'='.$value.'&';
			}
		}
		$QUERY_STRING = rtrim($QUERY_STRING, '&');
	} else {
		$QUERY_STRING = $_SERVER['QUERY_STRING'];
	}
	if (!empty($QUERY_STRING)) {
		parse_str($QUERY_STRING, $params);
		foreach ($params as $key => $value) {
			if (!array_key_exists($key, $excluded) && strtolower($key) !== strtolower($pageVar)) {
				$newParams[$key] = $value;					# PHP5
			}
		}
	}
	## Get current page
	$currentPage = sanitizeVar($_SERVER['PHP_SELF']);
#	$currentPage = ((bool)$config['sef']) ? sanitizeVar($_SERVER['REQUEST_URI']) : sanitizeVar($_SERVER['PHP_SELF']);

	## Build page navigation
	if ($totalPages > 1) {
		if (!empty($lang['admin_common']['misc_pages'])) {
			$pageText = $lang['admin_common']['misc_pages'];
		} else {
			$pageText = $lang['front']['misc_pages'];
		}
		$navigation		= ($download_links) ? '' : $totalPages.$pageText;
		$upper_limit	= $pageNum + $limit;
		$lower_limit	= $pageNum - $limit;

		if ($pageNum > 0) {
			## Show, if not the first page
			if (($pageNum-2)>=0) {
				$newParams[$pageVar] = 0;
				$first	= sprintf('%s?%s', $currentPage, http_build_query($newParams));
				$navigation .= "<a href='".$first."' class='".$class."'>&laquo;</a> ";
			}
			$newParams[$pageVar] = max(0, $pageNum-1);
			$prev	= sprintf('%s?%s', $currentPage, http_build_query($newParams));
			$navigation .= "<a href='".$prev."' class='".$class."'>&lt;</a> ";
		}

		## get in between pages
		for ($i=0; $i<$totalPages; ++$i) {

			$pageNo = $i+1;
			$newParams[$pageVar] = $i;

			if (!$download_links && $i==$pageNum) {
				$navigation	.= "&nbsp;<strong>[".$pageNo."]</strong>&nbsp;";
			} else if ($i!=$pageNum && $i<$upper_limit && $i>$lower_limit || $download_links) {
				$noLink = sprintf('%s?%s', $currentPage, http_build_query($newParams));
				$navigation	.= "&nbsp;<a href='".$noLink."' class='".$class."'>".$pageNo."</a>&nbsp;";
			} else if (($i - $lower_limit)==0) {
				$navigation	.=  "&hellip;";
			}
		}

		if (!$download_links && ($pageNum+1) < $totalPages) { // Show if not last page
			$newParams[$pageVar] = min($totalPages, $pageNum+1);
			$next = sprintf('%s?%s', $currentPage, http_build_query($newParams));
			$navigation .= "<a href='".$next."' class='".$class."'>&gt;</a> ";
			if (($pageNum+3)<=$totalPages) {
				$newParams[$pageVar] = $totalPages-1;
				$last	= sprintf('%s?%s', $currentPage, http_build_query($newParams));
				$navigation .= "<a href='".$last."' class='".$class."'>&raquo;</a>";
			}
		}
	}
	return $navigation;
}


## List Modules
function listModules($path) {
	return listAddons($path);
}


function listAddons($path) {
	foreach (glob($path.CC_DS.'*') as $dirpath) {
		$folder = basename($dirpath);
		if (is_dir($dirpath) && !preg_match('#^[\._]#iuU', $folder)) {
			$folderList[] = $folder;
		}
	}
	natcasesort($folderList);
	return (is_array($folderList)) ? $folderList : false;
}

function loadAddonConfig($path) {
	if (file_exists($path.CC_DS.'package.conf.php')) {
		$string = file_get_contents($path.CC_DS.'package.conf.php');
		return unserialize($string);
	}
	return false;
}

## Check Image Extension
function checkImgExt($filename) {
	if (preg_match('/\.(gif|jpg|jpeg|png)$/i', $filename)) {
		return true;
	}
	return false;
}

//////////////////////////////////
// Make time from time()
////////
function formatTime($timestamp, $format = false) {
	global $config;

	$format =  (!$format) ? $config['timeFormat'] : $format;
	$value = substr($config['timeOffset'], 1);

	switch (substr($config['timeOffset'], 0, 1)) {
		case '+':
			$timestamp += $value;
			break;
		case '-':
			$timestamp -= $value;
			break;
	}
//	die($format);
	return strftime($format, $timestamp);
}

## Generate a random password
function randomPass($max = 8) {
	$chars = array('a','A','b','B','c','C','d','D','e','E','f','F','g','G','h','H','i','I','j','J', 'k','K','l','L','m','M','n','N','o','O','p','P','q','Q','r','R','s','S','t','T', 'u','U','v','V','w','W','x','X','y','Y','z','Z','1','2','3','4','5','6','7','8','9','0');

	$max_chars = count($chars) - 1;
	srand((double)microtime()*1000000);
	for ($i = 0; $i < $max; ++$i) {
		$newPass = ($i == 0) ? $chars[rand(0, $max_chars)] : $newPass . $chars[rand(0, $max_chars)];
	}
	return $newPass;
}
//////////////////////////////////
// Recover Post Variables as hidden fields
////////
function recoverPostVars($array, $skipKey) {

	$hiddenFields = '';
	foreach ($array as  $key => $value){
		## Strip quotes
		$value = str_replace(array("\\'","'"),"&#39;",$value);

		## Strip slashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		if ($key == $skipKey) {
			$hiddenFields .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\r\n";
		} else {
			$hiddenFields .= '<input type="hidden" name="'.$key.'" value="'.validHTML($value).'" />'."\r\n";
		}
	}

	return $hiddenFields;
}

## Convert seconds into Human Readable
function readableSeconds($time = 0) {

	$hours    = (int)floor($time/3600);
	$minutes  = (int)floor($time/60)%60;
	$seconds  = (int)$time%60;
	$output   = '';

	if ($hours == 1) {
		$output = $hours.' hour';
	} else if ($hours>1) {
		$output  = $hours.' hours';
	}
	if ($output && $minutes>0 && $seconds>0) {
		$output .= ', ';
	} else if ($output && $minutes>0 && $seconds == 0) {
		$output .= ' and ';
	}

	$s = ($minutes>1)  ? 's' : NULL;
	if ($minutes>0) $output .= $minutes.' minute'.$s;
	$s = ($seconds>1) ? 's' : NULL;
	if ($output && $seconds>0) $output .= ' and ';
	if ($seconds>0) {
		$output .= $seconds.' second'.$s;
	} else if (!$output && $seconds == 0) {
		$output  = '0 seconds';
	}
	return $output;
}

## Get county/state abbreviation by ID
function countyAbbrev($id) {
	global $db,$glob;
	$county = $db->select('SELECT `abbrev` FROM '.$glob['dbprefix'].'CubeCart_iso_counties WHERE `id` = '.$db->mySQLSafe($id));
	return ($county) ? $county[0]['abbrev'] : false;
}

## Get country ISO by ID
function getCountryFormat($in, $inCol = 'id', $outCol = 'printable_name') {
	global $db,$glob;
	$country = $db->select('SELECT `'.$outCol.'` FROM '.$glob['dbprefix'].'CubeCart_iso_countries WHERE `'.$inCol.'` = '.$db->mySQLSafe($in));
	return ($country) ? $country[0][$outCol] : false;
}

## Get Tax by ID
function taxRate($id) {
	global $db,$glob;
	// start mod: Flexible Taxes (http://www.beadberry.com/cubemods)
	$config_tax_mod = fetchDbConfig('Multiple_Tax_Mod');
	if ($config_tax_mod['status']) {
		return false;
	}
	// end mod: Flexible Taxes
	$tax = $db->select('SELECT `percent` FROM '.$glob['dbprefix'].'CubeCart_taxes WHERE `id` = '.$db->mySQLSafe($id));
	return ($tax) ? $tax[0]['percent'] : false;
}

## Get order status by Id
function orderStatus($id) {
	$lang = getLang('orders.inc.php');
	return $lang['glob']['orderState_'.$id];
}

## Validate Email Address
function validateEmail($email) {
	 if(PHP51_MODE) {
		if (preg_match('#^([a-z0-9%`=~&\'\_\.\-\+\!\$\*\?\^\{\}\/\|]+)\@([a-z0-9\.\-]+)\.[a-z]{2,6}$#iuU', strtolower($email))) {
			return true;
		} else {
			return false;
		}
	} else {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}

## Get alternative language of product
function prodAltLang($productId) {
	global $db, $glob, $config;
	if (LANG_FOLDER != $config['defaultLang']) {
		$foreignVal = $db->select("SELECT `name`, `description` FROM ".$glob['dbprefix']."CubeCart_inv_lang WHERE `prod_master_id` = ".(int)$db->mySQLSafe($productId)." AND `prod_lang` = ".$db->mySQLSafe(LANG_FOLDER));
		if ($foreignVal) {
			return $foreignVal[0];
		}
	}
	return false;
}

// start: Flexible Taxes, by Estelle Winterflood
//////////////////////////////////
// jsGeoLocationExtended
// Update county field based on country field, extended to support revealing
// and hiding of two county fields (one a select field and the other a text
// field)
////////
function jsGeoLocationExtended($countryVar, $countyVar, $nullText, $divSelect, $divOther, $idOther, $idWhichField) {

	global $config, $db, $lang, $glob;
	## Get iso counties
	$isoCounties = $db->select("SELECT * FROM  ".$glob['dbprefix']."CubeCart_iso_counties ORDER BY `countryId`, `name` ASC;");

	$jsScript = <<<JSCRIPT
	/* <![CDATA[ */

	function updateCounty(theForm) {
		var NumState = theForm.{$countyVar}.options.length;
		var CurrentCountry = '';
		while(NumState > 0) {
		NumState--;
		theForm.{$countyVar}.options[NumState] = null;
	}
	CurrentCountry = theForm.{$countryVar}.options[theForm.{$countryVar}.selectedIndex].value;
JSCRIPT;

	for ($i = 0, $maxi = count($isoCounties); $i < $maxi; ++$i) {
		if ($i==0) {
			$optionKey = 0;
			$jsScript .= "\r\n  if (CurrentCountry == \"".$isoCounties[$i]['countryId']."\") {\r\n\r\n";
		} else if ($oldCountryId != $isoCounties[$i]['countryId']) {
			$optionKey = 0;
			$jsScript .= "\r\n  } else if (CurrentCountry == \"".$isoCounties[$i]['countryId']."\") {\r\n\r\n";
		}

		$countyName = $isoCounties[$i]['name'];
		if (strlen($countyName)>20) {
			$countyName = substr($countyName,0,20)."..";
		}
		$jsScript .= "    theForm.".$countyVar.".options[".$optionKey."] = new Option(\"".$countyName."\", \"".$countyName."\");\r\n";
		$oldCountryId = $isoCounties[$i]['countryId'];
		++$optionKey;
	}

	$jsScript .= "\r\n  } else { \r\n    theForm.".$countyVar.".options[0] = new Option(\"".$nullText."\", \"\"); \r\n  } \r\n";
	$jsScript .= "\r\n  if (theForm.".$countyVar.".options.length <= 1) { \r\n";
	$jsScript .= "    findObj('".$divOther."').style.display='block'; \r\n";
	$jsScript .= "    findObj('".$divSelect."').style.display='none'; \r\n";
	$jsScript .= "    findObj('".$idWhichField."').value='T'; \r\n";
	//$jsScript .= "    findObj('".$idOther."').value=''; \r\n";
	$jsScript .= "  } else { \r\n";
	$jsScript .= "    findObj('".$divOther."').style.display='none'; \r\n";
	$jsScript .= "    findObj('".$divSelect."').style.display='block'; \r\n";
	$jsScript .= "    findObj('".$idWhichField."').value='S'; \r\n";
	$jsScript .= "  }\r\n";
	$jsScript .= "\r\n} \r\n /* ]]> */";
	return $jsScript;
}


## Retrieve Spam code
function fetchSpamCode($ESC, $del=false) {
	global $db, $glob;
	## Check DB
	$sql = sprintf("SELECT SpamCode, userIp FROM %sCubeCart_SpamBot WHERE uniqueId = %s", $glob['dbprefix'], $db->mySQLSafe($ESC));
	$result = $db->select($sql);
	if ($result) {
		if ($del) {
			## Delete this SpamCode and any older ones
			$db->delete($glob['dbprefix']."CubeCart_SpamBot", "uniqueId = ".$db->mySQLSafe($ESC)." OR `time` < ".time()-3600);
		}
		$result[0]['SpamCode']	= strtoupper($result[0]['SpamCode']);
		return $result[0];
	} else {
		return false;
	}
}

## Create Spamcode
function createSpamCode($spamCode) {

	global $db, $glob, $cc_session;

	//$uniqueId = $cc_session->makeSessId();
	session_start();
	session_regenerate_id();
	$uniqueId = session_id();

	$data['uniqueId']	= $db->mySQLSafe($uniqueId);
	$data['time']		= $db->mySQLSafe(time());
	$data['spamCode']	= $db->mySQLSafe($spamCode);
	$data['userIp']		= $db->mySQLSafe(get_ip_address());

	## Insert into DB
	$insert = $db->insert($glob['dbprefix']."CubeCart_SpamBot", $data);
	return $uniqueId;
}

## Get spambot image
function imgSpambot($encodedSpamCode,$path = '') {
	global $config;

	if ($config['gdversion']>0) {
		$imgSpambot = "<img src=\"index.php?_g=cs&amp;_p=".urlencode("images/random/verifyGD.inc.php")."&amp;esc=".$encodedSpamCode."&amp;_a=verifyGD\" alt=\"\" title=\"\" />";
	} else {
		$imgSpambot = "";
		for ($i=1;$i<=5;++$i) {
			$imgSpambot .= "<img src=\"index.php?_g=cs&amp;_p=".urlencode("images/random/verifySTD.inc.php")."&amp;esc=".$encodedSpamCode."&amp;n=".$i."&amp;_a=verifyGD\" alt=\"\" title=\"\" />\r\n";
		}
	}
	return $imgSpambot;
}

//////////////////////////////////
// is the server a Win OS??!? Lets hope not...
////////
function win() {
	return (substr(PHP_OS, 0, 3) == 'WIN') ? true : false;
}

## large file downloads - thanks to php.net and contributors
function deliverFile($path) {
	ob_end_clean();

	if (!is_file($path) or connection_status()!=0) return false;

	header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header("Content-Type: application/octet-stream");
	//header("Content-Length: ".filesize($path));
	header("Content-Transfer-Encoding: binary");
	## IE 7 Fix
	header('Vary: User-Agent');

	if (($file = fopen($path, 'rb')) !== false) {
		while (!feof($file)) { // && !connection_status()) {
			echo fread($file, 8192);
  			flush();
		}
		fclose($file);
	}

	return (!connection_status() && !connection_aborted());
}

## Get language file - Version 2
function getLang($path, $setlang = false) {
	global $glob, $db, $config;
	ob_start();

	if($setlang){
		$langFolder = $setlang;
	} elseif (defined('LANG_FOLDER') && constant('LANG_FOLDER')) {
		$langFolder = LANG_FOLDER;
	} else {
		$langFolder = $config['defaultLang'];
	}

	if (!$langFolder) $langFolder = $setlang;

	if (!file_exists(CC_ROOT_DIR.CC_DS.'language'.CC_DS.$langFolder)) {
		$langFolder = $config['defaultLang'];
	}

	$path		= CC_ROOT_DIR.CC_DS.'language'.CC_DS.$langFolder.CC_DS.$path;
	$linuxPath	= str_replace(CC_ROOT_DIR, '', str_replace(CC_DS, '/', $path));
	$identifier	= explode('/language', $linuxPath);


	## Check the cache first
	$cacheName	= str_replace(array('/', '.inc.php'), array('.', ''), $identifier[1]);
	$cache		= new cache('lang'.$cacheName);
	$langCache	= $cache->readCache();
	if ($cache->cacheStatus) {
		if (empty($langCache)) {
			include $path;
			$langCache = $lang;
		}
	} else {
		if (isset($identifier[1])) {
			$query	= "SELECT langArray from ".$glob['dbprefix']."CubeCart_lang WHERE identifier = ".$db->mySQLSafe($identifier[1]);
			$result	= $db->select($query);
			if ($result) {
				$langCache = unserialize($result[0]['langArray']);
			} else {
				include $path;
				$langCache = $lang;
			}
		}
		$cache->writeCache($langCache);
	}
	if (is_array($GLOBALS['lang']) && is_array($langCache)) {
		$langCache = array_merge($GLOBALS['lang'], $langCache);
	}
	ob_end_clean();
	return $langCache;
}

## Split full name - version 2
function makeName($fullName) {
	$name = trim(strrev($fullName));
	$name = explode(' ', $name);
	$i = 3;
	foreach ($name as $value) {
		$output[$i--] = strrev($value);
	}
	if (!$output[1]) $output[1] = null;
	ksort($output);
	return $output;
}

## Get thumbnail path
function imgPath($masterImage, $thumb = false, $path = '') {
	// raw image path order is important
	$img = str_replace(array(
		CC_ROOT_DIR.CC_DS.'images'.CC_DS.'uploads'.CC_DS,
		$GLOBALS['storeURL'].'/images/uploads/',
		$GLOBALS['rootRel'].'images/uploads/',
		CC_ROOT_DIR.CC_DS.'cache'.CC_DS,
		'images'.CC_DS.'uploads'.CC_DS,
		'images/uploads/', ## Keeps windows servers happy
	), '', $masterImage);

	if ($thumb) {
		//$img = "thumbs/thumb_".str_replace('thumb_', '', basename($img));
		$img = "thumbs/thumb_".preg_replace('/^thumb_/', '//', basename($img), 1);
	}

	switch ($path) {
		case 'rel':
			$filepath = $GLOBALS['rootRel'].'images/uploads/'.str_replace('\\','/',$img);
			break;
		case 'root':
			$filepath = CC_ROOT_DIR.CC_DS.'images'.CC_DS.'uploads'.CC_DS.str_replace('/', CC_DS, $img);
			break;
		case 'url':
			$filepath = $GLOBALS['storeURL'].'/images/uploads/'.str_replace('\\','/',$img);
			break;
		case 'cacheRel':
			$filepath = $GLOBALS['rootRel'].'cache/'.str_replace('\\','/',$img);
			break;
		default:
			$filepath = $img;
	}
	return $filepath;
}

function starImg($i, $aveRating) {
	$aveRating = round($aveRating,1)-$i;
	if ($aveRating>=1) {
		return 1;
	} else if ($aveRating<1 && $aveRating>=0.5) {
		return 0.5;
	} else {
		return 0;
	}
}

function cc_print_array($array) {
	if (is_array($array) && count($array) > 0) {
		## if version is over 4.3.0
		if (version_compare(PHP_VERSION, '4.3.0', '<')) {
			ob_start();
			print_r($array);
			$output = ob_get_contents();
			ob_end_flush();
		} else {
			$output = print_r($array, 1);
		}
		return "<pre>".$output."</pre>";
	} else {
		return "No Data!";
	}
}

function buildCatTree(&$treeData, &$key, $cat_parent_id = 0, $level = 0) {
	global $glob, $db, $resultsForeign, $config;

	$emptyCat	= ($config['show_empty_cat']) ? '' : 'AND noProducts >= 1';
	$query = sprintf("SELECT cat_name, cat_id, noProducts, cat_father_id FROM %sCubeCart_category WHERE cat_father_id = '%d' AND hide = '0' AND (cat_desc != '##HIDDEN##' OR cat_desc IS NULL) %s ORDER BY priority, cat_father_id, cat_name ASC", $glob['dbprefix'], $cat_parent_id, $emptyCat);
	$results = $db->select($query);

	if ($results) {
		$level++;
		for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {
			if (is_array($resultsForeign)) {
				for ($k = 0, $maxk = count($resultsForeign); $k < $maxk; ++$k) {
					if ($resultsForeign[$k]['cat_id'] == $results[$i]['cat_id']) {
						$results[$i]['cat_name'] = $resultsForeign[$k]['cat_name'];
					}
				}
			} else {
				$results[$i]['cat_name'] = $results[$i]['cat_name'];
			}
			## Make an array of tree data this way always know what the next key value is and things become far easier
			$treeData[$key]['level']		= $level;
			$treeData[$key]['cat_name']		= validHTML($results[$i]['cat_name']);
			$treeData[$key]['cat_id']		= $results[$i]['cat_id'];
			$treeData[$key]['noProducts']	= $results[$i]['noProducts'];
			$treeData[$key]['cat_father_id']= $results[$i]['cat_father_id'];
			$key++;

			if ($config['cat_tree']) buildCatTree($treeData, $key, $results[$i]['cat_id'], $level);
		}
	}
	return $treeData;
}

## Detect possible spoofing URL's
function checkSpoof() {
	// Do not allow redirect to http(s):// ftp:// or //
	if (!preg_match('/^(http(s?)\:\/\/|ftp\:\/\/|\/\/)/i', $_GET['r'])) {
		httpredir($_GET['r']);
	} else {
		httpredir("index.php");
	}
}



function mkPath($in) {
	global $glob;
	$prefix = (substr($in ,0, 7) == "modules") ? '' : $glob['adminFolder'].CC_DS.'sources'.CC_DS;
	return str_replace('/', CC_DS, $prefix.$in.'.inc.php');
}

## Admin Permissions
function permission($section, $permission, $halt = false) {
	global $ccAdminData,$glob;
	// check if index exists and if not create it
	if (!isset($ccAdminData[$section][$permission])) {
		$ccAdminData[$section][$permission] = '';
	}
	$result = ($ccAdminData[$section][$permission] || $ccAdminData['isSuper']) ? true : false;

	if (!$result && $halt) httpredir($GLOBALS['rootRel'].$glob['adminFile']."?_g=401");
	return $result;
}

function writeConf($new = '', $path, $prevArray, $arrayName = 'config', $output = true, $backup = true, $returnConfig = false) {
	global $lang;

	if (!is_array($new)) $msg = "<p class='warnText'>".$lang['admin_common']['incs_error_editing']."</p>";

	if (count($new) < 1) {
		return '';
		exit;
	}

	## Add old config vars not in $new array
	if (is_array($prevArray)) {
		foreach ($prevArray as $key => $value) {
			if ($new[$key] != $prevArray[$key]) {
				$value = preg_replace("/\r/", '', $value);
				$newConfig[$key] = $value;
			}
		}
	}

	## Build new config vars from $new array
	foreach ($new as $key => $value) {
		$value = preg_replace("/\r/", '', $value);
		$newConfig[$key] = $value;
	}

	$content = "<?php\n";
	ksort($newConfig);
	foreach ($newConfig as $key => $value) {
		$value = str_replace(array("\\'","'"),"&#39;",$value);
		if (!get_magic_quotes_gpc()) {
			$value = addslashes($value);
		}
		$content .= "\$".$arrayName."['".$key."'] = '".trim($value)."';\n";
	}

	$content .= "?>";

	if ($returnConfig != false) {
		return $content;
	} else {
		@chmod($path, 0777);
		if ($backup) {
			@copy($path, $path.".bak");
			@chmod($path.".bak", 0644);
		}

		$fp = fopen($path, 'w+');

		if (($handle = @fopen($path, 'w+b')) !== false) {
			fwrite($handle, $content, strlen($content));
			fclose($handle);
			$msg = "<p class='infoText'>".$lang['admin_common']['incs_config_updated']."</p>";
			$returnVal = true;
		} else {
			$msg = "<p class='warnText'>".sprintf($lang['admin_common']['incs_cant_write'],$path)."</p>";
			$returnVal = false;
		}
		@chmod($path, 0644);
		return ($output) ? $msg : $returnVal;
	}
}

## Fetch config info
function fetchDbConfig($confName) {
	global $glob, $db;
	$cache		= new cache('config.'.$confName);
	$cacheData	= $cache->readCache();

	if (is_array($cacheData)) {
		return $cacheData;
	} else {
		$result = $db->select("SELECT array FROM ".$glob['dbprefix']."CubeCart_config WHERE name = ".$db->mySQLSafe($confName));
		if ($result) {
			$arrayOut = unserialize($result[0]['array']);
			foreach ($arrayOut as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $skey => $sval) {
						$arrayOut[$key][$skey] = stripslashes($sval);
					}
				} else {
					$arrayOut[$key] = stripslashes($value);
				}
			}
			return (is_array($arrayOut)) ? $arrayOut : false;
		}
		return false;
	}
}

##
function writeDbConf($new = '', $confName, $prevArray, $output = true) {
	global $lang, $db, $glob;
	if (!is_array($new)) $msg = sprintf('<p class="warnText">%s</p>', $lang['admin_common']['incs_error_editing']);

	if (count($new) < 1) {
		return '';
		exit;
	}

	## Add old config vars not in $new array
	if (is_array($prevArray)) {
		foreach ($prevArray as $key => $value) {
			if ($new[$key] != $prevArray[$key]) {
				$newConfig[$key] = $value;
			}
		}
	}

	## Build new config vars from $new array
	if (is_array($new)) {
		foreach ($new as $key => $value) {
			$newConfig[$key] = is_array($value) ? $value : trim($value);
		}
	}

	## serialise the array for DB storage
	$configText = addslashes(serialize($newConfig));

	## see if database config exists
	$result = $db->numrows('SELECT * FROM '.$glob['dbprefix'].'CubeCart_config WHERE `name` = '.$db->mySQLSafe($confName));
	$array['array'] = sprintf("'%s'", $configText);

	if ($result>0) {
		$store = $db->update($glob['dbprefix'].'CubeCart_config', $array, '`name` = '.$db->mySQLSafe($confName));
	} else {
		$array['name'] = $db->mySQLSafe($confName);
		$store = $db->insert($glob['dbprefix'].'CubeCart_config', $array);
	}

	if ($store) {
		$msg = "<p class='infoText'>".$lang['admin_common']['incs_db_config_updated']."</p>";
		$returnVal = true;
	} else {
		$msg = "<p class='warnText'>".sprintf($lang['admin_common']['incs_db_cant_write'], $confName)."</p>";
		$returnVal = false;
	}

	return ($output) ? $msg : $returnVal;
}

function jsGeoLocation($countryVar, $countyVar, $nullText){
	global $config, $db, $lang, $glob;

	## Get ISO counties
	$isoCounties = $db->select("SELECT * FROM  ".$glob['dbprefix']."CubeCart_iso_counties");

$jsScript = <<<JSCRIPT
	/* GENERATED JAVASCRIPT */
	function resetZoneSelected(theForm) {
		if (theForm.state.value != '') {
			theForm.{$countyVar}.selectedIndex = '0';
			if (theForm.{$countyVar}.options.length > 0) {
				theForm.state.value = '-- {$lang['admin_common']['incs_select_above']} --';
			}
		}
	}

	function updateCounty(theForm) {
		var NumState = theForm.{$countyVar}.options.length;
		var CurrentCountry;

		while (NumState > 0) {
			NumState--;
			theForm.{$countyVar}.options[NumState] = null;
		}
		CurrentCountry = theForm.{$countryVar}.options[theForm.{$countryVar}.selectedIndex].value;

JSCRIPT;

	for ($i = 0, $maxi = count($isoCounties); $i < $maxi; ++$i){
		if ($i==0) {
			$optionKey = 0;
			$jsScript .= "if (CurrentCountry == \"".$isoCounties[$i]['countryId']."\") {";
			$jsScript .= "\r\ntheForm.".$countyVar.".options[".$optionKey."] = new Option(\"".$nullText."\", \"\");\r\n";
		} else if ($oldCountryId != $isoCounties[$i]['countryId']) {
			$optionKey = 0;
			$jsScript .= "\r\n} else if (CurrentCountry == \"".$isoCounties[$i]['countryId']."\") {\r\n";
			$jsScript .= "\r\ntheForm.".$countyVar.".options[".$optionKey."] = new Option(\"".$nullText."\", \"\");\r\n";
		}
		++$optionKey;
		$jsScript .= "\r\ntheForm.".$countyVar.".options[".$optionKey."] = new Option(\"".$isoCounties[$i]['name']."\", \"".$isoCounties[$i]['id']."\");\r\n";
		$oldCountryId = $isoCounties[$i]['countryId'];
	}

	$jsScript .= "\r\n } else { \r\n theForm.".$countyVar.".options[0] = new Option(\"".$nullText."\", \"\"); \r\n } \r\n } \r\n //-->";
	return $jsScript;
}

## log and display message
function msg($msg, $log = true) {
	global $glob, $db, $ccAdminData;
	if ($log) {
		$logArray = array(
			'user' => $db->mySQLSafe($ccAdminData['username']),
			'desc' => $db->mySQLSafe(strip_tags($msg)),
			'time' => $db->mySQLSafe(time()),
			'ipAddress' => $db->mySQLSafe(get_ip_address())
		);
		$db->insert($glob['dbprefix'].'CubeCart_admin_log',$logArray);
	}
	return stripslashes($msg);
}

function enableSSl() {
	return (isset($_COOKIE['ccSSL']) || isset($_GET['ccSSL']) || isset($_POST['ccSSL'])) ? true : false;
}

function moduleParts($in) {
	$parts = explode('/',$in);
	if (is_array($parts)) $noParts = count($parts);
	$out[0] = $parts[$noParts-3];
	$out[1] = $parts[$noParts-2];
	return $out;
}

function formArray($mastArray, $i=0,$mastkey='') {
	if(is_array($mastArray)) {
		foreach($mastArray as $key => $value) {
			// get master key
			if(is_array($value)) {
				$mastkey = $key;
				$formArray = formArray($value,$i,$key);
				break;
			} else {
				$formArray[$i]['key'] = $key;
				$formArray[$i]['flatkey'] = '['.$mastkey.']['.$key.']';
				$formArray[$i]['flatvalue'] = stripslashes($value);
				++$i;
			}
		}
	}
	return $formArray;
}

function getTax(&$price, $addTax = false, $vatRate = 17.5) {
	if ($vatRate) {
		$vatRate = $vatRate/100;
		switch ($addTax) {
			case true:
				## Tax exclusive price
				## Add VAT to the price, nice and easy
				$vatTotal    = ($price*($vatRate+1)) - $price;
				break;
			case false:
				## Tax inclusive price
				## Calculate how much VAT a given price contains, and change the price to the sans-tax value
				$vatTotal    = $price - ($price/($vatRate+1));
				$price		-= $vatTotal;;
			break;
		}
		//$price = sprintf('%.2f',$price);
		return $vatTotal;
	}
	//$price = sprintf('%.2f',$price);
	return sprintf('%.2f',0);
}


if (!function_exists('getallheaders')) {
	function getallheaders() {
		foreach($_SERVER as $name => $value)
			if(substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		return $headers;
	}
}

#### Workaround for a documented PHP4 bug - With thanks to the folks on PHP.net documentation
## Will not be required for PHP5 versions

function html_entity_decode_utf8($string, $mode = ENT_COMPAT, $encoding = 'UTF-8') {
	if (version_compare(PHP_VERSION, 5, '>=')) {
		return html_entity_decode($string, $mode, $encoding);
	} else {
		static $trans_tbl;
		## replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'code2utf(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'code2utf(\\1)', $string);
		## replace literal entities
		if (!isset($trans_tbl)) {
			$trans_tbl = array();
			foreach (get_html_translation_table(HTML_ENTITIES) as $val => $key) {
				$trans_tbl[$key] = utf8_encode($val);
			}
		}
		return strtr($string, $trans_tbl);
	}
}

function code2utf($number) {
	if ($number < 0)	return false;
	if ($number < 128)	return chr($number);
	## Removing / Replacing Windows Illegals Characters
	if ($number < 160) {
		if ($number==128)		$number=8364;
		elseif ($number==129)	$number=160;
		elseif ($number==130)	$number=8218;
		elseif ($number==131)	$number=402;
		elseif ($number==132)	$number=8222;
		elseif ($number==133)	$number=8230;
		elseif ($number==134)	$number=8224;
		elseif ($number==135)	$number=8225;
		elseif ($number==136)	$number=710;
		elseif ($number==137)	$number=8240;
		elseif ($number==138)	$number=352;
		elseif ($number==139)	$number=8249;
		elseif ($number==140)	$number=338;
		elseif ($number==141)	$number=160;
		elseif ($number==142)	$number=381;
		elseif ($number==143)	$number=160;
		elseif ($number==144)	$number=160;
		elseif ($number==145)	$number=8216;
		elseif ($number==146)	$number=8217;
		elseif ($number==147)	$number=8220;
		elseif ($number==148)	$number=8221;
		elseif ($number==149)	$number=8226;
		elseif ($number==150)	$number=8211;
		elseif ($number==151)	$number=8212;
		elseif ($number==152)	$number=732;
		elseif ($number==153)	$number=8482;
		elseif ($number==154)	$number=353;
		elseif ($number==155)	$number=8250;
		elseif ($number==156)	$number=339;
		elseif ($number==157)	$number=160;
		elseif ($number==158)	$number=382;
		elseif ($number==159)	$number=376;
	}
	if ($number < 2048)		return chr(($number >> 6) + 192) . chr(($number & 63) + 128);
	if ($number < 65536)	return chr(($number >> 12) + 224) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
	if ($number < 2097152)	return chr(($number >> 18) + 240) . chr((($number >> 12) & 63) + 128) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
	return false;
}

### Category list caching code
function showCatList($thisCat = null, $rebuild = false) {
	$filename	= CC_ROOT_DIR.CC_DS.'includes'.CC_DS.'extra'.CC_DS.'admin_cat_cache.txt';

	## Do we need to delete this file?
	if ($rebuild) unlink($filename);

	if (!file_exists($filename)) buildCatList();

	$data = file_get_contents($filename);
	if (!is_null($thisCat) && is_numeric($thisCat)) {
		$data = str_replace('value="'.$thisCat.'"', 'value="'.$thisCat.'" selected="selected"', $data);
	}
	return $data;
}

function buildCatList() {
	$filename	= CC_ROOT_DIR.CC_DS.'includes'.CC_DS.'extra'.CC_DS.'admin_cat_cache.txt';
	$data 		= '';

	buildCatSegment(0, $data);

	$fp	= fopen($filename, 'wb+');
	fwrite($fp, $data, strlen($data));
	fclose($fp);
}

function buildCatSegment($thisCat, &$data) {
	global $db, $glob;

	$sql = sprintf("SELECT cat_name, cat_father_id, cat_id FROM %sCubeCart_category WHERE `cat_father_id` = '%d' ORDER BY %s ASC", $glob['dbprefix'], $thisCat, 'priority, cat_id, cat_name');
	$cats	= $db->select($sql);

	if (is_array($cats) && !empty($cats)) {
		foreach ($cats as $cat) {
			$data .= sprintf('<option value="%d">%s</option>'."\n", $cat['cat_id'], getCatDir($cat['cat_name'], $cat['cat_father_id'], $cat['cat_id'], false));
			buildCatSegment($cat['cat_id'], $data);
		}
	}
}


function custom_recaptcha_get_html ($pubkey, $error = null, $use_ssl = false) {
	## Deprecated
	return false;
}

## generate unique product code
function unique_product_code($product_name,$cat_id) {
	global $db, $glob;

	$chars = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N',
			'O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3',
			'4','5','6','7','8','9','0');
	$max_chars = count($chars) - 1;
	srand((double)microtime()*1000000);
	for ($i = 0; $i < 5; ++$i) {
		$randChars = ($i == 0) ? $chars[rand(0, $max_chars)] : $randChars . $chars[rand(0, $max_chars)];
	}

	$product_code = strtoupper(substr($product_name,0,3)).$randChars.$cat_id;
	## Check its not used already
	$sql	= sprintf("SELECT `productId` FROM `%sCubeCart_inventory` WHERE `productCode` = '%s'", $glob['dbprefix'], $product_code);
	$result	= $db->select($sql);
	if($result) {
		unique_product_code($product_name, $cat_id);
	} else {
		return $product_code;
	}
}

function fix_export_string($string) {
	$string = str_replace(array("\r","\n","\0","\x0B"), '', $string);
	// RFC 4180 double quotes are escaped with double quote before it
	$string = str_replace(array('"'), '""', $string);
	return $string;
}
?>