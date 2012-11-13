<!-- BEGIN: reg -->
<div id="ContentBox">
{JS_COUNTY_OPTIONS}
<div class="Title"><h1>{LANG_REGISTER}</h1></div>
<!-- BEGIN: checkout_progress -->
<div style="text-align: center; height: 25px;">
<div class="cartProgress">{LANG_CART} --- <span class="txtcartProgressCurrent">{LANG_CHECKOUT}</span> --- {LANG_PAYMENT} --- {LANG_COMPLETE}</div>
</div>
<!-- END: checkout_progress --> 
<!-- BEGIN: no_error -->
<p>{LANG_REGISTER_DESC}</p>
<!-- END: no_error -->
<!-- BEGIN: error -->
<p class="txtError">{VAL_ERROR}</p>
<!-- END: error -->
<form name="registerForm" method="post" action="{VAL_ACTION}">
<table  border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
<td colspan="2" class="tdcartTitle">{LANG_PERSONAL_DETAILS}</td>
<td colspan="2" class="tdcartTitle">{LANG_ADDRESS}</td>
</tr>
	  
<tr>
<td>{LANG_TITLE}</td>
<td align="left"><input name="title" type="text" class="textbox" id="title" size="5" value="{VAL_TITLE}" tabindex="1" /> <small>{LANG_TITLE_DESC}</small></td>

<td>{LANG_COMPANY_NAME}</td>
<td><input name="companyName" type="text" class="textbox" id="companyName" size="16" value="{VAL_COMPANY_NAME}" tabindex="7" /></td>
</tr>
	  
<tr>
<td>{LANG_FIRST_NAME}</td>
<td align="left"><input name="firstName" type="text" class="textbox" id="firstName" size="16" value="{VAL_FIRST_NAME}" tabindex="2" /> {ICON_REQUIRED}</td>

<td>{LANG_ADDRESS_FORM}</td>
<td><input name="add_1" type="text" class="textbox" id="add_1" size="16" value="{VAL_ADD_1}" tabindex="8" />
  {ICON_REQUIRED}</td>
</tr>
	  
<tr>
<td>{LANG_LAST_NAME}</td>
<td align="left"><input name="lastName" type="text" class="textbox" id="lastName" size="16" value="{VAL_LAST_NAME}" tabindex="3" /> {ICON_REQUIRED}</td>

<td>&nbsp;</td>
<td><input name="add_2" type="text" class="textbox" id="add_2" size="16" value="{VAL_ADD_2}" tabindex="9" />
  &nbsp;</td>
</tr>
	  
<tr>
<td>{LANG_EMAIL_ADDRESS}</td>
<td align="left"><input name="email" type="text" class="textbox" id="emailaddr" size="16" value="{VAL_EMAIL}" tabindex="4" /> {ICON_REQUIRED}</td>

<td>{LANG_TOWN}</td>
<td><input name="town" type="text" class="textbox" id="town" size="16" value="{VAL_TOWN}" tabindex="10" />
  {ICON_REQUIRED}</td>
</tr>
	  
<tr>
  <td>{LANG_TELEPHONE}</td>
  <td align="left"><input name="phone" type="text" class="textbox" id="phone" size="16" value="{VAL_PHONE}" tabindex="5" />
    {ICON_REQUIRED}</td>
  <td>{LANG_POSTCODE}</td>
  <td><input name="postcode" type="text" class="textbox" id="postcode" size="16" value="{VAL_POSTCODE}" tabindex="11" />
    {ICON_REQUIRED}</td>
</tr>
<tr>
  <td>{LANG_MOBILE}</td>
  <td align="left"><input name="mobile" type="text" class="textbox" id="mobile" size="16" value="{VAL_MOBILE}" tabindex="6" />
  &nbsp;</td>
  <td>{LANG_COUNTRY}</td>
  <td nowrap='nowrap'><select name="country" class="textbox" tabindex="12" onchange="updateCounty(this.form);">
      <!-- BEGIN: repeat_countries -->
      <option value="{VAL_COUNTRY_ID}" {VAL_COUNTRY_SELECTED}>{VAL_COUNTRY_NAME}</option>
      <!-- END: repeat_countries -->
    </select>
    {ICON_REQUIRED}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td align="left">&nbsp;</td>
<td>{LANG_COUNTY}</td>
<td><div id="divCountyText" {VAL_COUNTY_TXT_STYLE}>
  <input name="county" type="text" class="textbox" size="16" id="county" value="{VAL_DEL_COUNTY}" maxlength="50"  tabindex="13" /> {ICON_REQUIRED}
</div>
    <div id="divCountySelect" {VAL_COUNTY_SEL_STYLE}>
      <select name="county_sel" id="county_sel" class="textbox" tabindex="13">
        <!-- BEGIN: county_opts -->
        <option value="{VAL_DEL_COUNTY_ID}" {COUNTY_SELECTED}>{VAL_DEL_COUNTY_NAME}</option>
        <!-- END: county_opts -->
      </select>
      <input name="which_field" type="hidden" id="which_field" value="{VAL_COUNTY_WHICH_FIELD}" />
      {ICON_REQUIRED}</div></td>
</tr>

<tr>
<td colspan="4" class="tdcartTitle">{LANG_SECURITY_DETAILS}</td>
</tr>
<!-- BEGIN: account_opt -->
<tr>		
<td colspan="4">{LANG_NO_ACCOUNT_WANTED} <input name="skipReg" type="checkbox" onclick="toggleReg();" value="1" {VAL_SKIP_REG_CHECKED} /></td>
</tr>
<!-- END: account_opt -->
<tr>
<td>{LANG_CHOOSE_PASSWORD}</td>
<td align="left"><input name="password" type="password" autocomplete="off" class="{VAL_PASS_CLASS}" id="password" size="16" value="{VAL_PASSWORD}" tabindex="14" {VAL_PASS_MODE} /> <span id="password_required" {VAL_PASS_HIDE_REQUIRED}>{ICON_REQUIRED_SECURITY}</span></td>

<td>{LANG_CONFIRM_PASSWORD}</td>
<td><input name="passwordConf" type="password" autocomplete="off" class="{VAL_PASS_CLASS}" id="passwordConf" size="16" value="{VAL_PASSWORD_CONF}" tabindex="15" {VAL_PASS_MODE} /> <span id="passwordConf_required" {VAL_PASS_HIDE_REQUIRED}>{ICON_REQUIRED_SECURITY}</span></td>
</tr>
<!-- BEGIN: spambot -->
<tr>
<td>{TXT_SPAMBOT}</td>
<td colspan="3"><br /><br />{IMG_SPAMBOT}<br /><input name="spamcode" type="text" class="textbox" value="" tabindex="16" size="20" maxlength="5" /> {ICON_REQUIRED}</td>
</tr>
<!-- END: spambot -->
<!-- BEGIN: recaptcha -->
<tr>
	<td valign="top">{TXT_SPAMBOT}</td>
	<td colspan="3">{FILE "content/recaptcha.tpl"}</td>
</tr>
<!-- END: recaptcha -->
<tr>
<td colspan="4" class="tdcartTitle">{LANG_PRIVACY_SETTINGS}</td>
</tr>

<tr>
<td colspan="2">{LANG_RECIEVE_EMAILS} <input type="checkbox" name="optIn1st" value="1" tabindex="17" {VAL_OPTIN1ST_CHECKED}/></td>
<td colspan="2" align="center">{LANG_EMAIL_FORMAT} <select name="htmlEmail" class="textbox" tabindex="18">
<option value="1">{LANG_HTML_FORMAT}</option>
<option value="0" {VAL_HTMLEMAIL_SELECTED}>{LANG_PLAIN_TEXT}</option>
</select></td>
</tr>

<tr>
<td colspan="4">&nbsp;</td>
</tr>

<tr>
<td colspan="4" align="center">{LANG_PLEASE_READ} <a href="{LINK_TANDCS}" title="{LANG_TANDOCS}" target="_blank" class="txtLink">{LANG_TANDCS}</a> <input type="checkbox" name="tandc" value="checkbox" /> {ICON_REQUIRED}</td>
</tr>

<tr>
<td colspan="4"><input name="ESC" type="hidden" value="{VAL_ESC}" /></td>
</tr>
</table>

<div class="BlueBg" style="padding:5px 5px 5px 0;"><a href="javascript:submitDoc('registerForm');" class="txtButton" tabindex="19">{LANG_REGISTER_SUBMIT}</a></div>
</form>
</div>
<!-- END: reg -->
{LANG_SECURITY_DETAILS}