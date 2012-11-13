<!-- BEGIN: session_page -->
<div class="Title"><h1>{LANG_LOGIN_TITLE}</h1></div>
<div style="text-align: center; height: 25px;">
	<div class="cartProgress">
	{LANG_CART} --- <span class="txtcartProgressCurrent">{LANG_CHECKOUT}</span> --- {LANG_PAYMENT} --- {LANG_COMPLETE}
	</div>
</div>
<!-- BEGIN: cart_true -->
<p>{LANG_LOGIN_BELOW}</p>
<form action="index.php?_a=login&amp;redir={VAL_SELF}" target="_self" method="post">
<table border="0" cellspacing="0" cellpadding="3">
<tr>
<td align="right"><strong>{LANG_USERNAME}</strong></td>
<td><input type="text" name="username" class="textbox" value="{VAL_USERNAME}" /></td>
</tr>
							
<tr>
<td align="right"><strong>{LANG_PASSWORD}</strong></td>
<td><input type="password" autocomplete="off" name="password" class="textbox" /></td>
</tr>
							
<tr>
<td align="right">{LANG_REMEMBER}</td>
<td><input name="remember" type="checkbox" value="1" {CHECKBOX_STATUS} /></td>
</tr>
							
</table>
<div id="ProdBuyLogin">
<div class="HeadingHalf"><a href="index.php?_a=forgotPass" class="txtLinkPass" title="{LANG_FORGOT_PASS}" style="padding-top:15px;">{LANG_FORGOT_PASS}</a></div><input name="submit" type="submit" value="{TXT_LOGIN}" class="submit" />
</div>
</form>
		
<div class="InfoBox One">
<div id="ProdBuy">
<div class="HeadingHalf">{LANG_EXPRESS_REGISTER}</div><a href="index.php?_g=co&amp;_a=reg&amp;co=1" title="Register Account" class="txtButton">{LANG_REGISTER_BUTN}</a>
</div>
<p>{LANG_CONT_REGISTER}</p>
</div>

<div class="InfoBox Two">
<div id="ProdBuy">
<div class="HeadingHalf">{LANG_CONT_SHOPPING}</div><a href="index.php" class="txtButton" title="Continue Shopping">{LANG_CONT_SHOPPING_BTN}</a>
</div>
<p>{LANG_CONT_SHOPPING_DESC}</p>
</div>
<!-- END: cart_true -->
<!-- BEGIN: cart_false -->
<p>{LANG_CART_EMPTY}</p>
<!-- END: cart_false -->
<!-- END: session_page -->

<div class="Title"><h2>{LANG_EXPRESS_REGISTER}</h2></div>
<div class="Title"><h2>{LANG_CONT_SHOPPING}</h2></div>