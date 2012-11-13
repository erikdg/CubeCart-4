<?php
if(!defined('CC_INI_SET')){ die("Access Denied"); }

$formTemplate = new XTemplate ('modules'.CC_DS.'gateway'.CC_DS.$_POST['gateway'].CC_DS.'form.tpl','',null,'main',true, true);
$display_3ds = true;
$iframeURL = $module['test_mode'] ? 'https://checkout.test.optimalpayments.com/securePayment/op/profileCheckoutRequest.htm' : 'https://checkout.optimalpayments.com/securePayment/op/profileCheckoutRequest.htm';
$formTemplate->assign('VAL_IFRAME_URL',$iframeURL.'?'.fixedVars('get'));
$formTemplate->parse('form');
$formTemplate = $formTemplate->text('form');
?>