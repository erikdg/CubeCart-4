<!-- BEGIN: index -->
<!-- BEGIN: welcome_note -->
<div id="Announcement"><!--start Announcement-->
<div class="Title"><h1>{HOME_TITLE}</h1></div>
<p>{HOME_CONTENT}</p>
</div><!--close Announcement -->
<!-- END: welcome_note -->

<!-- BEGIN: latest_prods -->
<div id="LatestProducts">
<div class="Title"><h2>{LANG_LATEST_PRODUCTS}</h2></div>
<div id="Inner"><!--start Inner-->
<!-- BEGIN: repeat_prods -->
<div class="LPBox">
<div class="LPImage"><a href="index.php?_a=viewProd&amp;productId={VAL_PRODUCT_ID}" title="{VAL_PRODUCT_NAME}"><img src="{VAL_IMG_SRC}" alt="{VAL_PRODUCT_NAME}" border="0" /></a></div>

<div class="LPInfo">
<div class="LPName"><a href="index.php?_a=viewProd&amp;productId={VAL_PRODUCT_ID}" title="{VAL_PRODUCT_NAME}">{VAL_PRODUCT_NAME}</a></div>
<div class="LPPrice">{TXT_PRICE} {TXT_SALE_PRICE}</div>

</div><!--close LPInfo-->
</div><!--close LPBox-->
<!-- END: repeat_prods -->
<br clear="all" />
</div><!--close Inner-->
</div><!--close LatestProducts-->
<!-- END: latest_prods -->
<br clear="all" />
<!-- END: index -->
<span class="SalePrice">{TXT_SALE_PRICE}</span>
<div id="WelcomeTitle"><h1>{HOME_TITLE}</h1></div>