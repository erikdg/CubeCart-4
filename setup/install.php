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
|	install.php
|   ========================================
|	Installation Script	
+--------------------------------------------------------------------------
*/

require_once "../ini.inc.php";
session_start();
require_once "..".CC_DS."includes".CC_DS."functions.inc.php";
//require_once "..".CC_DS."admin".CC_DS."includes".CC_DS."functions.inc.php";

if (!isset($_GET['step'])) { $_GET['step'] = 1; }

if(!isset($_GET['l'])) {
$_GET['l'] = "en";
}


$langFolder = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['l']);
define('LANG_FOLDER', $langFolder);

require_once "..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."common.inc.php";
require_once "..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."setup.inc.php";


# We're geeks, and we're proud of it!
# But so are you, if you've come poking around in here ;)

$companyNameArray = array(
	'Applied Cryogenics',					# Futurama
	'Aperture Sciences',					# Portal
	'Bad Wolf Corporation',					# Doctor Who - Series 1 "Bad Wolf"/"The Parting of the Ways"
	'Blue Sun Corporation',					# Firefly
	'Colby Enterprises',					# Dynasty
	'CompuGlobalHyperMegaNet',				# Simpsons
	'Cyberdyne Systems',					# The Terminator
	'Cybus Industries',						# Doctor Who - Series 2 "Rise of the Cybermen"/"The Age of Steel"
	'Dervish and Banges',					# Harry Potter
	'ENCOM',								# TRON
	'FrobozzCo International',				# Zork
	'Globotech Industries',					# Small Soldiers
	'Hanso Foundation',						# Lost
	'Input, Inc.',							# Short Circuit (Thanks to Brivtech)
	'Jupiter Mining Corporation',			# Red Dwarf
	'Kaiba Corporation',					# Yu-Gi-Oh!
	'LuthorCorp',							# Smallville
	'Magpie Electricals',					# Doctor Who - Series 2 "The Idiot's Lantern"
	'Megadodo Publications',				# The Hitchiker's Guide to the Galaxy
	'Moms Friendly Robot Company',			# Futurama
	'Nakatomi Trading',						# Die Hard & Die Hard 2
	'Oceanic Airways',						# Lost
	'Omni Consumer Products',				# Robocop
	'Planet Express',						# Futurama
	'Powell Motors',						# The Simpsons
	'Primatech Paper Company',				# Heroes
	'Quest Aerospace',						# Spiderman (Rival of Oscorp)
	'Rekall, Inc',							# Total Recall
	'Rentaghost',							# Rentaghost, funnily enough...
	'Sparrow and Nightingale',				# Doctor Who - Series 3 "Blink"
	'The Androids Dungeon',					# The Simpsons
	'The Magic Box',						# Buffy the Vampire Slayer
	'Tyrell Corporation',					# Blade Runner
	'Universal Export',						# James Bond (Front for MI6)
	'Virtucon',								# Austin Powers: International Man of Mystery (Thanks to Brivtech)
	'Wayne Enterprises',					# Batman
	'Weyland-Yutani',						# Alien
	'Wolfram and Hart',						# Angel
	'Xanatos Enterprises',					# Gargoyles (Thanks to Brivtech)
	'Yoyodyne Propulsion Systems',			# The Adventures of Buckaroo Banzai Across the 8th Dimension (Many thanks to Kristen from padlockoutlet.com)
	'Zorg Corporation',						# The Fifth Element
	
	## We made some up, based on pop culture
	'MacGuffin Enterprises',				# Search Wikipedia for MacGuffin
	"Benjamin Barker's Shaving Supplies",	# Sweeney Todd
	"Mrs Lovett's Pie Shop",				# Sweeney Todd
	
	# And an anagram for good luck...
	'Mastrad',								# Yes, AMSTRAD. Please don't sue us Sir Alan...
);

## Let's pseudo-randomly pick a company name from the list
$companyName = $companyNameArray[mt_rand(0, count($companyNameArray)-1)];

function recPass() {
	$chars = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J", "k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T", "u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9","0");
	$max_chars = count($chars) - 1;
	srand((double)microtime()*1000000);
	for ($i = 0; $i < 8; $i++)	{
		$newPass = ($i == 0) ? $chars[rand(0, $max_chars)] : $newPass . $chars[rand(0, $max_chars)];
	}
	return $newPass;
}
function randomUser() {
	$alphabet = array("ALPHA","NOVEMBER","BRAVO","OSCAR","CHARLIE","PAPA","DELTA","QUEBEC","ECHO","ROMEO","FOXTROT","SIERRA","GOLF","TANGO","HOTEL","UNIFORM","INDIA","VICTOR","JULIET","WHISKY","KILO","X-RAY","LIMA","YANKEE","MIKE","ZULU");
	$max_chars = count($alphabet) - 1;
	srand((double)microtime()*1000);
	for ($i = 0; $i < 1; $i++) {
		$user = ($i == 0) ? $alphabet[rand(0, $max_chars)] : $user . $chars[rand(0, $max_chars)];
	}
	$randNo = rand(0, 1);
	if ($randNo == 1) {
		$user = strtolower($user);
	} 
	return $user.rand(10, 99);
}


function checkFilePerms($file) {
	global $lang;
	$filePath = '..'.CC_DS.$file;
	if (!file_exists($filepath)) {
		echo "<div class='redTxt' style='text-align: center'>".$lang['setup']['file_missing']."</div>";
		return false;
	} else if (!cc_is_writable($filepath)) {
		echo "<div class='redTxt' style='text-align: center'>".$lang['setup']['read_only']."</div>";
		return false;
	} else {
		echo "<div class='greenTxt' style='text-align: center'>".$lang['setup']['writable']."</div>";
		return true;
	}
}


$stageName = "";
if ($_GET['step'] == 2) {
	$stageName = $lang['setup']['stage2Name'];
	## Attempt auto chmod
	@chmod("../images/uploads/" ,0777);
	@chmod("../images/uploads/thumbs/", 0777);
	
	#@chmod('../includes/', 0777);
	if (!file_exists('../includes/global.inc.php')) { // && !ini_get('safe_mode')) {
		@chmod("../includes/global.inc.php-dist", 0777);
		@copy('../includes/global.inc.php-dist', '../includes/global.inc.php');
	}
	@chmod("../includes/global.inc.php", 0777);
	
	@chmod("../includes/extra/", 0777);
	@chmod("../cache/", 0777);
	
	if (!isset($_GET['skip'])) {
		if (!isset($_POST['agree'])) {
			$stepBack = 1;
			$error = $lang['setup']['stage1Error'];
		} else {
			$noRepeat = TRUE;
		}
	}

} else if ($_GET['step'] == 3) {
	
	$stageName = $lang['setup']['stage3Name'];
	$onclick = "onclick=\"YY_checkform('install','dbhost','#q','0','".$lang['setup']['enterDBHostname']."','dbname','#q','0','".$lang['setup']['enterDBName']."','dbuser','#q','0','".$lang['setup']['enterDBUsername']."','username','#q','0','".$lang['setup']['enteradminUsername']."','pass','#q','0','".$lang['setup']['enteradminPassword']."','pass_conf','#pass','6','".$lang['setup']['passwordMatch']."','email','S','2','".$lang['setup']['enterValidEmail']."','fullName','#q','0','".$lang['setup']['enterFullname']."','dbpass','#dbpassconfirm','6','".$lang['setup']['dbPasswordMatch']."');return document.returnValue\"";
	
} else if ($_GET['step'] == 4 && !isset($_GET['skip'])) {
	
	require_once "..".CC_DS."includes".CC_DS."global.inc.php";
	
	if ($_POST['subscribe']) {
		## subscribe to mailing list
		$URL = "http://www.cubecart.com/index.php?subscribeEmail=".trim($_POST['email'])."&source=install";
		$c = @curl_init();
		@curl_setopt($c, CURLOPT_MUTE, true);
		@curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($c, CURLOPT_URL, $URL);
		@curl_exec($c);
		@curl_close($c);
    }

	if($_POST['installCountry']=="US"){

	$config['defaultCurrency'] = 'USD';
	$config['siteCountry'] = '226';
	$config['siteCounty'] = '57';
	$config['storeAddress'] = $companyName.',
Street,
City,
State,
USA
12345';
	$config['taxCountry'] = '226';
	$config['taxCounty'] = '57';
	$config['weightUnit'] = 'Lb';
	
	} else if ($_POST['installCountry']=="UK"){
	
	$config['defaultCurrency'] = 'GBP';
	$config['siteCountry'] = '225';
	$config['siteCounty'] = '278';
	$config['storeAddress'] = $companyName.',
Street,
Town,
County,
UNITED KINGDOM
AB12 3CD';
	$config['taxCountry'] = '225';
	$config['taxCounty'] = '';
	$config['weightUnit'] = 'Kg';
	
	} else if ($_POST['installCountry']=="CA"){
	
	$config['defaultCurrency'] = 'CAD';
	$config['siteCountry'] = '38';
	$config['siteCounty'] = '76';
	$config['storeAddress'] = $companyName.',
Street,
City,
State,
CANADA 
A1B 2C3';
	$config['taxCountry'] = '38';
	$config['taxCounty'] = '';
	$config['weightUnit'] = 'Kg';
	
	} else if ($_POST['installCountry']=="AU"){
	
	$config['defaultCurrency'] = 'AUD';
	$config['siteCountry'] = '13';
	$config['siteCounty'] = '563';
	$config['storeAddress'] = $companyName.',
Street,
City,
State,
AUSTRALIA 
A1B 2C3';
	$config['taxCountry'] = '13';
	$config['taxCounty'] = '';
	$config['weightUnit'] = 'Kg';
	
	} elseif($_POST['installCountry']=="EU"){
	
	$config['defaultCurrency'] = 'EUR';
	$config['siteCountry'] = '160';
	$config['siteCounty'] = '';
	$config['storeAddress'] = $companyName.',
Street,
Town,
County,
NORWAY
1234';
	$config['taxCountry'] = '160';
	$config['taxCounty'] = '';
	$config['weightUnit'] = 'Kg';
	
	}
	
	$config['skinDir'] = 'KitaBlue';
	
	#$config['ob_gzhandler'] = '0';
	$config['dateFormat'] = 'l jS F Y';
	$config['defaultLang'] = $_GET['l'];
	$config['dirSymbol'] = '/';
	#$config['displaycatRows'] = '2';
	$config['dnLoadExpire'] = '172800';
	$config['dnLoadTimes'] = '3';
	$config['gdmaxImgSize'] = '390';
	$config['gdquality'] = '80';
	$config['gdthumbSize'] = '75';
	$config['floodControl'] = 'recaptcha';
	#$config['gdversion'] = $_POST['gdversion'];
	$config['mailMethod'] = 'mail';
	$config['masterEmail'] = trim($_POST['email']);
	$config['masterName'] = $companyName;
	$config['maxImageUploadSize'] = '524288';
	$config['metaDescription'] = 'This is the meta description.';
	$config['metaKeyWords'] = 'keyword1, keyword2, keyword3.';
	$config['noPopularBoxItems'] = '10';
	$config['noSaleBoxItems'] = '10';
	$config['outofstockPurchase'] = '1';
	$config['priceIncTax'] = '0';
	$config['productPages'] = '10';
	$config['productPrecis'] = '120';
	$config['rootRel_SSL'] = '';
	$config['saleMode'] = true;
	$config['salePercentOff'] = '20';
	$config['shipAddressLock'] = false;
	$config['siteTitle'] = $companyName;
	$config['sqlSessionExpiry'] = '172800';
	$config['ssl'] = '0';
	$config['stockLevel'] = '1';
	$config['storeName'] = $companyName;
	$config['storeURL_SSL'] = '';
	$config['timeFormat'] = '%b %d %Y, %H:%M %p';
	$config['timeOffset'] = '0';
	$config['installTime'] = time();
	$config['offLine'] = '0';
	## fix for bug #147
	$config['offLineContent'] = base64_encode($lang['setup']['storeOfflineText']);
	$config['offLineAllowAdmin'] = true;
	$config['showLatestProds'] = true;
	$config['noLatestProds'] = '3';
	$config['noRelatedProds'] = '3';
	
	## Detect GD version
	$config['gdversion']	= detectGD();
	
	## Added in 4.0.0 
	$config['cat_tree'] = '1';
	$config['hide_prices'] = '0';
	$config['pop_products_source'] = '0';
	$config['cache'] = '1';
	$config['show_empty_cat'] = 1;
	$config['disable_alert_email']=0;
	$config['latestNewsRRS'] = 'http://forums.cubecart.com/index.php?act=rssout&id=1';
	$config['richTextEditor'] = true;
	$config['rteHeight'] = '350';
	$config['rteHeightUnit'] = '';
	$config['add_to_basket_act'] = '0';
	
	$config['stock_change_time'] = '0';
	$config['stock_replace_time'][1] = '1';
	$config['stock_replace_time'][2] = '1';
	$config['stock_replace_time'][3] = '1';
	$config['stock_replace_time'][5] = '1';
	$config['stock_replace_time'][6] = '1';
	
	$config['orderExpire'] = '0'; // now disabled by default so not to cause v3-v4 upgrade problems
	
	$config['stock_warn_type'] = '0';
	$config['stock_warn_level'] = '5';
	$config['changeskin'] = '0';
	$config['priceTaxDelInv'] = '0';
	$config['currecyAuto'] = '0';
	$config['proxy'] = '0';
	$config['proxyHost'] = '';
	$config['proxyPort'] = '';
	$config['sef'] = '0';
	$config['seftags'] = '0';
	$config['sefprodnamefirst'] = '0';
	
	if ($config['gdversion'] !== false) {
		$gdArray = @gd_info();
		$config['gdGifSupport']	= ($gdArray['GIF Create Support']) ? true : false;
	} else {
		$config['gdGifSupport'] = false;
	}
	
	$glob['dbdatabase'] 	= trim($_POST['dbname']);
	$glob['dbhost'] 		= trim($_POST['dbhost']);
	$glob['dbpassword'] 	= trim($_POST['dbpass']);
	$glob['dbprefix'] 		= trim($_POST['dbprefix']);
	$glob['dbusername'] 	= trim($_POST['dbuser']);
	$glob['installed'] 		= true;
	$glob['rootRel'] 		= trim($_POST['rootRel']);
	$glob['storeURL'] 		= trim($_POST['storeURL']);
	$glob['adminFolder'] 	= "admin";
	$glob['adminFile'] 		= 'admin.php';
	$glob['license_key']	= preg_replace('#[^a-z0-9\-]+#i', '', $_POST['license_key']);
	
	if ($_POST['encoder'] == 'auto') {
		$glob['encoder']	= (has_ioncube_loader()) ? 'ioncube' : 'zend';
	} else {
		$glob['encoder']	= $_POST['encoder'];
	}
	
	$_SESSION['license_key'] 	= $glob['license_key'];
	$_SESSION['dbname'] 		= trim($_POST['dbname']);
	$_SESSION['dbhost'] 		= trim($_POST['dbhost']);
	$_SESSION['dbprefix'] 		= trim($_POST['dbprefix']);
	$_SESSION['dbuser'] 		= trim($_POST['dbuser']);
	$_SESSION['username'] 		= trim($_POST['username']);
	$_SESSION['fullName'] 		= trim($_POST['fullName']);
	$_SESSION['email'] 			= trim($_POST['email']);
	$_SESSION['installCountry'] = trim($_POST['installCountry']);
	$_SESSION['encoder'] 		= trim($_POST['encoder']);
	$_SESSION['subscribe'] 		= $_POST['subscribe'] ? true : false;
		
	if (writeConf($glob, CC_ROOT_DIR.CC_DS."includes".CC_DS."global.inc.php", $glob, 'glob', false)) {
		## Config written - lets install the database
		
		@chmod('../includes/global.inc.php', 0644);
		
		require_once "..".CC_DS."includes".CC_DS."global.inc.php";
		require_once "..".CC_DS."classes".CC_DS."db".CC_DS."db.php";
	
		$db = new db();
	
		## insert database
		$dbprefix	= $glob['dbprefix'];
		$dbname		= $glob['dbname'];
		
		## drop tables if they exist and they have specified to
		if (isset($_POST['dropTables']) && $_POST['dropTables'] == 1) {
			$sqlfile = "db".CC_DS."install-main-drop.sql";
			include "sqlinstaller.php";
		}
		
		## set correct DB charset
		$db->misc("ALTER DATABASE `".$_POST['dbname']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
		
		## install main sql file
		$sqlfile = "db".CC_DS."install-main.sql";
		include "sqlinstaller.php";
		
		## install locale settings
		$sqlfile = "db".CC_DS."install-".$_POST['installCountry'].".sql";
		include "sqlinstaller.php";
		
		##insert main admin user
		$salt = randomPass(6);
		$db->misc("INSERT INTO `".$glob['dbprefix']."CubeCart_admin_users` (`name`, `username`, `password`, `salt`, `email`, `isSuper`, `notes`) VALUES ('".$_POST['fullName']."', '".$_POST['username']."', '".md5(md5($salt).md5($_POST['pass']))."', '".$salt."', '".$_POST['email']."', '1', 'This user was setup during installation.');");
		$db->misc("INSERT INTO `".$glob['dbprefix']."CubeCart_history` (`version` ,`time`) VALUES ('".$ini['ver']."', '".time()."');");
	
		if (writeDbConf($config, "config", $config, false)) {
			$errorWriting = false;
		} else {
			$errorWriting = true;
			$stepBack = 3;
		}
		
		$noRepeat = true;
	} else {
		$errorWriting = true;
		if (ini_get('safe_mode')) {
			$safemode = true;
			$outputConfig = writeConf($glob, CC_ROOT_DIR.CC_DS."includes".CC_DS."global.inc.php", $glob, 'glob', false, false, true);
		} else {
			$error = $lang['setup']['configWriteError'];
			$errorWriting = true;
			$stepBack = 3;
		}
	}

	$stageName = $lang['setup']['stage4Name'];

} else if ($_GET['step'] == 5) {
	$stageName = $lang['setup']['stage5Name'];
	require("..".CC_DS."includes".CC_DS."global.inc.php");
	## Send anonymous server stats to CubeCart HQ
	if (function_exists('curl_init')) {
		preg_match('#^(\d+\.\d+\.\d+)#', PHP_VERSION, $php_version);
		preg_match('#^(\d+\.\d+\.\d+)#', mysql_get_client_info(), $sql_version);
		$request	= array(
			'CC_Version'	=> $ini['ver'],
			'IP_Address'	=> get_ip_address(),	## We ONLY use this to get the user's country with GeoIP, then discard it
			'Server'		=> urlencode($_SERVER['SERVER_SOFTWARE']),
			'MySQL'			=> $sql_version[1],
			'PHP'			=> $php_version[1],
			'PHP_OS'		=> PHP_OS,
			'PHP_SAPI'		=> php_sapi_name(),
			'PHP_EXT'		=> array(
				'Zend'			=> (int)has_zend_optimizer(),
				'Ioncube'		=> (int)has_ioncube_loader(),
			),
		);
		$php_exts	= array('APC','eAccelerator','FileInfo','Hash','mCrypt','mysqli','memcache','XCache','XDebug');
		foreach ($php_exts as $ext) {
			$request['PHP_EXT'][$ext]	= (int)extension_loaded($ext);
		}
		$stat	= curl_init('http://cp.cubecart.com/licence/statistics');
		$curl_options	= array(
			CURLOPT_HEADER			=> false,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_POST			=> true,
			CURLOPT_POSTFIELDS		=> http_build_query($request, null, '&')
		);
		## curl_setopt_array work around
		if (!function_exists('curl_setopt_array')) {
		   function curl_setopt_array(&$ch, $curl_options)
		   {
		       foreach ($curl_options as $option => $value) {
		           if (!curl_setopt($ch, $option, $value)) {
		               return false;
		           } 
		       }
		       return true;
		   }
		} else {
			curl_setopt_array($stat, $curl_options);
		}
		curl_exec($stat);
		curl_close($stat);
		unset($curl_options, $request, $stat);
	}
} else {

	$stageName = $lang['setup']['stage1Name'];
	$onclick = "onclick=\"YY_checkform('install','agree','#q','1','".$lang['setup']['agreeToLicense']."');return document.returnValue\"";

}

$noInstallSteps = 5;
include("includes".CC_DS."header.inc.php");
?>
<div class="mainPad">
<div class="subTitle"><?php echo sprintf($lang['setup']['installation'],$ini['ver']); ?></div>
<?php if(isset($error)){?><div class="errorBar"><?php echo $error; ?></div><?php } ?>
<div> 
<table border="0" width="100%" cellpadding="3" cellspacing="1">
  <tr>
  	<td><?php echo sprintf($lang['setup']['stepStatus'],$_GET['step'],$noInstallSteps);?>
	</td>
    <td height="13" width="13" <?php if($_GET['step']>0) echo "class='progressOn'"; else echo "class='progressOff'"; ?>>1</td>
    <td height="13" width="13" <?php if($_GET['step']>1) echo "class='progressOn'"; else echo "class='progressOff'"; ?>>2</td>
    <td height="13" width="13" <?php if($_GET['step']>2) echo "class='progressOn'"; else echo "class='progressOff'"; ?>>3</td>
    <td height="13" width="13" <?php if($_GET['step']>3) echo "class='progressOn'"; else echo "class='progressOff'";?>>4</td>
    <td height="13" width="13" <?php if($_GET['step']>4) echo "class='progressOn'"; else echo "class='progressOff'";?>>5</td>
  </tr>
</table>
<br />
<form name="install" action="install.php?step=<?php echo $_GET['step']+1; ?>&amp;l=<?php echo $langFolder; ?>" method="post" enctype="multipart/form-data">
<table border="0" width="100%" cellpadding="3" cellspacing="1" class="formTable">
  <tr>
    <td colspan="4" class="blueHead"><?php echo $lang['setup']['step']; ?> <?php echo $_GET['step'];?> - <?php echo $stageName; ?></td>
  </tr>
  <?php
  switch($_GET['step']){
  case 1;
  ?>
   <tr>
    <td colspan="4">
	<div class="license"><?php require("..".CC_DS."docs".CC_DS."license.htm"); ?></div>	</td>
    </tr>
	<tr>
    <td colspan="4" style="color: red;">
	<?php echo $lang['setup']['iagreetoLic']; ?> <input name="agree" type="checkbox" value="1" />	</td>
    </tr>
  <?php
  break;
  case 2;
  ?>
  <tr>
    <td colspan="4"><?php echo $lang['setup']['checkFilePerms'];?></td>
    </tr>
  <tr>
    <td><strong><?php echo $lang['setup']['fileFolder'];?></strong></td>
    <td colspan="2" align="center"><strong><?php echo $lang['setup']['currentPermission'];?></strong></td>
    <td rowspan="5" align="center"><a href="javascript:;"><img src="images/helpIcon.gif" alt="<?php echo $lang['setup']['help']; ?>" width="30" height="28" border="0" title="" onclick="openBrWindow('help/filePerms.php?l=<?php echo $langFolder; ?>','','width=500,height=600,scrollbars=1')" /></a></td>
  </tr>
  <tr>
    <td>images/uploads/</td>
    <td colspan="2" align="center">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."images".CC_DS."uploads".CC_DS)), -4);
	if(cc_is_writable("..".CC_DS."images".CC_DS."uploads".CC_DS)) {
		echo "<span class='greenTxt'>".$lang['setup']['writable']."</span>";
	} else {
		echo "<span class='redTxt'>".$lang['setup']['read_only']."</span>";
		$error = TRUE;
		$stepBack = 2;
	}
	?></td>
    </tr>
  <tr>
    <td>images/uploads/thumbs/</td>
    <td colspan="2" align="center">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."images".CC_DS."uploads".CC_DS."thumbs")), -4);
	
	if(cc_is_writable("..".CC_DS."images".CC_DS."uploads".CC_DS."thumbs")) {

		echo "<span class='greenTxt'>".$lang['setup']['writable']."</span>";
		
	} else {
		echo "<span class='redTxt'>".$lang['setup']['read_only']."</span>";
		$error = TRUE;
		$stepBack = 2;
	}
	?>	</td>
  </tr>
  <tr>
    <td>images/logos/</td>
    <td colspan="2" align="center">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."images".CC_DS."uploads".CC_DS."thumbs")), -4);
	
	if(cc_is_writable("..".CC_DS."images".CC_DS."logos")) {

		echo "<span class='greenTxt'>".$lang['setup']['writable']."</span>";
		
	} else {
		echo "<span class='redTxt'>".$lang['setup']['read_only']."</span>";
		$error = true;
		$stepBack = 2;
	}
	?>	</td>
  </tr>
  <tr>
    <td>includes/global.inc.php</td>
    <td colspan="2">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."includes".CC_DS."global.inc.php")), -4);
	$globalFilepath = "..".CC_DS."includes".CC_DS."global.inc.php";
	
	// try to write global.inc.php
	// if it couldn't write it ask user to
	if (!file_exists($globalFilepath)) {
		echo "<div class='redTxt' style='text-align: center'>".$lang['setup']['global_missing']."</div>";
		$error = true;
		$stepBack = 2;
		
	} else if (cc_is_writable($globalFilepath)) {
		echo "<div class='greenTxt' style='text-align: center'>".$lang['setup']['writable']."</div>";
		
	} else {
		echo "<div class='redTxt' style='text-align: center'>".$lang['setup']['read_only']."</div>";
		$error = true;
		$stepBack = 2;
	}
	?>  </td>
  </tr>
  <tr>
    <td>includes/extra/</td>
    <td colspan="2" align="center">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."images".CC_DS."uploads".CC_DS."thumbs")), -4);
	
	if (cc_is_writable("..".CC_DS."includes".CC_DS."extra")) {

		echo "<span class='greenTxt'>".$lang['setup']['writable']."</span>";
		
	} else {
		echo "<span class='redTxt'>".$lang['setup']['read_only']."</span>";
		$error = true;
		$stepBack = 2;
	}
	?>	</td>
  </tr>
  <tr>
    <td>cache/</td>
    <td colspan="2" align="center">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."cache")), -4);
	
	if (cc_is_writable("..".CC_DS."cache")) {
		echo "<span class='greenTxt'>".$lang['setup']['writable']."</span>";
		
	} else {
		echo "<span class='redTxt'>".$lang['setup']['read_only']."</span>";
		$error = true;
		$stepBack = 2;
	}
	?>
	</td>
  </tr>
  <?php
  if (!$error) {
  ?>
  <tr><td colspan="4"><div class="infoBar"><?php echo $lang['setup']['congratsFilePerms']; ?> </div></td></tr>
  <?php
  } else {
  ?>
  <tr><td colspan="4"><div class="errorBar"><?php echo $lang['setup']['filePermsNotCorrect']; ?></div></td></tr>
  <?php
  }
  ?>
  <?php
  break;
  case 3;
  ?>
  <tr>
    <td colspan="4"><strong><!--<a href="javascript:;"><img src="images/helpIcon.gif" alt="<?php echo $lang['setup']['help']; ?>" width="30" height="28" border="0" align="right" title="" onclick="openBrWindow('help/licenseKey.php?l=<?php echo $langFolder; ?>','','width=500,height=500')" /></a>--><?php echo $lang['setup']['license_key']; ?></strong> <br />
      <?php echo $lang['setup']['fromUs']; ?></td>
  </tr>
  <tr>
    <td class="borderBot"><?php echo $lang['setup']['license_key']; ?></td>
    <td class="borderBot"><input type="text" name="license_key" class="textbox" value="<?php echo $_SESSION['license_key']; ?>" /></td>
    <td colspan="2" class="borderBot"><?php echo $lang['setup']['not_copy_key']; ?></td>
  </tr>
  <tr>
    <td colspan="4"><strong><a href="javascript:;"><img src="images/helpIcon.gif" alt="<?php echo $lang['setup']['help']; ?>" width="30" height="28" border="0" align="right" title="" onclick="openBrWindow('help/db.php?l=<?php echo $langFolder; ?>','','width=500,height=500,scrollbars=1')" /></a><?php echo $lang['setup']['dbSettings']; ?></strong> <br />
      <?php echo $lang['setup']['fromProvider']; ?></td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['dbhostname']; ?></td>
    <td><input type="text" name="dbhost" class="textbox" value="<?php echo $_SESSION['dbhost']; ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> localhost</td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['dbName']; ?></td>
    <td><input type="text" name="dbname" class="textbox" value="<?php echo $_SESSION['dbname']; ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> cubecart_database </td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['dbUsername']; ?></td>
    <td><input type="text" name="dbuser" class="textbox" value="<?php echo $_SESSION['dbuser']; ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?>  database_user </td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['dbPassword']; ?></td>
    <td><input type="password" autocomplete="off" name="dbpass" class="textbox" /> </td>
    <td colspan="2" nowrap="nowrap">&nbsp;</td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['confPass']; ?></td>
    <td><input type="password" autocomplete="off" name="dbpassconfirm" class="textbox" /> </td>
    <td colspan="2" nowrap="nowrap">&nbsp;</td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['dbPrefix']; ?><br />
    <?php echo $lang['setup']['dbPrefixOptional']; ?></td>
    <td><input type="text" name="dbprefix" class="textbox" value="<?php echo $_SESSION['dbprefix']; ?>" />     </td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> store1_ </td>
  </tr>
  <tr>
    <td class="borderBot"><?php echo $lang['setup']['dropifExist']; ?></td>
    <td class="borderBot"><input name="dropTables" type="checkbox" value="1" checked="CHECKED" /></td>
    <td colspan="2" class="borderBot"><?php echo $lang['setup']['previousInstallLost']; ?></td>
  </tr>
  <tr>
    <td colspan="4"><strong><?php echo $lang['setup']['localeSettings']; ?></strong></td>
    </tr>
  <tr>
    <td class="borderBot"><?php echo $lang['setup']['storeCountry']; ?></td>
    <td class="borderBot">
	<select name="installCountry" class="textbox">
      <option value="AU" <?php if($_SESSION['installCountry']=="AU") echo "selected='selected'"; ?>>Australia</option>
	  <option value="CA" <?php if($_SESSION['installCountry']=="CA") echo "selected='selected'"; ?>>Canada</option>
	  <option value="EU" <?php if($_SESSION['installCountry']=="EU") echo "selected='selected'"; ?>><?php echo $lang['setup']['EU']; ?></option>
	  <option value="US" <?php if($_SESSION['installCountry']=="US") echo "selected='selected'"; ?>><?php echo $lang['setup']['US']; ?></option>
      <option value="UK" <?php if($_SESSION['installCountry']=="UK") echo "selected='selected'"; ?>><?php echo $lang['setup']['UK']; ?></option>
    </select></td>
    <td colspan="2" class="borderBot"><?php echo $lang['setup']['currenciesAccord']; ?></td>
    </tr>
  <tr>
    <td colspan="4"><a href="javascript:;"><img src="images/helpIcon.gif" alt="<?php echo $lang['setup']['help']; ?>" width="30" height="28" border="0" align="right" title="" onclick="openBrWindow('help/admin.php?l=<?php echo $langFolder; ?>','','width=500,height=500,scrollbars=1')" /></a><strong><?php echo $lang['setup']['administratorSettings']; ?></strong><br />
<?php echo $lang['setup']['adminSetDesc']; ?>    </td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['username']; ?></td>
    <td><input type="text" name="username" class="textbox" value="<?php echo $_SESSION['username']; ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> <?php echo randomUser();?></td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['password'];?></td>
    <td><input type="password" autocomplete="off" name="pass" class="textbox" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> <?php echo recPass();?></td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['confPass']; ?></td>
    <td><input type="password" autocomplete="off" name="pass_conf" class="textbox" /></td>
    <td colspan="2" nowrap="nowrap">&nbsp;</td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['fullName']; ?></td>
    <td><input type="text" name="fullName" class="textbox" value="<?php echo $_SESSION['fullName']; ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> Rachel Taylor</td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['emailAddress']; ?></td>
    <td><input type="text" name="email" class="textbox" value="<?php echo $_SESSION['email']; ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> yourname@example.com</td>
  </tr>
  <tr>
    <td class="borderBot"><?php echo $lang['setup']['subscribe']; ?></td>
    <td class="borderBot"><input type="checkbox" name="subscribe" value="1" <?php if($_SESSION['subscribe']) { ?>checked="checked"<?php } ?> /></td>
    <td class="borderBot" colspan="2"><?php echo $lang['setup']['subscribe_desc']; ?></td>
  </tr>
  <tr>
    <td colspan="4"><a href="javascript:;"><img src="images/helpIcon.gif" alt="<?php echo $lang['setup']['help']; ?>" width="30" height="28" border="0" align="right" title="" onclick="openBrWindow('help/advanced.php?l=<?php echo $langFolder; ?>','','width=500,height=500,scrollbars=1')" /></a>
	<strong><?php echo $lang['setup']['advancedSettings'];?></strong> <br />
    <?php echo $lang['setup']['leaveIfUnsure'];?></td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['storeURL']; ?></td>
    <td>
	<input type="text" name="storeURL" class="textbox" value="http://<?php echo $_SERVER['HTTP_HOST'].str_replace("/setup/install.php","",$_SERVER['PHP_SELF']); ?>" /></td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> http://www.example.com/store </td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['siteRootRel']; ?></td>
    <td>
	  <input type="text" name="rootRel" class="textbox" value="<?php echo str_replace("setup/install.php","",$_SERVER['PHP_SELF']); ?>" />
	</td>
    <td colspan="2" nowrap="nowrap"><?php echo $lang['setup']['eg']; ?> /store/ (Including End Slash) </td>
  </tr>
  <tr>
    <td><?php echo $lang['setup']['siteEncoder']; ?></td>
    <td>
	  <select class="textbox" name="encoder">
	    <option value="auto" <?php if($_SESSION['encoder']=="auto") echo "selected='selected'"; ?>><?php echo $lang['setup']['auto_detect']; ?></option>
	    <option value="zend" <?php if($_SESSION['encoder']=="zend") echo "selected='selected'"; ?>>Zend Optimizer (<?php echo (has_zend_optimizer()) ? $lang['setup']['installed'] : $lang['setup']['not_installed']; ?>)</option>
		<option value="ioncube" <?php if($_SESSION['encoder']=="ioncube") echo "selected='selected'"; ?>>IonCube Loader (<?php echo (has_ioncube_loader()) ? $lang['setup']['installed'] : $lang['setup']['not_installed']; ?>)</option>
	  </select>
	</td>
    <td colspan="2" nowrap="nowrap">&nbsp;</td>
  </tr>
  
  <?php
  break;
  case 4;
	if ($safemode == true) {
		
		if ($errorWriting == true) {
			$stepBack = 4;
	?>
	
  <tr>
    <td colspan="4">We have detected that your server is currently running in PHP safe mode. In order to finish installing your store, you will need to create includes/global.inc.php with the following content.</td>
  </tr>  
  <tr>
	<td colspan="4"><textarea id="global-inc-php" cols="60" rows="10"><?php echo htmlentities($outputConfig); ?></textarea></td>
  </tr>
  <?php
		}
	} else {
  ?>
  <tr>
    <td colspan="4"><?php echo $lang['setup']['filepermsBack']; ?>	</td>
    </tr>
  <tr>
 	<td><strong><?php echo $lang['setup']['fileFolder'];?></strong></td>
    <td align="center"><strong><?php echo $lang['setup']['currentPermission'];?></strong></td>
    <td colspan="2" rowspan="2" align="center"><a href="javascript:;"><img src="images/helpIcon.gif" alt="<?php echo $lang['setup']['help']; ?>" width="30" height="28" border="0" title="" onclick="openBrWindow('help/filePerms.php?l=<?php echo $langFolder; ?>','','width=500,height=500,scrollbars=1')" /></a></td>
  </tr>
  <tr>
    <td>includes/global.inc.php</td>
    <td align="center">
	<?php 
	//$fileperms = substr(sprintf('%o', fileperms("..".CC_DS."includes".CC_DS."global.inc.php")), -4);
	@chmod("..".CC_DS."includes".CC_DS."global.inc.php", 0444);
	if(substr(PHP_OS, 0, 3)!=="WIN" && !cc_is_writable("..".CC_DS."includes".CC_DS."global.inc.php")) {

		echo "<span class='greenTxt'>".$lang['setup']['read_only']."</span>";
		
	} elseif(substr(PHP_OS, 0, 3)!=="WIN" && cc_is_writable("..".CC_DS."includes".CC_DS."global.inc.php")) {

		echo "<span class='redTxt'>".$lang['setup']['writable']."</span>";
		$error = TRUE;

	} else {
		echo "<span class='greenTxt'>".$lang['front']['na']."</span>";
	}
	?>  </td>
    </tr>
  <?php
  if($error == true){
  ?>
  <tr><td colspan="4"><div class="errorBar"><?php echo $lang['setup']['filePermsNotCorrect']; ?></div></td></tr>
  <?php
  $stepBack = 4;
  } else {
  ?>
  <tr><td colspan="4"><div class="infoBar"><?php echo $lang['setup']['congratsFilePerms']; ?></div></td></tr>
  <?php
	}  
  }
  ?>
  <?php
  break;
  case 5;
  ?>
  <tr>
    <td colspan="4">
	<div class="infoBar"><?php echo $lang['setup']['congratulations']; ?></div>
	<?php echo $lang['setup']['congratulationsSub']; ?>
	<div style="padding-left: 100px;">
	<ul>
	<li><a href="<?php echo $glob['storeURL'];?>/<?php echo $glob['adminFile'];?>"><?php echo $lang['setup']['adminHomepage']; ?></a><br />
	  <?php echo $glob['storeURL'];?>/<?php echo $glob['adminFile'];?></li>
	<li><a href="<?php echo $glob['storeURL'];?>/"><?php echo $lang['setup']['storeHomepage']; ?></a><br />
	<?php echo $glob['storeURL'];?>/</li>
	</ul>
	</div>
	<strong><?php echo $lang['setup']['important']; ?></strong> <?php echo $lang['setup']['deleteInstall']; ?>	</td>
  </tr>
 <?php if ($glob['encoder'] == 'ioncube' && !has_ioncube_loader()) { ?>
  <tr>
	<td colspan="4">
	<?php echo sprintf($lang['setup']['ioncube_install'], PHP_OS); ?>
	</td>
  </tr>
 <?php
  }
  break;
  }
 ?>
 <tr>
    <td width="33%">&nbsp;</td>
    <td colspan="3">
 <?php
 if($stepBack>0){
 ?>
  <input name="button" type="button" class="submit" onclick="goToURL('parent','install.php?step=<?php echo $stepBack; if($noRepeat==TRUE || $_GET['skip']==1) { echo "&amp;skip=1"; }?>&amp;l=<?php echo $langFolder; ?>');return document.returnValue" value="<?php echo $lang['setup']['tryAgain']; ?>" />
  
 <?php
 } elseif($_GET['step']<5) {
 ?>
 <input name="submit" type="submit" class="submit" value="<?php echo sprintf($lang['setup']['contToStep'],$_GET['step']+1); ?> &raquo;" <?php if(isset($onclick)) { echo $onclick; } ?> />
 <?php
 } 
 ?> </td>
  </tr>
</table>
</form>
<?php
include("includes".CC_DS."footer.inc.php");
?>