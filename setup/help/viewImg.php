<?php
require("../../ini.inc.php");
require("..".CC_DS."..".CC_DS."language".CC_DS.$_GET['l'].CC_DS."common.inc.php");
require("..".CC_DS."..".CC_DS."language".CC_DS.$_GET['l'].CC_DS."setup.inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang['setup']['skinPreview'];?> - <?php echo $_GET['img']; ?></title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>

<body>
<a href="javascript:window.close();"><img src="../images/screenshots/<?php echo $_GET['img'].".gif"; ?>" alt="<?php echo $lang['setup']['clicktoClose']; ?>" title="<?php echo $lang['setup']['clicktoClose']; ?>" border="0" /></a> 
</body>
</html>
