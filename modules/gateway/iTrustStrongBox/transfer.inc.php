<?php
if (!defined('CC_INI_SET')) die("Access Denied");
function repeatVars()
{	
	return FALSE;	
}

function fixedVars()
{
    global $module, $orderSum, $config;
    
    $hiddenVars = "<input type='hidden' name='x_dealer_id' value='".$module['dealerId']."' />";
	$hiddenVars .= "<input type='hidden' name='x_custid_prefix' value='".$module['CustPrefix']."' />";
    $hiddenVars .= "<input type='hidden' name='x_cust_id' value='".$orderSum['customer_id']."' />";
    $hiddenVars .= "<input type='hidden' name='x_relay_url' value='" . $GLOBALS['storeURL'] . "/modules/gateway/iTrustStrongBox/return.php?status=Success&orderid=".$_GET['cart_order_id']."' />";
    $hiddenVars .= "<input type='hidden' name='x_relay_url_fail' value='" . $GLOBALS['storeURL'] . "/modules/gateway/iTrustStrongBox/return.php?status=Declined&orderid=".$_GET['cart_order_id']."' />";
    $hiddenVars .= "<input type='hidden' name='x_amount' value='".$orderSum['prod_total']."' />";    
    $hiddenVars .= "<input type='hidden' name='x_invoice_num' value='".$orderSum['cart_order_id']."' />";
    $hiddenVars .= "<input type='hidden' name='x_first_name' value='".$orderSum['name_d']."' />";
    $hiddenVars .= "<input type='hidden' name='x_address' value='".$orderSum['add_1_d']." ".$orderSum['add_2_d']."' />";
    $hiddenVars .= "<input type='hidden' name='x_city' value='".$orderSum['town_d']."' />";
    $hiddenVars .= "<input type='hidden' name='x_state' value='".$orderSum['country_d']."' />";
    $hiddenVars .= "<input type='hidden' name='x_zip' value='".$orderSum['postcode_d']."' />";
    $hiddenVars .= "<input type='hidden' name='x_country' value='".$orderSum['country_d']."' />";
    $hiddenVars .= "<input type='hidden' name='x_customer_ip' value='".get_ip_address()."' />";
            
    return $hiddenVars;
}

$formAction = "https://sb3.itruststrongbox.com/shop/ProcessOrder.aspx";
$formMethod = "post";
$formTarget = "_self";
$transfer = "auto";

?>
