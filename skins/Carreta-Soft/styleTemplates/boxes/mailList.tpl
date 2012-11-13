<!-- BEGIN: mail_list -->
<div class="LeftBox border">
<span>{LANG_MAIL_LIST_DESC}</span>
<!-- BEGIN: form -->
<form action="{FORM_METHOD}" method="post">
<span class="SmallTitle">{LANG_EMAIL}</span>
<input name="email" type="text" size="18" maxlength="255" class="textbox" id="email" value="{LANG_EMAIL_ADDRESS}" onclick="this.value='';" /> 
<input type="hidden" name="act" value="mailList" />
<input name="Submit" type="submit" class="SubmitBtn" value="&nbsp;" />
</form>
<!-- END: form -->
</div>
<!-- END: mail_list -->
