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
|	index.php
|   ========================================
|	Installation Script Start Page	
+--------------------------------------------------------------------------
*/

require("../ini.inc.php");
session_start();
require("..".CC_DS."includes".CC_DS."functions.inc.php");

// stop empty basket issue when add to basket
if(isset($GLOBALS[CC_SESSION_NAME])) {
	setcookie(CC_SESSION_NAME);
}

if(!isset($_GET['l'])){
	httpredir("index.php?l=en");
}

$langFolder = preg_replace('/[^a-zA-Z0-9_\-\+]/', '',$_GET['l']);
define('LANG_FOLDER', $langFolder);

require("..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."common.inc.php");
require("..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."setup.inc.php");
include("includes".CC_DS."header.inc.php");
?>

<div class="mainPad">
<div class="subTitle"><?php echo sprintf($lang['setup']['installation'],$ini['ver']); ?></div>
<div>
<table border="0" width="100%" cellpadding="3" cellspacing="1" class="formTable">
  <tr>
    <td class="blueHead"><?php echo $lang['setup']['splash_title']; ?></td>
  </tr>
   <tr>
     <td style="text-align:center;">
	 <strong>
	 <?php 
	 echo $lang['setup']['chooseLang']; 
	 ?>
	 </strong>
	 <select name="langSwitch" class="dropDown" onchange="jumpMenu('parent',this,0)">
	 <?php
	$path = "..".CC_DS."language";
	if (is_dir($path)) {
	
		$returnPage = urlencode(currentPage());
		foreach (glob($path.CC_DS.'*', GLOB_MARK) as $folder) {
			if (is_dir($folder) && preg_match('#[a-z]{2}(\_[A-Z]{2})?#i', $folder) && file_exists($folder.'config.php')) {
				require $folder.'config.php';
				
				$folderName = str_replace(array('language', CC_DS, ".."), '', $folder);
				
				if($langFolder==$folderName){ $selected = "selected=\"selected\""; } else { $selected = ""; }
				
				echo "<option value=\"index.php?step=1&amp;l=".$folderName."\"   onmouseover=\"javascript:getImage('../language/".$folderName."/flag.gif');\" ".$selected.">".$langName."</option>";
			}
		}
	}
	
	
	 ?> 
	 </select>
	 <img src="../language/<?php echo $langFolder; ?>/flag.gif" alt="" width="21" height="14" id="img" title="" />	 </td>
     </tr>
   
   <tr>
    <td><div style="background-color: #E8F3FD; padding: 6px; cursor: pointer; cursor: hand;" onmouseover="this.style.backgroundColor='#EBFDE8'" onmouseout="this.style.backgroundColor='#E8F3FD'" onclick="parent.location='install.php?l=<?php echo $_GET['l'];?>'">
	<img src="images/install.gif" width="89" height="84" hspace="7" align="left" /><a href="install.php?l=<?php echo $_GET['l'];?>" class="splashLink"><?php echo $lang['setup']['install_cubecart'];?></a>
	<p><?php echo $lang['setup']['fresh_install'];?></p></div></td>
    </tr>
	<?php
	$globPath = "..".CC_DS."includes".CC_DS."global.inc.php";
	if (file_exists($globPath)) {
		include($globPath);
	}
	if ($glob['installed'] == true) {
	?>
	<tr>
    <td>
	<div style="background-color: #E8F3FD; padding: 6px; cursor: pointer; cursor: hand;" onmouseover="this.style.backgroundColor='#EBFDE8'" onmouseout="this.style.backgroundColor='#E8F3FD'" onclick="parent.location='upgrade.php?l=<?php echo $_GET['l'];?>'">
	  <img src="images/upgrade.gif" width="72" height="83" hspace="15" align="left" /><a href="upgrade.php?l=<?php echo $_GET['l'];?>" class="splashLink"><?php echo $lang['setup']['upgrade_cubecart'];?></a>	
	  <p><?php echo $lang['setup']['upgrade_existing'];?></p>
	  </div>
	</td>
    </tr>
	<?php
	}
	?>
</table>
<?php
include("includes".CC_DS."footer.inc.php");
?>