<!-- BEGIN: session -->
<!-- BEGIN: session_false -->
<div class="boxTitleLeft">{LANG_WELCOME_GUEST}</div>
<div class="boxContentLeft">
  <div align="center">
    <span class="txtSession"> [</span><a href="index.php?_a=login&amp;redir={VAL_SELF}" class="txtSession">{LANG_LOGIN}</a> <span class="txtSession">|</span> <a href="index.php?_g=co&amp;_a=reg&amp;redir={VAL_SELF}" class="txtSession">{LANG_REGISTER}</a><span class="txtSession">]</span>
  </div>
</div>
<!-- END: session_false -->
<!-- BEGIN: session_true -->
<div class="boxTitleLeft">{LANG_WELCOME_BACK}</div>
<div class="boxContentLeft">
  <div align="center">
    {TXT_USERNAME}<br />
	<span class="txtSession">[</span><a href="index.php?_a=logout" class="txtSession">{LANG_LOGOUT}</a> <span class="txtSession">|</span> <a href="index.php?_a=account" class="txtSession">{LANG_YOUR_ACCOUNT}</a><span class="txtSession">]</span>
  </div>
</div>
<!-- END: session_true -->
<!-- END: session -->
