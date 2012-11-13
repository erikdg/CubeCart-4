<!-- BEGIN: session -->
<div id="Login"><!--start Login-->
<!-- BEGIN: session_false -->
<p>{LANG_WELCOME_GUEST} [ <a href="index.php?_a=login&amp;redir={VAL_SELF}" title="{LANG_LOGIN}">{LANG_LOGIN}</a> | 
<a href="index.php?_g=co&amp;_a=reg&amp;redir={VAL_SELF}" title="{LANG_REGISTER}">{LANG_REGISTER}</a> ]</p>
<!-- END: session_false -->

<!-- BEGIN: session_true -->
<p>{LANG_WELCOME_BACK}, {TXT_USERNAME} [ <a href="index.php?_a=logout" title="{LANG_LOGOUT}">{LANG_LOGOUT}</a> | 
<a href="index.php?_a=account" title="{LANG_YOUR_ACCOUNT}">{LANG_YOUR_ACCOUNT}</a> ]</p>
<!-- END: session_true -->
</div><!-- close Login -->
<!-- END: session -->