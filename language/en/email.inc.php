<?php
$lv = !$langBully ?  "lang" : "bully";
${$lv}['email'] = array(
'coupon_subject' => "Your gift certificate!",

'coupon_body' => "Dear {RECIP_NAME},

{SENDER_NAME} has sent you a gift voucher worth {AMOUNT} which can be redeemed against any goods in our store! 

~~~~~~~~~~~~~~~~~~~~~~~~~~
Message: (from {SENDER_NAME} <{SENDER_EMAIL}>)
{MESSAGE}
~~~~~~~~~~~~~~~~~~~~~~~~~~
Voucher Code: {COUPON}
~~~~~~~~~~~~~~~~~~~~~~~~~~

Why not spend it now?

Goto: {STORE_URL}",

'downloads_body' => "Dear {RECIP_NAME},

Thank you for your order no: {ORDER_ID} placed on {ORDER_DATE}

Below are the links you need to access the digital products you have ordered.

IMPORTANT these links will expire on {EXPIRE_DATE} and you have {DOWNLOAD_ATTEMPTS} attempts to download them. If you have any problems please contact us stating your order number.

~~~~~~~~~~~~~~~~~~~~~~~~~~\n",
'downloads_body_2' => "\n{PRODUCT_NAME}
DOWNLOAD LINK:
{DOWNLOAD_URL}\n
~~~~~~~~~~~~~~~~~~~~~~~~~~\n\n",
'downloads_subject' => "Downloads Access: {ORDER_ID}",
'order_breakdown_1' => "Dear {RECIP_NAME},

Thank you for your order no: {ORDER_ID} placed on {ORDER_DATE}

The transaction was successful and we will ship your goods at the first possible opportunity (if applicable).

~~~~~~~~~~~~~~~~~~~~~~~~~~
Name: {INVOICE_NAME}
Subtotal: {SUBTOTAL}
Postage & Packaging: {SHIPPING_COST}
{TAX_COST}
Grand Total: {GRAND_TOTAL}
~~~~~~~~~~~~~~~~~~~~~~~~~~

Invoice Address:
{INVOICE_NAME}
{INVOICE_COMPANY}
{INVOICE_ADD_1}
{INVOICE_ADD_2}
{INVOICE_CITY}
{INVOICE_REGION}
{INVOICE_POSTCODE}
{INVOICE_COUNTRY}

Delivery Address:
{DELIVERY_NAME}
{DELIVERY_COMPANY}
{DELIVERY_ADD_1}
{DELIVERY_ADD_2}
{DELIVERY_CITY}
{DELIVERY_REGION}
{DELIVERY_POSTCODE}
{DELIVERY_COUNTRY}

Payment Method: {PAYMENT_METHOD}
Shipping Method: {DELIVERY_METHOD}\n",
'order_breakdown_2' => "\nYour comments: {CUSTOMER_COMMENTS}\n",
'order_breakdown_3' => "\n~~~~~~~~~~~~~~~~~~~~~~~~~~\n

Order Inventory:\n",
'order_breakdown_4' =>"Product: {PRODUCT_NAME}\n",
'order_breakdown_5' => "Options: {PRODUCT_OPTIONS}\n",
'order_breakdown_6' => "Quantity: {PRODUCT_QUANTITY}
Product Code: {PRODUCT_CODE}
Price: {PRODUCT_PRICE}\n\n",

'order_breakdown_subject' => "Order Complete #{ORDER_ID}",
'admin_pending_order_subject' => "Pending Order #{ORDER_ID}",
'admin_pending_order_body' => "{CUSTOMER_NAME}, has recently placed order #{ORDER_ID}. This order is pending payment and as a result it should not be fulfilled until you have received the funds in full. Please follow the link below to view this order:

{ADMIN_ORDER_URL}

Logged IP Address: {SENDER_ID}",
'order_acknowledgement_subject' => "Order Acknowledgement #{ORDER_ID}",
'order_acknowledgement_body' => "Dear {CUSTOMER_NAME},

This email confirms that you have successfully placed a new order #{ORDER_ID}. Once payment has been received we will issue your goods at the first possible opportunity.

You can view the status of your order at any time, via our website by following the link below: 

{ORDER_URL}

Please feel free to contact a member of staff if you have any questions or problems with your purchase.",
'reset_password_body' => "Dear {RECIP_NAME},

Your password has now been reset. Please find your new access details below:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Email Address: {EMAIL}
Password: {PASSWORD}
~~~~~~~~~~~~~~~~~~~~~~~~~~
To login, please follow the link below:
{STORE_URL}
~~~~~~~~~~~~~~~~~~~~~~~~~~

Requester's IP Address: {SENDER_IP}",
'reset_password_subject' => "New Password",
'profile_mofified_body' => "Dear {CUSTOMER_NAME},

This email has been sent to confirm that your personal information has been updated successfully. If you feel that your account has been updated by someone other than yourself please contact a member of staff immediately.\n\n

This email was sent from {STORE_URL}\n

Visitor's IP Address: {SENDER_IP}",
'profile_mofified_subject' => "Personal Info Updated",
'new_reg_subject' => "Your Account Details",
'new_reg_body' => "Dear {CUSTOMER_NAME},

For your records the following account has been setup so that you can login to our site. Once logged in you can view the status of your orders, make repeat orders efficiently and amend your profile.

Your access details are:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Email:	{EMAIL}
Password:	{PASSWORD}
~~~~~~~~~~~~~~~~~~~~~~~~~~

This email was sent from {STORE_URL}

Registration IP Address: {SENDER_IP}",
'tellafriend_body' => "Dear {RECIP_NAME},

{MESSAGE}

~~~~~~~~~~~~~~~~~~~~~~~~~~
To view this product please follow the link below:
{PRODUCT_URL}
~~~~~~~~~~~~~~~~~~~~~~~~~~

This email was sent from {STORE_URL}

Sender's IP Address: {SENDER_IP}",
'tellafriend_subject' => "Product Recommended by {SENDER_NAME}",
'fraud_subject' => "Order {ORDER_ID} Failed Fraud Review",
'fraud_body' => "Dear {RECIP_NAME},

We regret to inform you that payment for your order {ORDER_ID} did not pass security checks performed by either our staff or bank. If you have any further questions concerning this please refer to your orders notes by following the link below or contact a member of staff quoting your order number.

{ORDER_URL_PATH}

Typical reasons for this:
- The country selected may not have matched that of where the card was issued. This is a common accident.
- The security code which can be found on the back of the card may have been entered incorrectly.
- You may be purchasing the order in a country other that the one where your card was issued.

If you wish to create a new order please feel free to do so. No card or account has been charged for this order.

This email was sent from {STORE_URL}",


'payment_complete_subject' => "Payment Received for {ORDER_ID}",
'payment_complete_body' => "Dear {RECIP_NAME},

We would just like to inform you that payment for order number {ORDER_ID} has cleared and you should receive your goods shortly.

This email was sent from {STORE_URL}",


'payment_cancelled_subject' => "Order {ORDER_ID} Cancelled",
'payment_cancelled_body' => "Dear {RECIP_NAME},

Order number {ORDER_ID} has been cancelled. More information concerning this may be in the order notes which can be found by following this link:

{ORDER_URL_PATH}

N.B. Orders can be cancelled by the customer during purchase or staff member. If you wish to raise a new order please feel free to do so.

This email was sent from {STORE_URL}",
'admin_reset_pass_body' => "Dear {RECIP_NAME},

You, or somebody pretending to be you has requested your password to be reset.

Your new access details are:

~~~~~~~~~~~~~~~~~~~~~~~~~~
Username: {USERNAME}
Password: {PASSWORD}
~~~~~~~~~~~~~~~~~~~~~~~~~~

This email was sent from {STORE_URL}\n

Requester's IP Address: {SENDER_IP}",
'admin_reset_pass_subject' => "New Admin Access Details",
'new_review_subject' => "New Product Review/Comment",
'new_review_body' => "Authors Name: {AUTHOR_NAME}
Authors Email: {AUTHOR_EMAIL}
Authors IP Address: {SENDER_ID}
Product Reviewed: {PRODUCT_NAME}
Rating: {RATING}
Review Title: {REVIEW_TITLE}
Review Copy:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
{REVIEW_COPY}
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Approve: {APPROVE_URL}
Decline: {DECLINE_URL}"
);
?>