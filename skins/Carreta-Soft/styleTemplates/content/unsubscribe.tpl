<!-- BEGIN: unsubscribe -->
<div id="ContentBox">
<div class="Title"><h1>{UNSUBSCRIBE_TITLE}</h1></div>
<!-- BEGIN: no_error -->
<p>{LANG_UNSUBSCRIBE_DESC}</p>
<!-- END: no_error -->
<!-- BEGIN: error -->
<p class="txtError">{VAL_ERROR}</p>
<!-- END: error -->
<!-- BEGIN: form -->
<form action="index.php?_a=unsubscribe" target="_self" method="post" style="text-align: center;">
<strong>{TXT_ENTER_EMAIL}</strong> <input type="text" name="email" class="textbox" />
<div class="BlueBg">
<div class="Button"><input name="submit" type="submit" value="{TXT_SUBMIT}" class="submit" /></div>
</div></form>
<!-- END: form -->
</div>
<!-- END: unsubscribe -->