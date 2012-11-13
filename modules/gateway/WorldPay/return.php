<?php
$_GET['_g'] = 'rm';
$_GET['type'] = 'gateway';
$_GET['cmd'] = 'process';
$_GET['module'] = 'WorldPay';
$_GET['email'] = $_REQUEST['email'];
$_GET['cartId'] = $_REQUEST['cartId'];
$_GET['transId'] = $_REQUEST['transId'];
$_GET['amount'] = $_REQUEST['amount'];
$_GET['transStatus'] = $_REQUEST['transStatus'];
$_GET['disablejs'] = true;
include ('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'index.php');