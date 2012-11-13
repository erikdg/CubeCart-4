<?php
if (isset($_POST['transaction_id']) && !empty($_POST['transaction_id']) && isset($_POST['status'])) {
	
	function string_to_ascii($string) {
    	$ascii = null;
    	for ($i = 0; $i < strlen($string); $i++) {
      		$ascii += ord($string[$i]);
      	}
    	return($ascii);
    }
	if (isset($module['secret'])) {
		$md5_string = 	$_POST['merchant_id'] . 
						$_POST['transaction_id'] .  
						strtoupper(md5($module['secret'])) . 
						$_POST['mb_amount'] . 
						$_POST['mb_currency'] . 
						$_POST['status'];
		$hash = strtoupper(md5(string_to_ascii($md5_string)));
		if($hash === $_POST['md5sig']){
		$proceed = true;
		} else {
			$transData['status'] = 'MD5 signature missmatch. Please check your secret word.';
			$proceed = false;
		}
	} else {
		$proceed	= true;
	}
	
	if ($proceed) {
		switch ((int)$_POST['status']) {
			case '0':	## Pending
				$order->orderStatus(1, $_POST['transaction_id']);
				$transData['status'] = 'Pending';
				break;
			case '2':	## Processed
				$order->orderStatus(2, $_POST['transaction_id']);
				$transData['status'] = 'Processed';
				break;
			case '-2':	## Failed
				$order->orderStatus(4, $_POST['transaction_id']);
				$transData['status'] = 'Failed';
				break;
			case '-1':	## Cancelled
				$order->orderStatus(6, $_POST['transaction_id']);
				$transData['status'] = 'Cancelled';
				break;
		}
	}
	
	$transData['customer_id'] = $order->orderSum["customer_id"];
	$transData['gateway'] = 'Moneybookers';
	$transData['trans_id'] = $_POST['mb_transaction_id'];
	$transData['order_id'] = $_POST['transaction_id'];
	$transData['amount'] = $_POST['mb_amount'];
	$order->storeTrans($transData);

}
?>