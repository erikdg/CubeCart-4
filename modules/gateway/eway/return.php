<?php
$array = array(
	'_g'	 =>'rm',
	'type'	 =>'gateway',
	'cmd'	 =>'process',
	'module' =>'eway'
);
$query_string = is_array($_POST) ? array_merge($array,$_POST) : $array;
header('location: ../../../index.php?'.http_build_query($query_string,'','&'));