<!-- BEGIN: index -->
<div class="txtContentTitle">{HOME_TITLE}</div>
<!-- BEGIN: welcome_note -->
<div class="boxContent">
	<div>
	{HOME_CONTENT}
	</div>
</div>
<!-- END: welcome_note -->
<!-- BEGIN: latest_prods -->
	<div class="txtContentTitle">{LANG_LATEST_PRODUCTS}</div>
	<div class="boxContent">
		<div style="text-align:justify; margin-top: 10px;">
		<!-- BEGIN: repeat_prods -->
			<div class="latestProds">
				<a href="index.php?_a=viewProd&amp;productId={VAL_PRODUCT_ID}"><img src="{VAL_IMG_SRC}" alt="{VAL_PRODUCT_NAME}" border="0" title="{VAL_PRODUCT_NAME}" /></a>
				<br />
				<a href="index.php?_a=viewProd&amp;productId={VAL_PRODUCT_ID}" class="txtDefault">{VAL_PRODUCT_NAME}</a>
				<br /> 
				<span class="txtPrice">{TXT_PRICE}</span> <span class="txtSale">{TXT_SALE_PRICE}</span>
			</div>
		<!-- END: repeat_prods -->
		<br clear="all" />
		</div>
		<br clear="all" />
		
		
	</div>
<!-- END: latest_prods -->
<!-- END: index -->