<!-- BEGIN: view_orders -->
<div id="ContentBox">
<div class="Title"><h1>{LANG_YOUR_VIEW_ORDERS}</h1></div>
<!-- BEGIN: session_true -->
<!-- BEGIN: orders_true -->
<p>{LANG_ORDER_LIST}</p>

<table width="100%" border="0" cellpadding="3" cellspacing="0" id="ViewOrders">
<tr>
<td align="center" class="tdcartTitle">{LANG_ORDER_NO}</td>
<td align="center" class="tdcartTitle">{LANG_STATUS}</td>
<td align="center" class="tdcartTitle">{LANG_DATE_TIME}</td>
<td align="center" class="tdcartTitle">{LANG_ACTION}</td>
</tr>
<!-- BEGIN: repeat_orders -->
<tr>
<td align="center" class="{TD_CART_CLASS}"><a href="index.php?_g=co&amp;_a=viewOrder&amp;cart_order_id={DATA.cart_order_id}" title="{DATA.cart_order_id}">{DATA.cart_order_id}</a></td>
<td align="center" class="{TD_CART_CLASS}">{VAL_STATE}</td>
<td align="center" class="{TD_CART_CLASS}">{VAL_DATE_TIME}</td>
<td align="center" class="{TD_CART_CLASS}"><a href="index.php?_g=co&amp;_a=viewOrder&amp;cart_order_id={DATA.cart_order_id}" title="{LANG_VIEW_ORDER}">{LANG_VIEW_ORDER}</a>
<!-- BEGIN: make_payment -->
<br /><a href="index.php?_g=co&amp;_a=step3&amp;cart_order_id={DATA.cart_order_id}" title="{LANG_COMPLETE_PAYMENT}">{LANG_COMPLETE_PAYMENT}</a>
<!-- END: make_payment -->
</td>
</tr>
<!-- END: repeat_orders -->
</table>

<div id="ViewOrdersInfo">
<ol>
<!-- BEGIN: repeat_status --><li><strong>{LANG_ORDER_STATUS}</strong> - {LANG_ORDER_STATUS_DESC}</li><!-- END: repeat_status -->
</ol>
</div>
<!-- END: orders_true -->

<!-- BEGIN: orders_false -->
<p>{LANG_NO_ORDERS}</p>
<!-- END: orders_false -->
<!-- END: session_true -->
<!-- BEGIN: session_false -->
<p>{LANG_LOGIN_REQUIRED}</p>
<!-- END: session_false -->
</div>
<!-- END: view_orders -->