<!-- BEGIN: view_cat -->
<div class="txtContentTitle">{TXT_CAT_TITLE}</div>
<div class="boxContent">
<div id="BreadCrumb"><strong>{LANG_DIR_LOC}</strong> <a href="index.php" title="{LANG_HOME}"><img src="skins/{VAL_SKIN}/styleImages/icons/home.gif" alt="{LANG_HOME}" border="0" /></a>  {CURRENT_LOC}</div>
<!-- BEGIN: cat_desc -->
<p>{TXT_CAT_DESC}</p>
<!-- END: cat_desc -->
<!-- BEGIN: sub_cats -->
<div id="subCats">
	<!-- BEGIN: sub_cats_loop -->
	<div class="subCat">
		<a href="index.php?_a=viewCat&amp;catId={TXT_LINK_CATID}" class="txtDefault"><img src="{IMG_CATEGORY}" alt="{TXT_CATEGORY}" border="0" title="{TXT_CATEGORY}" /></a><br />
		<a href="index.php?_a=viewCat&amp;catId={TXT_LINK_CATID}" class="txtDefault">{TXT_CATEGORY}</a> ({NO_PRODUCTS})
	</div>
	<!-- END: sub_cats_loop -->
</div>
<!-- END: sub_cats -->
<br clear="left" />
<!-- BEGIN: cat_img -->
<img src="{IMG_CURENT_CATEGORY}" alt="{TXT_CURENT_CATEGORY}" border="0" title="{TXT_CURENT_CATEGORY}" />
<!-- END: cat_img -->
<div><strong>{LANG_CURRENT_DIR}</strong> {CURRENT_DIR}</div>
<div class="pagination">{PAGINATION}</div>
<!-- BEGIN: productTable -->
<div style="text-align: right; margin: 0px 7px;">
<select class="dropDown" id="sortMethod" class="textbox">
  <option value="{SORT_NAME}"{SORT_NAME_SELECTED}>{LANG_NAME}</option>
  <option value="{SORT_PRICE}"{SORT_PRICE_SELECTED}>{LANG_PRICE}</option>
</select>
<input type="button" class="submit" value="{LANG_SORT}" onclick="goUrl('sortMethod');" />
</div>
<table border="0" width="100%" cellspacing="0" cellpadding="3" class="tblList">
  <tr>
    <td align="center" class="tdListTitle"><strong>{LANG_IMAGE}</strong></td>
    <td class="tdListTitle"><a href="{SORT_NAME}" class="sortLink"><strong>{LANG_NAME}</strong></a> {SORT_ICON}</td>
    <td align="center" class="tdListTitle"><a href="{SORT_PRICE}" class="sortLink"><strong>{LANG_PRICE}</strong></a> {SORT_ICON}</td>
	<td class="tdListTitle">&nbsp;</td>
  </tr>
  <!-- BEGIN: products -->
  <tr>
    <td align="center" class="{CLASS}"><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" target="_self"><img src="{SRC_PROD_THUMB}" alt="{TXT_TITLE}" border="0" title="{TXT_TITLE}" /></a></td>
    <td valign="top" class="{CLASS}"><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="txtDefault"><strong>{TXT_TITLE}</strong></a><br />
	{TXT_DESC}<div class="txtOutOfStock">{TXT_OUTOFSTOCK}</div></td>
	<td align="center" class="{CLASS}">{TXT_PRICE}
    <div class="txtSale">{TXT_SALE_PRICE}</div></td>
    <td align="right" nowrap='nowrap' class="{CLASS}">
	<form action="{CURRENT_URL}" method="post" name="prod{PRODUCT_ID}">
	<!-- BEGIN: buy_btn -->
	<input type="hidden" name="add" value="{PRODUCT_ID}" />
	<input type="hidden" name="quan" value="1" /><a href="javascript:submitDoc('prod{PRODUCT_ID}');" target="_self" class="txtButton">{BTN_BUY}</a><!-- END: buy_btn --> <a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="txtButton">{BTN_MORE}</a></form></td>
</tr>
<!-- END: products -->
</table>
<!-- END: productTable -->
<!-- BEGIN: noProducts -->
<div>{TXT_NO_PRODUCTS}</div>
<!-- END: noProducts -->

<div class="pagination">{PAGINATION}</div>
</div>
<!-- END: view_cat -->