<div id="recaptcha_container">
  <div id="recaptcha_image"></div>
  <label for="recaptcha_response_field">Enter both words, separated with a space&hellip;</label><br />
  <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="textbox" style="width: 280px" /> *
	
  <div class="recaptcha_only_if_image recaptcha-switch">
	Can't read the words above? Try <a href="javascript:Recaptcha.reload()" class="txtDefault">different words</a>, or <a href="javascript:Recaptcha.switch_type('audio')" class="txtDefault">listen to the audio</a>. <a href="javascript:Recaptcha.showhelp()" class="txtDefault">Help!</a>
  </div>
  <div class="recaptcha_only_if_audio recaptcha-switch">
	Can't hear the numbers? Try <a href="javascript:Recaptcha.reload()" class="txtDefault">different numbers</a>, or <a href="javascript:Recaptcha.switch_type('image')" class="txtDefault">look at the image</a>. <a href="javascript:Recaptcha.showhelp()" class="txtDefault">Help!</a>
  </div>
</div>
<script type="text/javascript">
  var RecaptchaOptions = {theme:'custom',custom_theme_widget:'recaptcha_container'};
  var RecaptchaHost = (('https:' == document.location.protocol) ? 'https://api-secure.' : 'http://api.');
  document.write(unescape('%3Cscript type="text/javascript" src="' + RecaptchaHost + 'recaptcha.net/js/recaptcha_ajax.js"%3E%3C/script%3E'));
</script>
{DISPLAY_RECAPTCHA}