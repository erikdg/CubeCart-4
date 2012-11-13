<?php
$lv = !$langBully ?  "lang" : "bully";
${$lv}['glob'] = array(
'orderState_1' => "Pending (New Order)",
'orderState_2' => "Processing (See order notes)",
'orderState_3' => "Order Complete &amp; Dispatched",
'orderState_4' => "Declined (See order notes)",
'orderState_5' => "Failed Fraud Review",
'orderState_6' => "Cancelled",
'orderState_1_desc' => "Order has been created and staff members are awaiting payment before any further action will be taken. This order may be automatically cancelled if payment has not been made by a specific time scale.",
'orderState_2_desc' => "Payment may or may have not cleared or the order hasn't been dealt with yet.",
'orderState_3_desc' => "Order has been paid for and dispatched. Goods should arrive shortly. Tracking information may be available.",
'orderState_4_desc' => "Order has been declined. More information may be available in the order notes.",
'orderState_5_desc' => "Payment for the order has failed external/internal fraud review.",
'orderState_6_desc' => "Order has been cancelled. Reasons for order cancellation should show in your order notes. Please note that new orders which have not been paid for within a certain time scale may automatically be cancelled."
);
?>