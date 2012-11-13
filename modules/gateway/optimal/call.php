<?php
$_GET = array(
	'_g' => 'rm',
	'type' => 'gateway',
	'cmd' => 'call',
	'module' => 'optimal'
);
$vars = is_array($_POST) ? array_merge($_POST, $_GET) : $_GET;
header('Location: ../../../index.php?'.http_build_query($vars));