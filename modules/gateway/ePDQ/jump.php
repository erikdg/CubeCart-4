<?php
if (isset($_GET['oid'])) {
	include("../../../includes/functions.inc.php");
	preg_match('#^(.*)/modules#iu', $_SERVER['REQUEST_URI'], $matches);
	httpredir($matches[1].'/index.php?_g=rm&type=gateway&cmd=process&module=ePDQ&cart_order_id='.$_GET['oid']);
	exit;
}
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Redirecting to Barclays ePDQ...</title>
</head>
<body onload="document.getElementById('jump').submit();">
  <form id="jump" action="https://<?php echo $_SESSION['epdq']['server']; ?>/cgi-bin/CcxBarclaysEpdq.e" method="post">
<?php
if (isset($_SESSION['epdq'])) {
	foreach ($_SESSION['epdq'] as $name => $value) {
		if ($name == 'server') continue;
		echo sprintf('<input type="hidden" name="%s" value="%s" />', $name, $value);
	}
}
?>
	<input type="submit" value="Proceed" />
  </form>
</body>
</html>