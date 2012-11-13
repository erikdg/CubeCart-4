<?php
require("../../ini.inc.php");
define("LANG_FOLDER",$_GET['l']);
require("..".CC_DS."..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."common.inc.php");
require("..".CC_DS."..".CC_DS."language".CC_DS.LANG_FOLDER.CC_DS."setup.inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang['setup']['installHelp']; ?> <?php echo $lang['setup']['helpFilePerms']; ?></title>
<link href="../styles/style.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#FFFFFF;">
<?php 
if(!isset($_GET['os'])) { 
?>
<div style="position:absolute; bottom: 0px; width: 500px;" align="center"><a href="javascript:window.close();"><?php echo $lang['setup']['closeWindow']; ?></a> <?php if(isset($_GET['os'])) { ?>| <a href="<?php echo $_SERVER['PHP_SELF']; ?>?l=<?php echo $_GET['l']; ?>"><?php echo $lang['setup']['prevPage']; ?></a><?php } ?></div>
<div style="font-size: 16px; color: #0971CE; font-weight: bold; padding-top: 5px; padding-bottom: 5px;"><?php echo $lang['setup']['selectOs']; ?></div>
  
<?php

require("osDetect.inc.php");
 
} 
elseif($_GET['os']=="mac") 
{ 
?>
<div style="position:absolute; bottom: 0px; width: 500px;" align="center"><a href="javascript:window.close();"><?php echo $lang['setup']['closeWindow']; ?></a> <?php if(isset($_GET['os'])) { ?>| <a href="<?php echo $_SERVER['PHP_SELF']; ?>?l=<?php echo $_GET['l']; ?>"><?php echo $lang['setup']['prevPage']; ?></a><?php } ?></div>
<div style="font-size: 16px; color: #0971CE; font-weight: bold; padding-top: 5px; padding-bottom: 5px;" id="mac"><?php echo $lang['setup']['permsMac']; ?></div>
<div style="padding-bottom: 500px;">
  <div align="center"><img src="../images/macFilePerms.gif" alt="" width="432" height="432" title="" /></div>
</div>

<?php 
} 
elseif($_GET['os']=="win") 
{ 
?>
<div style="position:absolute; bottom: 0px; width: 500px;" align="center"><a href="javascript:window.close();"><?php echo $lang['setup']['closeWindow']; ?></a> <?php if(isset($_GET['os'])) { ?>| <a href="<?php echo $_SERVER['PHP_SELF']; ?>?l=<?php echo $_GET['l']; ?>"><?php echo $lang['setup']['prevPage']; ?></a><?php } ?></div>
<div style="font-size: 16px; color: #0971CE; font-weight: bold; padding-top: 5px; padding-bottom: 5px;" id="win"><?php echo $lang['setup']['permsWin']; ?></div>
<div style="padding-bottom: 500px;">
<?php echo $lang['setup']['permsWinDesc']; ?>
</div>

<?php 
} 
elseif($_GET['os']=="linux") 
{ 
?>
<div style="font-size: 16px; color: #0971CE; font-weight: bold; padding-top: 5px; padding-bottom: 5px;" id="linux"><?php echo $lang['setup']['permsLinux']; ?></div>
<?php echo $lang['setup']['permsLinuxDesc']; ?>

<div align="center"><a href="javascript:window.close();"><?php echo $lang['setup']['closeWindow']; ?></a> <?php if(isset($_GET['os'])) { ?>| <a href="<?php echo $_SERVER['PHP_SELF']; ?>?l=<?php echo $_GET['l']; ?>"><?php echo $lang['setup']['prevPage']; ?></a><?php } ?></div>
<?php 
} 
?>
</body>
</html>