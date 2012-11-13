<!-- BEGIN: account -->
<div id="ContentBox">
<div class="Title"><h1>{LANG_YOUR_ACCOUNT}</h1></div>
<!-- BEGIN: session_true -->
<div id="Account">	
<ul>
<li><a href="index.php?_a=profile" title="{TXT_PERSONAL_INFO}">{TXT_PERSONAL_INFO}</a></li>
<li><a href="index.php?_g=co&amp;_a=viewOrders" title="{TXT_ORDER_HISTORY}">{TXT_ORDER_HISTORY}</a></li>
<li><a href="index.php?_a=changePass" title="{TXT_CHANGE_PASSWORD}">{TXT_CHANGE_PASSWORD}</a></li>
<li><a href="index.php?_a=newsletter" title="{TXT_NEWSLETTER}">{TXT_NEWSLETTER}</a></li>
</ul>
</div>
<!-- END: session_true -->
	
<!-- BEGIN: session_false -->
<p>{LANG_LOGIN_REQUIRED}</p>
<!-- END: session_false -->
</div>
<!-- END: account -->