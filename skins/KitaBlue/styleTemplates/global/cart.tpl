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
<!--[if IE 7]><link href="skins/{VAL_SKIN}/styleSheets/IE7.css" rel="stylesheet" type="text/css" /><![endif]-->
<!--[if IE 6]><link href="skins/{VAL_SKIN}/styleSheets/IE6.css" rel="stylesheet" type="text/css" /><![endif]-->
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
	<div id="topHeader">
		<div>{SEARCH_FORM}</div>
	</div>
		<div>{SITE_DOCS}</div>

	<div id="pageSurround">

<div>
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="190" valign="top">
	<div class="colLeftCheckout">
		{CART_NAVI}
		<div>{SESSION}</div>
	</div>
	</td>
	<td width="100%" valign="top">
	<div class="colMainCheckout">
		{PAGE_CONTENT}	</div>
</td>
</tr>
</table>
</div>

<br clear="all" />



</div>

{DEBUG_INFO}

</body>
</html>
<!-- END: body -->