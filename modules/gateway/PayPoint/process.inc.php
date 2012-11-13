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
|	process.inc.php
|   ========================================
|	Process PayPoint Gateway
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

if (!empty($_GET['code'])) {
	switch($_GET['code']) {
		case 'A':
			$message = 'Transaction authorised by bank. auth_code available as bank reference';
			break;
		case 'N':
			$message = 'Transaction not authorised. Failure message text available to merchant';
			break;
		case 'C':
			$message = 'Communication problem. Trying again later may well work';
			break;
		case 'F':
			$message = 'The PayPoint.net system has detected a fraud condition and rejected the transaction. The message field will contain more details.';
			break;
		case 'P:A':
			$message = 'Pre-bank checks. Amount not supplied or invalid';
			break;
		case 'P:X':
			$message = 'Pre-bank checks. Not all mandatory parameters supplied';
			break;
		case 'P:P':
			$message = 'Pre-bank checks. Same payment presented twice';
			break;
		case 'P:S':
			$message = 'Pre-bank checks. Start date invalid';
			break;
		case 'P:E':
			$message = 'Pre-bank checks. Expiry date invalid';
			break;
		case 'P:I':
			$message = 'Pre-bank checks. Issue number invalid';
			break;
		case 'P:C':
			$message = 'Pre-bank checks. Card number fails LUHN check (the card number is wrong)';
			break;
		case 'P:T':
			$message = 'Pre-bank checks. Card type invalid - i.e. does not match card number prefix';
			break;
		case 'P:N':
			$message = 'Pre-bank checks. Customer name not supplied';
			break;
		case 'P:M':
			$message = 'Pre-bank checks. Merchant does not exist or not registered yet';
			break;
		case 'P:B':
			$message = 'Pre-bank checks. Merchant account for card type does not exist';
			break;
		case 'P:D':
			$message = 'Pre-bank checks. Merchant account for this currency does not exist';
			break;
		case 'P:V':
			$message = 'Pre-bank checks. CV2 security code mandatory and not supplied / invalid';
			break;
		case 'P:R':
			$message = 'Pre-bank checks. Transaction timed out awaiting a virtual circuit. Merchant may not have enough virtual circuits for the volume of business.';
			break;
		case 'P:#': // this won't come up needs work
			$message = 'Pre-bank checks. No MD5 hash / token key set up against account';
		break;
		default:
			$message = 'Unspecified problem. Please look in your PayPoint control panel for more information.';

	}
}

$cart_order_id = sanitizeVar($_GET['cart_order_id']); // Used in remote.php $cart_order_id is important for failed orders
if ($customer = $db->select('SELECT `customer_id`, `prod_total` FROM '.$glob['dbprefix'].'CubeCart_order_sum WHERE `cart_order_id` = '.$db->MySQLSafe($_GET['cart_order_id']))) {

	$transData['customer_id']	= $customer[0]['customer_id'];
	$transData['gateway']		= 'PayPoint';
	$transData['trans_id']		= $_GET['trans_id'];
	$transData['order_id']		= $cart_order_id;
	$transData['amount']		= $_GET['amount'];
	$transData['status'] 		= ($_GET['code'] == 'A') ? 'Authorised' : 'Not Authorised';

	if ($_GET['code'] == 'A') {
		if (isset($_GET['hash']) && !empty($module['digest_key']) && preg_match('#(.*)&hash=[0-9a-f]+$#i', $_SERVER['REQUEST_URI'], $match)) {
			$hash	= md5($match[1].'&'.$module['digest_key']);
			if ($hash == $_GET['hash']) {
				$paymentResult		= 2;
				$transData['notes']	= 'Card charged successfully. '.$message;
				$order->orderStatus(3, $cart_order_id);
			} else {
				$paymentResult		= 1;
				$transData['notes']	= 'Payment failed. '.$message;
			}
		} else {
			$paymentResult		= 2;
			$transData['notes']	= 'Card charged successfully. '.$message;
			$order->orderStatus(3, $cart_order_id);
		}
	} else {
		$paymentResult		= 1;
		$transData['notes']	= 'Payment failed. '.$message;
	}
	$order->storeTrans($transData);
}
# Set to true to use HTML meta refresh, instead php header redirect
$use_html_meta_refresh = true;