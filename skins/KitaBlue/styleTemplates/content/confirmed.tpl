<!-- BEGIN: confirmation -->
<script type="text/javascript">
if (top.location!= self.location) {
	top.location = self.location.href
}
</script>
<div class="txtContentTitle">{LANG_CONFIRMATION_SCREEN}</div>
<div class="boxContent">
	<div>
		<div style="text-align: center; height: 25px;">
			<div class="cartProgress">
			{LANG_CART} --- {LANG_CHECKOUT} --- {LANG_PAYMENT} --- <span class='txtcartProgressCurrent'>{LANG_COMPLETE}</span>
			</div>
		</div>
		<!-- BEGIN: order_success -->
		<p>{LANG_ORDER_SUCCESSFUL}</p>
		<!-- END: order_success -->
		
		<!-- BEGIN: order_processing -->
		<p>{LANG_ORDER_PROCESSING}</p>
		<!-- END: order_processing -->
		
		<!-- BEGIN: order_failed -->
		<p>{LANG_ORDER_FAILED}</p>
		<p>{LANG_ORDER_RETRY}</p>
		<div style="text-align: center; padding: 10px;"><a href="index.php?_g=co&amp;_a=step3&amp;cart_order_id={VAL_CART_ORDER_ID}"  class="txtButton">{LANG_RETRY_BUTTON}</a></div>
		<!-- END: order_failed -->
	</div>		
</div>
<!-- END: confirmation -->