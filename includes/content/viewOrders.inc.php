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
|	viewOrders.inc.php
|   ========================================
|	Displays the Customers Orders
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file

$lang = getLang('includes'.CC_DS.'content'.CC_DS.'viewOrders.inc.php');
$lang = getLang('orders.inc.php');

// query database

$view_orders=new XTemplate ('content'.CC_DS.'viewOrders.tpl');

	$view_orders->assign('LANG_YOUR_VIEW_ORDERS',$lang['viewOrders']['your_orders']);

	$orders = $db->select('SELECT `status`, `cart_order_id`, `time`, `courier_tracking` FROM '.$glob['dbprefix'].'CubeCart_order_sum WHERE `customer_id` = '.$db->mySQLsafe($cc_session->ccUserData['customer_id']).' ORDER BY `time` DESC');

	if ($orders) {

		$view_orders->assign('LANG_ORDER_LIST',$lang['viewOrders']['orders_listed_below']);

		$view_orders->assign('LANG_ORDER_NO',$lang['viewOrders']['order_no']);
		$view_orders->assign('LANG_STATUS',$lang['viewOrders']['status']);
		$view_orders->assign('LANG_DATE_TIME',$lang['viewOrders']['date_time']);
		$view_orders->assign('LANG_ACTION',$lang['viewOrders']['action']);
		$view_orders->assign('LANG_VIEW_ORDER',$lang['viewOrders']['view']);
		$view_orders->assign('LANG_COURIER_TRACKING', $lang['viewOrders']['courier_tracking']);

		for($i = 0, $maxi = count($orders); $i < $maxi; ++$i)
		{

			$state = $orders[$i]['status'];
			$orders[$i]['state'] =  $lang['glob']['orderState_'.$state];

			$view_orders->assign('TD_CART_CLASS',cellColor($i, 'tdcartEven', 'tdcartOdd'));
			$view_orders->assign('DATA',$orders[$i]);
			$view_orders->assign('VAL_STATE',$lang['glob']['orderState_'.$orders[$i]['status']]);
			$view_orders->assign('VAL_DATE_TIME',formatTime($orders[$i]['time']));

			if (!empty($orders[$i]['courier_tracking'])) {
				$view_orders->assign('TRACKING_URL', $orders[$i]['courier_tracking']);
				$view_orders->parse('view_orders.session_true.orders_true.repeat_orders.courier_tracking');
			}

			if (in_array($orders[$i]['status'], array(1,4))) {
				$view_orders->assign('LANG_COMPLETE_PAYMENT',$lang['viewOrders']['complete_payment']);
				$view_orders->parse('view_orders.session_true.orders_true.repeat_orders.make_payment');

			}

			$view_orders->parse('view_orders.session_true.orders_true.repeat_orders');

		}

		for ($i=1; $i<=6; ++$i) {
			$view_orders->assign('LANG_ORDER_STATUS',$lang['glob']['orderState_'.$i]);
			$view_orders->assign('LANG_ORDER_STATUS_DESC',$lang['glob']['orderState_'.$i.'_desc']);
			$view_orders->parse('view_orders.session_true.orders_true.repeat_status');

		}
		$view_orders->parse('view_orders.session_true.orders_true');
	} else {
		$view_orders->assign('LANG_NO_ORDERS',$lang['viewOrders']['no_orders']);
		$view_orders->parse('view_orders.session_true.orders_false');
	}


	$view_orders->assign('LANG_LOGIN_REQUIRED',$lang['viewOrders']['login_required']);

	if($cc_session->ccUserData['customer_id']>0) $view_orders->parse('view_orders.session_true');

	else $view_orders->parse('view_orders.session_false');

	$view_orders->parse('view_orders');

$page_content = $view_orders->text('view_orders');
?>