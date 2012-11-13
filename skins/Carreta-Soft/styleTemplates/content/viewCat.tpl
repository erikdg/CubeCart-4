<!-- BEGIN: view_cat -->
<div id="BreadCrumb"><strong>{LANG_DIR_LOC}</strong> <a href="index.php" title="{LANG_HOME}"><img src="skins/{VAL_SKIN}/styleImages/icons/home.gif" alt="{LANG_HOME}" border="0" /></a>  {CURRENT_LOC}</div>

<div class="Title"><h1>{TXT_CAT_TITLE}</h1></div>

<!-- BEGIN: cat_desc -->
<div id="CatDescription">
<p>{TXT_CAT_DESC}</p>
</div>
<!-- END: cat_desc -->

<!-- BEGIN: sub_cats -->
<div id="SubCategories">
<!-- BEGIN: sub_cats_loop -->
<div class="subCat"><a href="index.php?_a=viewCat&amp;catId={TXT_LINK_CATID}" title="{TXT_CATEGORY}"><img src="{IMG_CATEGORY}" alt="{TXT_CATEGORY}" border="0" /></a><br />
<a href="index.php?_a=viewCat&amp;catId={TXT_LINK_CATID}" title="{TXT_CATEGORY}">{TXT_CATEGORY}</a> ({NO_PRODUCTS})</div>
<!-- END: sub_cats_loop -->
</div>
<br clear="all" />
<!-- END: sub_cats -->

<!-- BEGIN: cat_img -->
<div class="CurrentCatImage"><img src="{IMG_CURENT_CATEGORY}" alt="{TXT_CURENT_CATEGORY}" border="0" /></div>
<!-- END: cat_img -->

<!-- BEGIN: pagination_top -->
<div class="pagination">{PAGINATION}</div>
<!-- END: pagination_top -->

<!-- BEGIN: productTable -->
<div style="text-align: right; margin: 0px 7px;">
<select class="dropDown" id="sortMethod" class="textbox">
  <option value="{SORT_NAME}"{SORT_NAME_SELECTED}>{LANG_NAME}</option>
  <option value="{SORT_PRICE}"{SORT_PRICE_SELECTED}>{LANG_PRICE}</option>
</select>
<input type="button" class="txtButton" value="{LANG_SORT}" onclick="goUrl('sortMethod');" />
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="0" class="catview">
<!-- BEGIN: products -->
<tr>
<td align="center" class="catsep"><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" title="{TXT_TITLE}" target="_self"><img src="{SRC_PROD_THUMB}" alt="{TXT_TITLE}" border="0" class="catimage" /></a></td>

<td class="catsep"><h2><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" title="{TXT_TITLE}" target="_self" class="txtDefault"><strong>{TXT_TITLE}</strong></a></h2>
<p>{TXT_DESC}<br /><span class="txtOutOfStock" style="float: right;">{TXT_OUTOFSTOCK}</span></p>
<p class="Price">{TXT_PRICE} <span class="Sale">{TXT_SALE_PRICE}</span></p>
</td>

<td width="100" class="catsep" style="border-right:none;"><form action="{CURRENT_URL}" style="text-align:center;" method="post" name="prod{PRODUCT_ID}">
<div class="Button"><!-- BEGIN: buy_btn --><input type="hidden" name="add" value="{PRODUCT_ID}" /><input type="hidden" name="quan" value="1" /><a href="javascript:submitDoc('prod{PRODUCT_ID}');" target="_self" title="{BTN_BUY}" class="txtButton">{BTN_BUY}</a><!-- END: buy_btn --><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" target="_self" title="{BTN_MORE}" class="txtButton">{BTN_MORE}</a></div>
</form></td>
</tr>
<!-- END: products -->
</table>
<!-- END: productTable -->

<!-- BEGIN: noProducts -->
<p>{TXT_NO_PRODUCTS}</p>
<!-- END: noProducts -->

<!-- BEGIN: pagination_bot -->
<div class="pagination">{PAGINATION}</div>
<!-- END: pagination_bot -->
<!-- END: view_cat -->

<!-- BEGIN: buy_btn -->
<form action="{CURRENT_URL}" style="text-align:center;" method="post" name="prod{PRODUCT_ID}">
<input type="hidden" name="add" value="{PRODUCT_ID}" />
<input type="hidden" name="quan" value="1" />
<a href="javascript:submitDoc('prod{PRODUCT_ID}');" target="_self" title="Buy Now" class="Button">{BTN_BUY}</a>
</form>
<!-- END: buy_btn -->