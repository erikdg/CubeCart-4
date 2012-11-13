<?php  
  require_once '../../../ini.inc.php';
  require_once '../../../includes/global.inc.php';
  
  $status = $_GET['status'];
  $statusid = '';
  $orderid = $_GET['orderid'];
  $completeUrl = '../../../index.php?_g=co&_a=confirmed&cart_order_id=' . $orderid . '&s=';

  if ($status == 'Cancelled') {
    $statusid = '6';
    $completeUrl = $completeUrl . '6';    //cancelled
  }
  else if ($status == 'Declined') {
    $statusid = '4';
    $completeUrl = $completeUrl . '4';    //declined
  }
  else if ($status == 'Success') {
    $statusid = '2';
    $completeUrl = $completeUrl . '2';    //success / processing
  }
  
  $localdb = mysql_connect($glob['dbhost'], $glob['dbusername'], $glob['dbpassword']) or die(mysql_error());
  $selectdb = mysql_select_db($glob['dbdatabase'], $localdb);
  $query = "UPDATE " .$glob['dbprefix']. "CubeCart_order_sum SET status = " . $statusid ." WHERE cart_order_id = '" . $orderid . "'";
  mysql_query($query);
    
  header('Location: ' . $completeUrl);
  
?>
