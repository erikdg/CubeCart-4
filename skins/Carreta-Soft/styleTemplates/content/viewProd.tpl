<!-- BEGIN: view_prod -->
<div id="SingleProd">
<!-- BEGIN: prod_true -->
<div id="BreadCrumb"><strong>{LANG_DIR_LOC}</strong> <a href="index.php" title="{LANG_HOME}"><img src="skins/{VAL_SKIN}/styleImages/icons/home.gif" alt="{LANG_HOME}" border="0" /></a> {CURRENT_DIR}</div>

<div class="Title"><h1>{TXT_PRODTITLE}</h1></div>
<!-- BEGIN: opts_notice -->
<p class="txtError">{LANG_OPTS_NOTICE}</p>
<!-- END: opts_notice -->

<form action="{CURRENT_URL}" method="post" name="addtobasket" target="_self">
<div align="center"><img src="{IMG_SRC}" alt="{TXT_PRODTITLE}" name="MainProdImage" border="0" align="middle" id="MainProdImage" /></div>
<!-- BEGIN: popup_gallery -->
<div id="MoreImages"><a href="javascript:;" onclick="openPopUp('index.php?_g=ex&amp;_a=prodImages&amp;productId={PRODUCT_ID}', 'images', 548, 455, 0); return false;" title="{LANG_MORE_IMAGES}">{LANG_MORE_IMAGES}</a></div>
<!-- END: popup_gallery -->

<!-- BEGIN: image_gallery -->
<div id="LightBoxGallery">
<p>{IMAGE_GALLERY}</p>
<!-- BEGIN: img_repeat -->
<div class="LightBox"><a href="{VALUE_IMG_SRC}" rel="lightbox[imageset]"><img src="{VALUE_THUMB_SRC}" width="{VALUE_THUMB_WIDTH}" border="0" alt="/" class="LightBoxImage" /></a></div>
<!-- END: img_repeat -->
</div><!--close LightBoxGallery-->
<!-- END: image_gallery -->
<br clear="all" />

<div id="ProdDescription">
<h2>{LANG_PRODINFO}</h2>
{TXT_DESCRIPTION}
</div>

<div id="ProdInfo">
<div id="ProdPrice">{LANG_PRICE} <br />
{TXT_PRICE_VIEW} <span class="Sale">{TXT_SALE_PRICE_VIEW}</span></div>

<div id="ProdReviews">
<!-- BEGIN: reviews_false -->
<p><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}&amp;review=write#write_review" target="_self" title="{LANG_FIRST_TO_REVIEW}">{LANG_FIRST_TO_REVIEW}</a></p>
<!-- END: reviews_false -->
<!-- BEGIN: reviews_true -->
<!-- BEGIN: review_stars -->
<img src="skins/{VAL_SKIN}/styleImages/icons/rating/{VAL_STAR}.gif" width="15" height="15" class="star" alt="" />
<!-- END: review_stars -->
<p><a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}&amp;review=read#read_review" target="_self" title="{LANG_BASED_ON_X_REVIEWS}">{LANG_BASED_ON_X_REVIEWS}</a></p>
<!-- END: reviews_true -->
</div><!--close ProdReviews-->

<div id="TellAFriend">
<ul>
<li class="nobullet"><img src="skins/{VAL_SKIN}/styleImages/icons/TellAFriend.jpg" alt="{LANG_TELLFRIEND}" />&nbsp;&nbsp;<a href="index.php?_a=tellafriend&amp;productId={PRODUCT_ID}" target="_self" title="{LANG_TELLFRIEND}">{LANG_TELLFRIEND}</a></li>
<!-- BEGIN: read_reviews -->
<li class="nobullet"><img src="skins/{VAL_SKIN}/styleImages/icons/ReadReview.jpg" alt="{LANG_READ_REVIEWS}" />&nbsp;&nbsp;<a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}&amp;review=read#read_review" target="_self" title="{LANG_READ_REVIEWS}">{LANG_READ_REVIEWS}</a></li>
<!-- END: read_reviews -->
<li class="nobullet"><img src="skins/{VAL_SKIN}/styleImages/icons/WriteReview.jpg" alt="{LANG_WRITE_REVIEWS}" />&nbsp;&nbsp;<a href="index.php?_a=viewProd&amp;productId={PRODUCT_ID}&amp;review=write#write_review" target="_self" title="{LANG_WRITE_REVIEWS}">{LANG_WRITE_REVIEWS}</a></li>
</ul>
</div><!--close TellAFriend-->
</div><!-- close ProdInfo-->

<br clear="all" />

<div id="ProdOptions">	
<!-- BEGIN: prod_opts -->
<p>{TXT_PROD_OPTIONS}</p>
<table border="0" cellspacing="0" cellpadding="3" id="Options">
<!-- BEGIN: repeat_options -->
<tr>
<td>{VAL_OPTS_NAME}</td>
<td><select name="productOptions[{VAL_OPTION_ID}]" class="OptionsStyle">
<!-- BEGIN: repeat_values -->
<option value="{VAL_ASSIGN_ID}">{VAL_VALUE_NAME}
<!-- BEGIN: repeat_price -->
({VAL_OPT_SIGN}{VAL_OPT_PRICE})
<!-- END: repeat_price -->
</option>
<!-- END: repeat_values -->
</select>
</td>
</tr><!-- END: repeat_options -->

<!-- BEGIN: text_opts -->
<tr>
<td valign="top">{VAL_OPTS_NAME}
<!-- BEGIN: repeat_price -->
({VAL_OPT_SIGN}{VAL_OPT_PRICE})
<!-- END: repeat_price -->
</td>
<td>
<!-- BEGIN: textbox -->
<input type="text" name="productOptions[{VAL_OPTION_ID}]" class="textbox"  />
<!-- END: textbox -->
<!-- BEGIN: textarea -->
<textarea name="productOptions[{VAL_OPTION_ID}]" class="textbox" cols="30" rows="4"></textarea>
<!-- END: textarea -->
</td>
</tr>
<!-- END: text_opts -->

</table><!-- END: prod_opts -->
</div><!--close ProdOptions -->

<div id="ProdLevels">
<p>{LANG_PRODCODE} {TXT_PRODCODE} </p>
<p>{TXT_INSTOCK} <span class="OutOfStock">{TXT_OUTOFSTOCK}</span></p>
</div>

<div id="ProdBuy">
<!-- BEGIN: buy_btn -->
{LANG_QUAN} <input name="quan" type="text" value="1" size="1" class="textbox" style="font-size:12px;text-align:center;" />
<a href="javascript:submitDoc('addtobasket');" title="{BTN_ADDBASKET}" class="txtButton">{BTN_ADDBASKET}</a>
<!-- END: buy_btn --> 
<input type="hidden" name="add" value="{PRODUCT_ID}" />
<!--close Purchase -->
</div></form>
<!-- close Product -->


<div id="RelatedProducts">	
<!-- BEGIN: related_products -->
<h3>{LANG_RELATED_PRODUCTS}</h3>
<div id="Inner">
<!-- BEGIN: repeat_prods -->
<div class="LPBox">
<div class="LPImage"><a href="index.php?_a=viewProd&amp;productId={VAL_PRODUCT_ID}" title="{VAL_PRODUCT_NAME}"><img src="{VAL_IMG_SRC}" alt="{VAL_PRODUCT_NAME}" border="0" /></a></div>

<div class="LPInfo">
<div class="LPName"><a href="index.php?_a=viewProd&amp;productId={VAL_PRODUCT_ID}" title="{VAL_PRODUCT_NAME}">{VAL_PRODUCT_NAME}</a></div>
<div class="LPPrice">{TXT_PRICE} <span class="Sale">{TXT_SALE_PRICE}</span></div>

</div><!--close LPInfo-->
</div><!--close LPBox-->
<!-- END: repeat_prods -->
</div><!--close Inner -->
<!-- END: related_products -->
</div><!--close RelatedProducts -->


<div id="CustomerReviews">
<!-- BEGIN: write_review -->
<form action="index.php?_a=viewProd&amp;review=write&amp;productId={PRODUCT_ID}#write_review" method="post" id="write_review">
<h3>{LANG_SUBMIT_REVIEW}</h3>

<!-- BEGIN: error -->
<p class="txtError">{VAL_ERROR}</p>
<!-- END: error -->

<!-- BEGIN: success -->
<p>{VAL_SUCCESS}</p>
<!-- END: success -->

<!-- BEGIN: form -->
<p>{LANG_SUBMIT_REVIEW_COMPLETE}</p>
<div class="SubmitReview">
<p class="right"><span>{LANG_REVIEW_TYPE}</span>
<select name="review[type]" style="width: 152px;" class="textbox">
<option value="0" onclick="findObj('rating_p').style.display = '';" {VAL_REV_TYPE_R_SELECTED}>{LANG_REVIEW}</option>
<option value="1" onclick="findObj('rating_p').style.display = 'none';" {VAL_REV_TYPE_C_SELECTED}>{LANG_COMMENT}</option>
</select> </p>

<p class="right" id="rating_p"><span>{LANG_RATING}</span> 
<img src="images/general/px.gif" name="star0" width="15" height="15" id="star0" onmouseover="stars(0,'{VAL_ROOT_REL}skins/{VAL_SKIN}/styleImages/icons/rating/');" style="cursor: pointer; cursor: hand;" alt="" />
<!-- BEGIN: review_stars -->
<img src="skins/{VAL_SKIN}/styleImages/icons/rating/{VAL_STAR}.gif" name="star{VAL_STAR_I}" width="15" height="15" id="star{VAL_STAR_I}" onmouseover="stars({VAL_STAR_I},'{VAL_ROOT_REL}skins/{VAL_SKIN}/styleImages/icons/rating/');" style="cursor: pointer; cursor: hand;" alt="" />	
<!-- END: review_stars --></p>
		
<!-- BEGIN: spambot -->
<p class="right"><span>{LANG_SPAMBOT}</span> {IMG_SPAMBOT} <br />
<input name="review[spambot]" type="text" class="textbox" style="width: 118px;" maxlength="5" /></p>
<!-- END: spambot -->

<!-- BEGIN: recaptcha -->
<p style="text-align:right;">
<strong style="float: left;">{LANG_SPAMBOT}</strong><br />
{FILE "content/recaptcha.tpl"}
</p>
<!-- END: recaptcha -->

<p class="right"><span>{LANG_NAME}</span> <input name="review[name]" type="text" style="width: 150px;" class="textbox" value="Your Name" onclick="this.value = ''" /></p>
<p class="right"><span>{LANG_EMAIL} {LANG_NOT_DISPLAYED}</span> <input name="review[email]" type="text" style="width: 150px;" class="textbox" value="{VAL_REV_EMAIL}" /></p>
<p class="right"><span>{LANG_TITLE}</span> <input name="review[title]" type="text" style="width: 150px;" class="textbox" value="{VAL_REV_TITLE}" /></p>
<p><strong style="float: left;">{LANG_DETAILS}</strong> <br /> <textarea name="review[review]" style="width:98%; margin:3px 0 0 3px;" rows="7" class="textbox">{VAL_REVIEW}</textarea></p>

<div class="BlueBg">
<div class="Button"><input name="ESC" type="hidden" value="{VAL_ESC}" /><input type="hidden" name="review[rating]" id="rating_val" value="{VAL_RATING}" /> 
<input name="submit" type="submit" value="{LANG_SUBMIT_REVIEW}" class="submit" /></div>
</div>
</div><!--close SubmitReview -->
<!-- END: form -->
</form>
<!-- END: write_review -->
</div><!-- close CustomerReviews -->

<div id="ReviewArchives">
<!-- BEGIN: read_review -->
<h3>{LANG_REVIEWS_AND_COMMENTS}</h3>
<div class="Pagnation right">{VAL_REVIEW_PAGINATION}</div>
<div class="ReviewEntry">
<!-- BEGIN: reviews_true -->
<div class="ReviewRatings">
<div style="float: right;"><!-- BEGIN: review_stars --><img src="skins/{VAL_SKIN}/styleImages/icons/rating/{VAL_REVIEW_STAR}.gif" width="15" height="15" alt="" />	<!-- END: review_stars --></div>
{LANG_TYPE} <div class="ReviewTitle">{VAL_REVIEW_TITLE}</div> 
</div><!--close ReviewRatings-->
	
<div class="ReviewContent">&quot;{VAL_REVIEW}&quot;</div>

<div class="ReviewedBy">
<div class="Date">{VAL_REVIEW_DATE}</div>
{LANG_BY} {VAL_REVIEW_NAME}	
</div><!--close ReviewedBy-->
<!-- END: reviews_true -->
</div><!--close ReviewEntry-->
<div class="Pagnation">{VAL_REVIEW_PAGINATION}</div>

<!-- BEGIN: reviews_false -->
<p>{LANG_NO_REVIEWS_MADE}</p>
<!-- END: reviews_false -->
<!-- END: read_review -->
</div><!-- close ReviewArchives -->
<!-- END: prod_true -->

<!-- BEGIN: prod_false -->
<p>{LANG_PRODUCT_EXPIRED}</p>
<!-- END: prod_false -->
</div>
<!-- END: view_prod -->


<span class="Sale">{TXT_SALE_PRICE}</span>

<span class="txtSale">{TXT_SALE_PRICE}</span>
<strong class="txtContentTitle" id="read_review">
{VAL_REV_NAME}