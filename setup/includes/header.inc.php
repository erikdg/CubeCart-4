<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php 
$versionNumber = (isset($thisVersion)) ? $thisVersion : $ini['ver'];
echo sprintf($lang['setup']['installation'],$versionNumber);
?></title>
<script language="javascript" src="js/library.js" type="text/javascript"></script>
<script src="../js/prototype.js" type="text/javascript"></script>
<script src="../js/password.js" type="text/javascript"></script>
<link href="styles/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="main"><div style="padding: 20px 0px 10px 20px;"><img src="../images/logos/ccLogo.gif" alt="" width="280" height="55" title="" /></div>