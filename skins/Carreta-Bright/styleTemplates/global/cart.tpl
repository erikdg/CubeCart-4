<!-- BEGIN: body -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={VAL_ISO}" />
<title>{META_TITLE}</title>
<meta name="description" content="{META_DESC}" />
<meta name="keywords" content="{META_KEYWORDS}" />
<link href="skins/{VAL_SKIN}/styleSheets/layout.css" rel="stylesheet" type="text/css" />
<link href="skins/{VAL_SKIN}/styleSheets/style.css" rel="stylesheet" type="text/css" />
<!--[if lt IE 7.0]>
<script defer type="text/javascript" src="js/pngfix.js"></script>
<link href="skins/{VAL_SKIN}/styleSheets/IE6.css" rel="stylesheet" type="text/css" /><![endif]-->
<style type="text/css">@import "skins/{VAL_SKIN}/styleSheets/importStyles.css" screen;</style>
<script type="text/javascript">
var fileBottomNavCloseImage = '{VAL_ROOTREL}images/lightbox/close.gif';
var fileLoadingImage = '{VAL_ROOTREL}images/lightbox/loading.gif';
</script>
<script type="text/javascript" src="js/jslibrary.js"></script>
<script type="text/javascript">
 var RecaptchaOptions = {
    theme : 'custom'
 }
</script>
</head>

<body onload="initialiseMenu();">
<div id="PageOuter">
<div id="pageSurround">
<div id="Header">
<div id="Session">
{SESSION}
</div>
</div><!--close Header -->

<div id="LeftColumn">
{SEARCH_FORM}

{CART_NAVI}
</div>

<div id="Content">
{PAGE_CONTENT}
</div>
<br clear="all" />

<div id="SiteDocs">
{SITE_DOCS}
</div>
</div><!-- close pageSurround -->
</div><!--close PageOuter-->
{DEBUG_INFO}
</body>
</html>
<!-- END: body -->