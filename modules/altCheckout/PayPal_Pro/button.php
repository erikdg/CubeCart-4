<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed. 
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	button.inc.php
|   ========================================
|	Button PayPal EC
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");
class PayPal_Pro {

	function buildIt($moduleName="", $urlOnly = false) {
		
		global $config,$basket,$altCheckoutInv,$orderSum;
		
		if($basket['discount']>0) {
			$altCheckoutInv[] = array(
				'taxType' => 0,
           	 	'name' => 'Discount',
            	'options' => null,
            	'quantity' => 1,
            	'price' => '-'.$basket['discount']
            );
		}
		
		if(is_array($altCheckoutInv)){
			$subTotal = 0;
			foreach($altCheckoutInv as $item) {
				$line_amount = isset($item['priceIncTax']) ? $item['priceIncTax'] : $item['price'];
				$subTotal += $line_amount * $item['quantity'];
				
			}
		}
		
		switch(strtolower(LANG_FOLDER)) {
			case "es":
				$locale = "es_ES";
			break;
			case "fr":
				$locale = "fr_FR";
			break;
			case "nl":
				$locale = "nl_NL";
			break;
			case "de":
				$locale = "de_DE";
			break;
			case "it":
				$locale = "it_IT";
			break;
			default:
				$locale = "en_US";
			break;
		}
	
		$linkInv = "";
		
		if(is_array($altCheckoutInv)) {
			$linkInv .= "&amp;items=".base64_encode(serialize($altCheckoutInv));
		}

		if($urlOnly==true) {
			$link = "index.php?_g=rm&amp;type=altCheckout&amp;cmd=process&amp;module=PayPal_Pro&amp;ccb=".base64_encode($orderSum['prod_total'].",".$config['defaultCurrency']).$linkInv;
		} else {
		
			$link = "<a href='index.php?_g=rm&amp;type=altCheckout&amp;cmd=process&amp;module=PayPal_Pro&amp;ccb=".base64_encode($subTotal.",".$config['defaultCurrency']).$linkInv."' target='_self' /><img src='https://www.paypal.com/".$locale."/i/btn/btn_xpressCheckout.gif' border='0' title='' alt='' /></a>";
		
		}
		
		return $link;
		
	}
}
?>