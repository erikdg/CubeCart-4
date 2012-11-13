<?php

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$formTemplate = new XTemplate ('modules'.CC_DS.'gateway'.CC_DS.$_POST['gateway'].CC_DS.'form.tpl','',null,'main',true, true);
$display_3ds = true;


$vars = array(	'pay_to_email' => $module['email'],
		'transaction_id' => $orderSum['cart_order_id'],
		'return_url' => $GLOBALS['storeURL'].'/index.php?_g=rm&amp;type=gateway&amp;cmd=process&amp;module=moneybookers&amp;cart_order_id='.$orderSum['cart_order_id'],
		'return_url_target' => 1,
		'cancel_url' => $GLOBALS['storeURL'].'/index.php?_g=rm&amp;type=gateway&amp;cmd=process&amp;module=moneybookers&amp;cart_order_id='.$orderSum['cart_order_id'].'&amp;cancelled=true',
		'cancel_url_target' => 1,
		'hide_login' => 1,
		'status_url' => $GLOBALS['storeURL'].'/index.php?_g=rm&amp;type=gateway&amp;cmd=call&amp;module=moneybookers',
		'language' => 'EN',
		'pay_from_email' => $orderSum['email'],
		'amount' => $orderSum['prod_total'],
		'currency' => $config['defaultCurrency'],
		'firstname' => $billingName[2],
		'lastname' => $billingName[3],
		'address' => $orderSum['add_1'].' '.$orderSum['add_2'],
		'postal_code' => $orderSum['postcode'],
		'city' => $orderSum['town'],
		'country' => getCountryFormat($orderSum['country'],'id','iso'),
		'hide_login' => 0,
		'payment_methods' => 'WLT',
		'recipient_description' => $config['siteTitle'],
		'merchant_fields' => 'referring_platform',
		'referring_platform' => 'cubecart',
		'status_url2' => 'mailto:'.$config['masterEmail'],
		'logo_url' => $module['logoURL']
	);

$formTemplate->assign('VAL_IFRAME_URL','https://www.moneybookers.com/app/payment.pl?'.http_build_query($vars));
$formTemplate->parse('form');
$formTemplate = $formTemplate->text('form');
?>