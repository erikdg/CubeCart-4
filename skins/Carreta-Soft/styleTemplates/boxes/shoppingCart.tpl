<!-- BEGIN: shopping_cart -->
<div class="LeftBox">
<div style="border-top:5px solid #ffffff; border-bottom:5px solid #ffffff; padding-bottom: 15px;">
<div class="subHeading"><h3><a href="index.php?_g=co&amp;_a={CART_STEP}" title="View Shopping Basket">{LANG_SHOPPING_CART_TITLE}</a></h3></div>
<!-- BEGIN: contents_false -->
<div class="BasketProduct">
<p>{LANG_CART_EMPTY}</p>
</div><!--close BasketProduct-->
<!-- END: contents_false -->
<!-- BEGIN: contents_true -->
<div class="BasketProduct">
<p><span>{PRODUCT_PRICE}</span><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" title="{VAL_PRODUCT_NAME}">{VAL_NO_PRODUCT} x {VAL_PRODUCT_NAME}</a> ~</p>
</div>
<!-- END: contents_true -->

<div id="BasketItems"><span class="TotalItems">{VAL_CART_ITEMS}</span>{LANG_ITEMS_IN_CART}</div>
<div id="BasketTotal"><span class="TotalPrice">{VAL_CART_TOTAL}</span>{LANG_TOTAL_CART_PRICE}</div>

<!-- BEGIN: view_cart -->

<div class="Button">
  <div align="center"><a href="index.php?_g=co&amp;_a={CART_STEP}" class="txtviewCart" id="flashBasket">{LANG_VIEW_CART}</a></div>
</div>

<!-- END: view_cart -->
</div>
</div><!-- close LeftBox -->
<!-- END: shopping_cart -->
