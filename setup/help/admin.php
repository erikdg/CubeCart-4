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
<title><?php echo $lang['setup']['installHelp']; ?> <?php echo $lang['setup']['helpAdminSettings']; ?></title>
<link href="../styles/style.css" rel="stylesheet" type="text/css" />
</head>

<body style="background-color:#FFFFFF;">
<div style="position:absolute; bottom: 0px; width: 500px;" align="center"><a href="javascript:window.close();"><?php echo $lang['setup']['closeWindow']; ?></a> <?php if(isset($_GET['os'])) { ?>| <a href="<?php echo $_SERVER['PHP_SELF']; ?>?l=<?php echo $_GET['l']; ?>"><?php echo $lang['setup']['prevPage']; ?></a><?php } ?></div>
<div style="font-size: 16px; color: #0971CE; font-weight: bold; padding-top: 5px; padding-bottom: 5px;"><strong><?php echo $lang['setup']['adminConfSettings']; ?></strong></div>
<?php echo $lang['setup']['adminConfSettingsDesc']; ?>
</body>
</html>