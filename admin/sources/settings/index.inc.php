<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	index.php
|   ========================================
|	Manage Main Store Settings
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$lang = getLang('admin'.CC_DS.'admin_settings.inc.php');
$lang = getLang('orders.inc.php');

$msg = false;

permission('settings','read', true);

if (isset($_POST['install_htaccess']) && permission('settings','write', true)) {
	$htaccess = CC_ROOT_DIR.CC_DS.'.htaccess';
	$ht_new = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-htaccess.txt');
	## Some hosting companies need a RewriteBase if we can detect them e.g. Mosso
	if($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) {
		$ht_new = str_replace("RewriteEngine On","RewriteEngine On\nRewriteBase ".$glob['rootRel'],$ht_new);
	}
	if (@file_exists($htaccess)) {
		## .htaccess file already exists - lets check if it already has the settings, and append them if it doesn't
		$ht_old = @file_get_contents($htaccess);
		if (!strstr($ht_old, $ht_new) && @cc_is_writable($htaccess)) {
			## Append the rewrite rules
			$fp = @fopen($htaccess, 'ab');
			if (@fwrite($fp, $ht_new, strlen($ht_new))) {
				$msg .= '<p class="infoText">.htaccess was successfully created.</p>';
			} else {
				$msg .= '<p class="warnText">.htaccess file could not be written. Please create it manually.</p>';
			}
			@fclose($fp);
		}
	} else {
		$fp = @fopen(CC_ROOT_DIR.CC_DS.'.htaccess', 'wb');
		if (!@fwrite($fp, $ht_new)) {
			$msg .= '<p class="warnText">.htaccess file could not be written. Please create it manually.</p>';
		} else {
			$msg .= '<p class="infoText">.htaccess was successfully created.</p>';
		}
		@fclose($fp);
	}
} elseif (isset($_POST['install_rewrite_script']) && permission('settings','write', true)) {
	## rewrite.script has to sit in web root folder
	$rewrite_script = $_SERVER['DOCUMENT_ROOT'].CC_DS.'rewrite.script';
	$ht_new = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-rewrite.script.txt');
	$ht_new = str_replace("{VAL_ROOT_REL}",$glob['rootRel'],$ht_new);
	if (@file_exists($rewrite_script)) {
		## rewrite.script file already exists - lets check if it already has the settings, and append them if it doesn't
		$ht_old = @file_get_contents($rewrite_script);
		if (!strstr($ht_old, $ht_new) && @cc_is_writable($rewrite_script)) {
			## Append the rewrite rules
			$fp = @fopen($rewrite_script, 'ab');
			if (@fwrite($fp, $ht_new, strlen($ht_new))) {
				$msg .= '<p class="infoText">rewrite.script was successfully created.</p>';
			} else {
				$msg .= '<p class="warnText">rewrite.script file could not be written. Please create it manually.</p>';
			}
			@fclose($fp);
		}
	} else {
		$fp = @fopen($_SERVER['DOCUMENT_ROOT'].CC_DS.'rewrite.script', 'wb');
		if (!@fwrite($fp, $ht_new)) {
			$msg .= '<p class="warnText">rewrite.script file could not be written. Please create it manually.</p>';
		} else {
			$msg .= '<p class="infoText">rewrite.script was successfully created.</p>';
		}
		@fclose($fp);
	}
}

if (isset($_POST['config']) && permission('settings','write', true)) {
	$cache = new cache();
	$cache->clearCache();

	## fix for Bug #147
	$fckEditor = (detectSSL() && !$config['force_ssl']) ?  str_replace($config['rootRel_SSL'],$glob['rootRel'],$_POST['FCKeditor']) : $_POST['FCKeditor'];
	$_POST['config']['offLineContent'] = base64_encode($fckEditor);

	$config = fetchDbConfig('config');

	## DIRTY BUT MAKES SUPPORT EASIER!!
	if ($_POST['config']['ssl'] && !strstr($_POST['config']['rootRel_SSL'], '/')) {
		$msg .= "<p class='warnText'>The HTTPS Root Relative Path entered is not valid! SSL has not been enabled.</p>";
		$_POST['config']['force_ssl'] = false;
		$_POST['config']['ssl'] = false;

	}

	if ($_POST['config']['ssl'] && !strstr($_POST['config']['storeURL_SSL'], 'https')) {
		$msg .= "<p class='warnText'>The absolute HTTPS Absolute URL entered is not valid. SSL has not been enabled.</p>";
		$_POST['config']['force_ssl'] = false;
		$_POST['config']['ssl'] = false;
	}

	if ($_POST['config']['sqlSessionExpiry'] && $_POST['config']['sqlSessionExpiry']<7200) {
		$msg .= "<p class='infoText'>The minimum session time has been set to 2 hours (7200 seconds). This will prevent IE session problems.</p>";
		$_POST['config']['sqlSessionExpiry'] = 7200;
	}
	$msg .= writeDbConf($_POST['config'], 'config', $config, true);
}
$config = fetchDbConfig('config');

$jsScript = jsGeoLocation('siteCountry', 'siteCounty', '-- '.$lang['admin_common']['na'].' --');

require($glob['adminFolder'].CC_DS."includes".CC_DS."header.inc.php");
?>
<p class="pageTitle"><?php echo $lang['admin']['settings_store_settings']; ?></p>

<?php if (isset($msg)) echo msg($msg); ?>
<p class="copyText"><?php echo $lang['admin']['settings_edit_below']; ?></p>

<p class="copyText"><?php echo $lang['admin']['settings_jump_to']; ?>
<select name="jump" onchange="jumpMenu('parent',this,0)">
<option value="#meta_data"><?php echo $lang['admin']['settings_meta_data']; ?></option>
<option value="#dirs_folders"><?php echo $lang['admin']['settings_dirs_folders']; ?></option>
<option value="#digital_downloads"><?php echo $lang['admin']['settings_digital_downloads'];?></option>
<option value="#styles_misc"><?php echo $lang['admin']['settings_styles_misc'];?></option>
<option value="#gd_settings"><?php echo $lang['admin']['settings_gd_settings'];?></option>
<option value="#stock_settings"><?php echo $lang['admin']['settings_stock_settings'];?></option>
<option value="#time_and_date"><?php echo $lang['admin']['settings_time_and_date'];?></option>
<option value="#locale_settings"><?php echo $lang['admin']['settings_locale_settings'];?></option>
<option value="#off_line_settings"><?php echo $lang['admin']['settings_off_line_settings'];?></option>
<option value="#proxy"><?php echo $lang['admin']['settings_proxy'];?></option>
<option value="#sef"><?php echo $lang['admin']['settings_sef'];?></option>
</select>
</p>

<form name="updateSettings" method="post" enctype="multipart/form-data" target="_self" action="<?php echo $glob['adminFile']; ?>?_g=settings/index">
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="mainTable">
	<tr>
		<td colspan="2" class="tdTitle" id="meta_data"><strong><?php echo $lang['admin']['settings_meta_data']; ?></strong></td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_browser_title']; ?></strong></td>
	  <td align="left"><input name="config[siteTitle]" type="text" size="35" class="textbox" value="<?php echo $config['siteTitle']; ?>" /></td>
    </tr>
	<tr>
	  <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings_meta_desc'];?></strong></td>
	  <td align="left"><textarea name="config[metaDescription]" cols="35" rows="3" class="textbox"><?php echo $config['metaDescription']; ?></textarea></td>
    </tr>
	<tr>
	  <td width="30%" align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['settings_meta_keywords'];?></strong><br />
 <?php echo $lang['admin']['settings_comma_separated'];?></td>
	  <td align="left"><textarea name="config[metaKeyWords]" cols="35" rows="3" class="textbox"><?php echo $config['metaKeyWords']; ?></textarea></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_store_co_name'];?></strong></td>
	  <td><input name="config[storeName]" type="text" size="35" class="textbox" value="<?php echo $config['storeName']; ?>" /></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_store_address'];?></strong></td>
	  <td><textarea name="config[storeAddress]" cols="35" rows="3" class="textbox"><?php echo $config['storeAddress']; ?></textarea></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_country'];?></strong></td>
      <td>
	  <?php
	  $countries = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_countries");
	  ?>

	<select name="config[siteCountry]" id="siteCountry" onChange="updateCounty(this.form);">
	<?php
	for($i = 0, $maxi = count($countries); $i < $maxi; ++$i)
	{
	?>
	<option value="<?php echo $countries[$i]['id']; ?>" <?php if($countries[$i]['id'] == $config['siteCountry']) echo 'selected="selected"'; ?>><?php echo $countries[$i]['printable_name']; ?></option>
	<?php
	}
	?>
	</select>
	  </td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_zone'];?></strong></td>
	  <td>
	  <?php
	  $counties = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_iso_counties WHERE `countryId` = '".$config['siteCountry']."'");
	  ?>
	  <select name="config[siteCounty]" id="siteCounty">
	  <option value="" <?php if(empty($config['siteCounty'])) echo 'selected="selected"'; ?>>-- <?php echo $lang['admin_common']['na'];?> --</option>
	  <?php
	  if($counties)
	  {
	   for($i = 0, $maxi = count($counties); $i < $maxi; ++$i)
	   { ?>
	  <option value="<?php echo $counties[$i]['id']; ?>" <?php if($counties[$i]['id']==$config['siteCounty']) echo 'selected="selected"'; ?>><?php echo $counties[$i]['name']; ?></option>
	  <?php
	    }
	  } ?>
      </select></td>
    </tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="dirs_folders"><strong><?php echo $lang['admin']['settings_dirs_folders'];?></strong></td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_rootRel'];?></strong><br />
<?php echo $lang['admin']['settings_eg_rootRel'];?></td>
		<td align="left"><span class="textboxDisabled"><?php echo $glob['rootRel']; ?></span> <a href="javascript:;" class="txtLink" onclick="alert('<?php echo $lang['admin']['settings_ref_only'];?>');">*</a></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_storeURL'];?></strong> <br />
	  <?php echo $lang['admin']['settings_eg_domain_com'];?> </td>
		<td align="left"><span class="textboxDisabled"><?php echo $glob['storeURL']; ?></span> <a href="javascript:;" class="txtLink" onclick="alert('<?php echo $lang['admin']['settings_ref_only'];?>');">*</a></td>
	</tr>
	<!--
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_rootDir'];?></strong><br />
	  <?php echo $lang['admin']['settings_eg_root_path'];?>
	</td>
		<td align="left"><span class="textboxDisabled"><?php echo CC_ROOT_DIR; ?></span> <a href="javascript:;" class="txtLink" onclick="alert('<?php echo $lang['admin']['settings_ref_only'];?>');">*</a></td>
	</tr>
	-->
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_enable_ssl'];?></strong></td>
	  <td align="left">
	  <select name="config[ssl]" class="textbox">
		<option value="1" <?php if($config['ssl']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
		<option value="0" <?php if($config['ssl']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
	  </select> <?php echo $lang['admin']['settings_ssl_warn'];?></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_force_ssl'];?></strong></td>
	  <td align="left">
	  <select name="config[force_ssl]" class="textbox">
		<option value="1" <?php if($config['force_ssl']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
		<option value="0" <?php if($config['force_ssl']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
	  </select> <?php echo $lang['admin']['settings_force_ssl_desc'];?></td>
    </tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_rootRel_SSL'];?></strong>
	<br />
<?php echo $lang['admin']['settings_eg_rootRel'];?> </td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[rootRel_SSL]" value="<?php echo $config['rootRel_SSL']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_storeURL_SSL'];?></strong> <br />
	  <?php echo $lang['admin']['settings_eg_domain_SSL'];?></td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[storeURL_SSL]" value="<?php echo $config['storeURL_SSL']; ?>" /></td>
	</tr>
	<!--
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_rootDir_SSL'];?></strong><br />
	  <?php echo $lang['admin']['settings_eg_root_path_secure'];?></td>
		<td align="left">
		
		<input type="text" size="35" class="textbox" name="config[rootDir_SSL]" value="<?php echo $config['rootDir_SSL']; ?>" />
		
		<span class="textboxDisabled"><?php echo CC_ROOT_DIR; ?></span>  <a href="javascript:;" class="txtLink" onclick="alert('<?php echo $lang['admin']['settings_ref_only'];?>');">*</a>
		</td>
	</tr>
	-->
	<tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p class="copyText">* <?php echo $lang['admin']['settings_ref_only'];?></p>

<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="digital_downloads"><strong><?php echo $lang['admin']['settings_digital_downloads'];?></strong></td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_download_expire_time'];?></strong><br/>
      <?php echo $lang['admin']['settings_seconds'];?></td>
	  <td align="left"><input type="text" size="35" class="textbox" name="config[dnLoadExpire]" value="<?php echo $config['dnLoadExpire']; ?>" /></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_download_attempts'];?></strong><br />
      <?php echo $lang['admin']['settings_attempts_desc'];?></td>
	  <td align="left"><input type="text" size="35" class="textbox" name="config[dnLoadTimes]" value="<?php echo $config['dnLoadTimes']; ?>" /></td>
    </tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="styles_misc"><strong><?php echo $lang['admin']['settings_styles_misc'];?></strong></td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_default_language'];?></strong></td>
	  <td align="left">
		<select class="textbox" name="config[defaultLang]">
		<?php
		$path = CC_ROOT_DIR.CC_DS."language";
		foreach (glob($path.CC_DS.'*') as $langpath) {
			$folder = basename($langpath);
			if (is_dir($langpath) && preg_match('#^[a-z]{2}(\_[A-Z]{2})?$#iuU', $folder)) {
				if (file_exists($langpath.CC_DS.'config.php')) {
					include $langpath.CC_DS.'config.php';

					$selected = ($config['defaultLang']==$folder) ? ' selected="selected"' : '';
					echo sprintf('<option value="%s"%s>%s</option>', $folder, $selected, $langName);
				}
			}
		}
		?>
		</select>
	  </td>
    </tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_store_skin'];?></strong></td>
	  <td align="left">
		<select class="textbox" name="config[skinDir]">
		<?php
		$skinPath = CC_ROOT_DIR.CC_DS.'skins';
		$skinList = listAddons($skinPath);

		foreach ($skinList as $folder) {
			if (file_exists($skinPath.CC_DS.$folder.CC_DS.'package.conf.php')) {
			//	loadAddonConfig();
			//	include $skinPath.CC_DS.$folder.CC_DS.'package.conf.php';
			} else {
				$skin['name'] = $folder;
			}
			$selected = ($config['skinDir'] == $folder) ? ' selected="selected"' : '';
			echo sprintf('<option value="%s"%s>%s</option>', $folder, $selected, $skin['name']);
		}
		?>
		</select>
	  </td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_changeskin'];?></strong></td>
	<td align="left">
		<select class="textbox" name="config[changeskin]">
		  <?php
		  $array = array($lang['admin_common']['no'], $lang['admin_common']['yes']);
		  foreach ($array as $key => $title) {
		  	$selected = ($config['changeskin']==$key) ? 'selected="selected"' : '';
			echo sprintf('<option value="%s"%s>%s</option>', $key, $selected, $title);
		  }
		  ?>
		</select>
	</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_show_latest'];?></strong></td>
		<td align="left">
		<select class="textbox" name="config[showLatestProds]">
			<option value="0" <?php if($config['showLatestProds']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
			<option value="1" <?php if($config['showLatestProds']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
		</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_no_latest'];?></strong></td>
		<td align="left">
		<input type="text" class="textbox" size="3" name="config[noLatestProds]" value="<?php echo $config['noLatestProds']; ?>" />		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_no_related'];?></strong></td>
		<td align="left">
		<input type="text" class="textbox" size="3" name="config[noRelatedProds]" value="<?php echo (isset($config['noRelatedProds'])) ? $config['noRelatedProds'] : 3; ?>" />		</td>
	</tr>
	<!--
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_no_cats_per_row'];?></strong></td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[displaycatRows]" value="<?php echo $config['displaycatRows']; ?>" /></td>
	</tr>
	-->
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_dir_symbol'];?></strong></td>
		<td align="left"><input type="text" size="20" class="textbox" name="config[dirSymbol]" value="<?php echo $config['dirSymbol']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_prods_per_page'];?></strong></td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[productPages]" value="<?php echo $config['productPages']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_precis_length'];?></strong><?php echo $lang['admin']['settings_chars'];?></td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[productPrecis]" value="<?php echo $config['productPrecis']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_no_sale_items'];?></strong></td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[noSaleBoxItems]" value="<?php echo $config['noSaleBoxItems']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_no_pop_prod'];?></strong></td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[noPopularBoxItems]" value="<?php echo $config['noPopularBoxItems']; ?>" /></td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_email_name'];?></strong><br />
	  <?php echo $lang['admin']['settings_email_name_desc'];?></td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[masterName]" value="<?php echo $config['masterName']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_email_address'];?></strong><br />
	<?php echo $lang['admin']['settings_email_address_desc'];?></td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[masterEmail]" value="<?php echo $config['masterEmail']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_mail_method'];?></strong><br />
	  <?php echo $lang['admin']['settings_mail_recommended'];?> </td>
		<td align="left">
			<select name="config[mailMethod]" class="textbox">
				<option value="mail" <?php if($config['mailMethod']=="mail") echo 'selected="selected"'; ?>>mail()</option>
				<option value="smtp" <?php if($config['mailMethod']=="smtp") echo 'selected="selected"'; ?>>SMTP</option>
			</select>		</td>
	</tr>
	<tr>
	  <td class="tdText"><?php echo $lang['admin']['settings_smtpHost'];?></td>
	  <td align="left" class="tdText"><input type="text" size="25" class="textbox" name="config[smtpHost]" value="<?php echo $config['smtpHost']; ?>" />
	     <?php echo $lang['admin']['settings_defaultHost'];?></td>
    </tr>
		<tr>
		  <td class="tdText"><?php echo $lang['admin']['settings_smtpPort'];?></td>
		  <td align="left" class="tdText"><input type="text" size="3" class="textbox" name="config[smtpPort]" value="<?php echo $config['smtpPort']; ?>" />
	      <?php echo $lang['admin']['settings_defaultPort'];?></td>
    </tr>
		<tr>
		  <td class="tdText"><?php echo $lang['admin']['settings_smtpAuth'];?></td>
		  <td align="left" class="tdText"><select name="config[smtpAuth]" class="textbox">
            <option value="false" <?php if($config['smtpAuth']=="false") echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
			<option value="true" <?php if($config['smtpAuth']=="true") echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
          </select>
		  <?php echo $lang['admin']['settings_defaultAuth'];?></td>
    </tr>
		<tr>
		  <td class="tdText"><?php echo $lang['admin']['settings_smtpUsername'];?></td>
		  <td align="left"><input type="text" size="25" class="textbox" name="config[smtpUsername]" value="<?php echo $config['smtpUsername']; ?>" /></td>
    </tr>
		<tr>
		  <td class="tdText"><?php echo $lang['admin']['settings_smtpPassword'];?></td>
		  <td align="left"><input type="text" size="25" class="textbox" name="config[smtpPassword]" value="<?php echo $config['smtpPassword']; ?>" /></td>
    </tr>
		<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_max_upload_size'];?></strong><br />
	  <?php echo $lang['admin']['settings_under_x_recom'];?></td>
		<td align="left"><input type="text" size="10" class="textbox" name="config[maxImageUploadSize]" value="<?php echo $config['maxImageUploadSize']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_max_sess_length'];?></strong><br />
	  <?php echo $lang['admin']['settings_seconds'];?></td>
	  <td align="left"><input type="text" size="10" class="textbox" name="config[sqlSessionExpiry]" value="<?php echo $config['sqlSessionExpiry']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_floodControl'];?></strong><br /><?php echo $lang['admin']['settings_floodControlDesc'];?></td>
	  <td align="left">
	  <select name="config[floodControl]" class="textbox">
			<option value="0" <?php if($config['floodControl']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no']; ?></option>
			<option value="1" <?php if($config['floodControl']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes']; ?></option>
			<option value="recaptcha" <?php if($config['floodControl']=="recaptcha") echo 'selected="selected"'; ?>>reCaptcha (http://www.recaptcha.net)</option>
		</select></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_richTextEditor'];?></strong></td>
	  <td align="left" class="tdText">
	  <select name="config[richTextEditor]" class="textbox">
			<option value="0" <?php if($config['richTextEditor']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes']; ?></option>
			<option value="1" <?php if($config['richTextEditor']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no']; ?></option>
		</select> <?php echo $lang['admin']['settings_rte_height'];?> <input type="text" name="config[rteHeight]" size="5" class="textbox" value="<?php echo $config['rteHeight']; ?>" />

		<select name="config[rteHeightUnit]" class="textbox">
			<option value="%" <?php if($config['rteHeightUnit']=='%') echo 'selected="selected"'; ?>>%</option>
			<option value="" <?php if(empty($config['rteHeightUnit'])) echo 'selected="selected"'; ?>>px</option>
		</select>
	  </td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_debug'];?></strong></td>
	  <td align="left" class="tdText">
	  <select name="config[debug]" class="textbox">
			<option value="0" <?php if($config['debug']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no']; ?></option>
			<option value="1" <?php if($config['debug']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes']; ?></option>
		</select> <?php echo $lang['admin']['settings_debug_desc']; ?></td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_latestNewsRSS'];?></strong></td>
	  <td align="left" class="tdText">
	  <input type="text" name="config[latestNewsRRS]" size="35" class="textbox" value="<?php echo $config['latestNewsRRS']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_add_to_basket_act'];?></strong></td>
		<td align="left">
			<select name="config[add_to_basket_act]" class="textbox">
				<option value="0" <?php if($config['add_to_basket_act']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['add_to_basket_act']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>

			</select>		</td>
	</tr>


	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_img_gallery_type'];?></strong></td>
		<td align="left">
			<select name="config[imgGalleryType]" class="textbox">
				<option value="0" <?php if($config['imgGalleryType']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_img_gallery_type_popup'];?></option>
				<option value="1" <?php if($config['imgGalleryType']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_img_gallery_type_lightbox'];?></option>

			</select>		</td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_cat_tree'];?></strong></td>
		<td align="left">
			<select name="config[cat_tree]" class="textbox">
				<option value="0" <?php if($config['cat_tree']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['cat_tree']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>

			</select>		</td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['hide_prices'];?></strong></td>
		<td align="left">
			<select name="config[hide_prices]" class="textbox">
				<option value="0" <?php if($config['hide_prices']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['hide_prices']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>

			</select>		</td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['pop_products_source'];?></strong></td>
		<td align="left">
			<select name="config[pop_products_source]" class="textbox">
				<option value="0" <?php if($config['pop_products_source']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['pop_products_views'];?></option>
				<option value="1" <?php if($config['pop_products_source']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['pop_products_sales'];?></option>

			</select>		</td>
	</tr>
		<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['use_cache'];?></strong></td>
		<td align="left">
			<select name="config[cache]" class="textbox">
				<option value="0" <?php if($config['cache']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['cache']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>

			</select>		</td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['show_empty_cat'];?></strong></td>
		<td align="left">
			<select name="config[show_empty_cat]" class="textbox">
				<option value="0" <?php if($config['show_empty_cat']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['show_empty_cat']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>

			</select></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['disable_alert_email'];?></strong></td>
		<td align="left">
			<select name="config[disable_alert_email]" class="textbox">
				<option value="0" <?php if($config['disable_alert_email']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['disable_alert_email']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
			</select></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['cat_newest_first'];?></strong><br />
	<?php echo $lang['admin']['cat_newest_first_info'];?>
	</td>
		<td align="left">
			<select name="config[cat_newest_first]" class="textbox">
				<option value="0" <?php if(!$config['cat_newest_first']) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['cat_newest_first']) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
			</select></td>
	</tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['google_analytics'];?></strong><br />
	  <?php echo $lang['admin']['google_analytics_info'];?></td>
	  <td align="left"><input type="text" size="10" class="textbox" name="config[google_analytics]" value="<?php echo $config['google_analytics']; ?>" /></td>
	</tr>

	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_order_expire'];?></strong><br />
	  <?php echo $lang['admin']['settings_seconds'];?></td>
	  <td align="left"><input type="text" size="10" class="textbox" name="config[orderExpire]" value="<?php echo $config['orderExpire']; ?>" /><?php echo $lang['admin']['settings_zero_disabled'];?> </td>
	</tr>
	<tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="gd_settings"><strong><?php echo $lang['admin']['settings_gd_settings'];?></strong></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_gd_ver'];?></strong></td>
		<td align="left">
			<select name="config[gdversion]" class="textbox">
				<option value="2" <?php if($config['gdversion']==2) echo 'selected="selected"'; ?>>2</option>
				<option value="0" <?php if($config['gdversion']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['na']; ?></option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_gd_gif_support'];?></strong></td>
		<td align="left">
			<select name="config[gdGifSupport]" class="textbox">
				<option value="0" <?php if($config['gdGifSupport']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
				<option value="1" <?php if($config['gdGifSupport']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_gd_thumb_size'];?></strong></td>
		<td align="left"><input type="text" size="4" class="textbox" name="config[gdthumbSize]" value="<?php echo $config['gdthumbSize']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_gd_max_img_size'];?></strong></td>
		<td align="left"><input type="text" size="4" class="textbox" name="config[gdmaxImgSize]" value="<?php echo $config['gdmaxImgSize']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_gd_img_quality'];?></strong><br />
<?php echo $lang['admin']['settings_recom_quality'];?></td>
		<td align="left"><input type="text" size="3" class="textbox" name="config[gdquality]" value="<?php echo $config['gdquality']; ?>" /></td>
	</tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="stock_settings"><strong><?php echo $lang['admin']['settings_stock_settings'];?></strong></td>
    </tr>
		<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_use_stock'];?></strong></td>
		<td align="left">
			<select name="config[stockLevel]" class="textbox">
				<option value="1" <?php if($config['stockLevel']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
				<option value="0" <?php if($config['stockLevel']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_allow_out_of_stock_purchases'];?></strong></td>
		<td align="left">
			<select name="config[outofstockPurchase]" class="textbox">
				<option value="1" <?php if($config['outofstockPurchase']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
				<option value="0" <?php if($config['outofstockPurchase']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_stock_change_time'];?></strong></td>
		<td align="left">
			<select name="config[stock_change_time]" class="textbox">
				<option value="0" <?php if($config['stock_change_time']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_stock_change_timement'];?></option>
				<option value="1" <?php if($config['stock_change_time']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_stock_decrease_onprocessing'];?></option>
				<option value="2" <?php if($config['stock_change_time']==2) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_stock_decrease_onorderbuild'];?></option>
			</select>
	  </td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_stock_replace_time']; ?></strong></td>
		<td align="left">
		<input type="checkbox" value="1" name="config[stock_replace_time][1]"  <?php if($config['stock_replace_time'][1]==1) echo 'checked="checked"'; ?> /> <?php echo $lang['glob']['orderState_1'];?> <br />
		  <input type="checkbox" value="1" name="config[stock_replace_time][2]"  <?php if($config['stock_replace_time'][2]==1) echo 'checked="checked"'; ?> /> <?php echo $lang['glob']['orderState_2'];?> <br />
		  <input type="checkbox" value="1" name="config[stock_replace_time][4]"  <?php if($config['stock_replace_time'][4]==1) echo 'checked="checked"'; ?> /> <?php echo $lang['glob']['orderState_4'];?> <br />
		  <input type="checkbox" value="1" name="config[stock_replace_time][5]"  <?php if($config['stock_replace_time'][5]==1) echo 'checked="checked"'; ?> /> <?php echo $lang['glob']['orderState_5'];?> <br />
		  <input type="checkbox" value="1" name="config[stock_replace_time][6]"  <?php if($config['stock_replace_time'][6]==1) echo 'checked="checked"'; ?> /> <?php echo $lang['glob']['orderState_6'];?>
	  </td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_stock_warn_type'];?></strong>
</td>
		<td align="left" class="tdText">
			<select name="config[stock_warn_type]" class="textbox">
				<option value="0" <?php if($config['stock_warn_type']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_stock_global_warn'];?></option>
				<option value="1" <?php if($config['stock_warn_type']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_stock_product_warn'];?></option>
			</select>
	  </td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_stock_warn_level'];?></strong>
</td>
		<td align="left" class="tdText">
			<input type="text" size="3" class="textbox" name="config[stock_warn_level]" id="stock_warn_level" value="<?php echo $config['stock_warn_level']; ?>" /> <?php echo $lang['admin']['settings_stock_warn_level_desc'];?>
	  </td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_weight_unit'];?></strong></td>
	  <td align="left"><select name="config[weightUnit]" class="textbox">
        <option value="Lb" <?php if($config['weightUnit']=="Lb") echo 'selected="selected"'; ?>>Lb</option>
        <option value="Kg" <?php if($config['weightUnit']=="Kg") echo 'selected="selected"'; ?>>Kg</option>
      </select></td>
    </tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="time_and_date"><strong><?php echo $lang['admin']['settings_time_and_date'];?></strong></td>
    </tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_time_format'];?></strong><br />
	  <?php echo $lang['admin']['settings_time_format_desc'];?></td>
		<td align="left"><input type="text" size="20" class="textbox" name="config[timeFormat]" value="<?php echo $config['timeFormat']; ?>" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_time_offset'];?></strong><br />
	  <?php echo $lang['admin']['settings_time_offset_desc'];?></td>
		<td align="left"><input name="config[timeOffset]" type="text" class="textbox" value="<?php echo $config['timeOffset']; ?>" size="20" /></td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_date_format'];?></strong><br />
	  <?php echo $lang['admin']['settings_date_format_desc'];?></td>
		<td align="left"><input type="text" size="35" class="textbox" name="config[dateFormat]" value="<?php echo $config['dateFormat']; ?>" /></td>
	</tr><tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">

	<tr>
		<td colspan="2" class="tdTitle" id="locale_settings"><?php echo $lang['admin']['settings_locale_settings'];?></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_default_currency'];?></strong></td>
	  <td align="left">
	  <?php
	  $currencies = $db->select("SELECT name, code FROM ".$glob['dbprefix']."CubeCart_currencies WHERE active = 1 ORDER BY name ASC");
		?>
		<select name="config[defaultCurrency]">
		<?php
		for($i = 0, $maxi = count($currencies); $i < $maxi; ++$i){
		?>
		<option value="<?php echo $currencies[$i]['code']; ?>" <?php if($currencies[$i]['code']==$config['defaultCurrency']) echo 'selected="selected"'; ?>><?php echo $currencies[$i]['name']; ?></option>
		<?php
		}
	  ?>
	  </select>	  </td>
    </tr>
		<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_currency_auto_method'];?></strong></td>
	  <td align="left" class="tdText">
	  <select name="config[currecyAuto]">
		<option value="0" <?php if($config['currecyAuto']==0) echo 'selected="selected"'; ?> onmouseover="showHideLayers('currencyExtra','','hide');">
		<?php echo $lang['admin']['settings_currency_csv'];?>
		</option>
	  <!--
	  <option value="1" <?php if($config['currecyAuto']==1) echo 'selected="selected"'; ?> onmouseover="showHideLayers('currencyExtra','','show');">
	  <?php echo $lang['admin']['settings_currency_pear'];?>
	  </option>
	  -->
	  </select>
	  <!--
	  <span id="currencyExtra" <?php if($config['currecyAuto']==0) { ?>style="visibility:hidden"<?php } ?>>

	  <?php echo $lang['admin']['settings_source_exchange'];?>
	  <select name="config[Source]">
		<option value="ECB" <?php if($config['exchangeSource']=="ECB") echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_ecb']; ?></option>
	    <option value="NBI" <?php if($config['exchangeSource']=="NBI") echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_nbi']; ?></option>
		<option value="NBP" <?php if($config['exchangeSource']=="NBP") echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_nbp']; ?></option>
	  </select>

	  </span>
	  -->
	  </td>
    </tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_inc_tax_prices'];?></strong></td>
		<td align="left">
			<select name="config[priceIncTax]" class="textbox">
				<option value="0" <?php if($config['priceIncTax']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
				<option value="1" <?php if($config['priceIncTax']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
			</select>
	  </td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_tax_del_inv'];?></strong></td>
		<td align="left">
			<select name="config[priceTaxDelInv]" class="textbox">
				<option value="0" <?php if($config['priceTaxDelInv']==0) echo 'selected="selected"'; ?>>
				<?php echo $lang['admin']['settings_tax_del_add'];?>
				</option>
				<option value="1" <?php if($config['priceTaxDelInv']==1) echo 'selected="selected"'; ?>>
				<?php echo $lang['admin']['settings_tax_inv_add'];?>
				</option>
				<!--
				<option value="2" <?php if($config['priceTaxDelInv']==2) echo 'selected="selected"'; ?>>
				<?php echo $lang['admin']['settings_tax_either_add'];?>
				-->
				</option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_sale_mode'];?></strong></td>
		<td align="left">
			<select name="config[saleMode]" class="textbox">
				<option value="2" <?php if($config['saleMode']==2) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_percent_of_all'];?></option>
				<option value="1" <?php if($config['saleMode']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_ind_sale_per_item'];?></option>
				<option value="0" <?php if($config['saleMode']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_sale_mode_off'];?></option>
			</select>		</td>
	</tr>
	<tr>
	<td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_sale_per_off'];?></strong><br />
	  <?php echo $lang['admin']['settings_sale_per_off_desc'];?></td>
		<td align="left"><input type="text" size="5" class="textbox" name="config[salePercentOff]" value="<?php echo $config['salePercentOff']; ?>" /></td>
	</tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_diff_dispatch'];?></strong></td>
	  <td align="left">
	  <select name="config[shipAddressLock]" class="textbox">
        <option value="0" <?php if($config['shipAddressLock']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
        <option value="1" <?php if($config['shipAddressLock']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
      </select></td>
    </tr>
	<tr>
<td>&nbsp;</td>
<td><input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
</tr>
</table>
<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
	<tr>
	  <td colspan="2" class="tdTitle" id="off_line_settings"><?php echo $lang['admin']['settings_off_line_settings'];?></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_off_line'];?></strong></td>
	  <td align="left">
	  <select name="config[offLine]" class="textbox">
        <option value="1" <?php if($config['offLine']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
        <option value="0" <?php if($config['offLine']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
      </select></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_off_line_allow_admin'];?></strong></td>
	  <td align="left">
	  <select name="config[offLineAllowAdmin]" class="textbox">
        <option value="1" <?php if($config['offLineAllowAdmin']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
        <option value="0" <?php if($config['offLineAllowAdmin']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
      </select></td>
    </tr>
	<tr>
	  <td valign="top" class="tdText"><strong><?php echo $lang['admin']['settings_off_line_content'];?></strong></td>
	  <td align="left">&nbsp;</td>
    </tr>
	<tr>
	  <td colspan="2" valign="top" class="tdText">
	    <?php
			require($glob['adminFolder']."/includes".CC_DS."rte".CC_DS."fckeditor.php");
			$oFCKeditor = new FCKeditor('FCKeditor');
			$oFCKeditor->BasePath = $GLOBALS['rootRel'].$glob['adminFolder'].'/includes/rte/';
			$oFCKeditor->Value = stripslashes(base64_decode($config['offLineContent']));
			if (!$config['richTextEditor']) {
				$oFCKeditor->off = true;
			}
			$oFCKeditor->Create();
		?>
	  </td>
    </tr>

	<tr>
	<td width="30%" class="tdText">&nbsp;</td>
	  <td align="left">
	  <input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
	</tr>
</table>


<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%">
	<tr>
	  <td colspan="2" class="tdTitle" id="proxy"><?php echo $lang['admin']['settings_proxy'];?></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_use_proxy'];?></strong></td>
	  <td align="left">
	  <select name="config[proxy]" class="textbox">
        <option value="0" <?php if($config['proxy']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
		<option value="1" <?php if($config['proxy']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
      </select>
	  </td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_proxy_host'];?></strong></td>
	  <td align="left"><input type="text" size="30" class="textbox" name="config[proxyHost]" value="<?php echo $config['proxyHost']; ?>" /></td>
    </tr>
	<tr>
	  <td valign="top" class="tdText"><strong><?php echo $lang['admin']['settings_proxy_port'];?></strong></td>
	  <td align="left"><input type="text" size="5" class="textbox" name="config[proxyPort]" value="<?php echo $config['proxyPort']; ?>" /></td>
    </tr>

	<tr>
	<td width="30%" class="tdText">&nbsp;</td>
	  <td align="left">
	  <input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
	</tr>
</table>


<p><a href="#pageTop" class="txtLink"><?php echo $lang['admin']['settings_top'];?></a></p>
<table border="0" cellspacing="1" cellpadding="3" class="mainTable" width="100%" id="sef">
	<tr>
	  <td colspan="2" class="tdTitle"><?php echo $lang['admin']['settings_sef'];?></td>
    </tr>
	<tr>
	  <td width="30%" class="tdText"><?php echo $lang['admin']['settings_use_seo']; ?></td>
	  <td align="left">
	  <select name="config[sef]" class="textbox">
        <option value="1" <?php if($config['sef']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['yes'];?></option>
        <option value="0" <?php if($config['sef']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin_common']['no'];?></option>
      </select></td>
    </tr>
<?php
if($config['sef']) {
?>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_url_method'];?></strong><br />
	  <?php echo $lang['admin']['settings_seo_method']; ?></td>
	  <td align="left">
	  <select name="config[sefserverconfig]" class="textbox">
	    <?php if(stripos($_SERVER['SERVER_SOFTWARE'], 'zeus') !== false) { ?>
	    <option value="4" <?php if($config['sefserverconfig']==4) echo 'selected="selected"'; ?>>Zeus Rewrite Script (Recommended)</option>
	    <?php } else { ?>
        <option value="0" <?php if($config['sefserverconfig']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_seo_method_mod_rewrite'];?></option>
        <?php } ?>
        <option value="2" <?php if($config['sefserverconfig']==2 || $config['sefserverconfig']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_seo_method_lookback'];?></option>
        <option value="3" <?php if($config['sefserverconfig']==3) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_seo_method_ftp'];?></option>
      </select>	  </td>
    </tr>
	<?php
	if (in_array($config['sefserverconfig'], array(0))) {
	?>
	<tr>
	  <td valign="top" class="tdText"><p><strong>.htaccess</strong>
	  <br /><?php echo $lang['admin']['settings_seo_htaccess']; ?></td>
	  <td align="left" class="tdText">
	  	<textarea cols="50" rows="15" wrap="off"><?php
	  	$htaccess_conts = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-htaccess.txt');
	  	if($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) {
			$htaccess_conts = str_replace("RewriteEngine On","RewriteEngine On\nRewriteBase ".$glob['rootRel'],$htaccess_conts);
		}
		echo $htaccess_conts;
	  	?>
	  	</textarea><br />
		<br />
		<input type="submit" name="install_htaccess" class="submit" id="install_htaccess" value="Install .htaccess" />
	  </td>
    </tr>
    <?php
	} else if ($config['sefserverconfig'] == 3) {
	?>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_seo_generate_pages'];?></strong><br />
<?php echo $lang['admin']['settings_seo_generate_pages_desc'];?></td>
	  <td align="left" class="tdText">
<strong><?php echo $lang['admin']['settings_ftp_server'];?></strong> <input type="text" size="25" class="textbox" name="config[ftp_server]" value="<?php echo $config['ftp_server'];?>" /><br />
<strong><?php echo $lang['admin']['settings_ftp_user'];?> </strong><input type="text" size="25" class="textbox" name="config[ftp_username]" value="<?php echo $config['ftp_username'];?>" /><br />
<strong><?php echo $lang['admin']['settings_ftp_pass'];?></strong> <input type="text" size="25" class="textbox" name="config[ftp_password]" value="<?php echo $config['ftp_password'];?>" /><br />
<strong><?php echo $lang['admin']['settings_ftp_dir'];?></strong> <input type="text" size="25" class="textbox" name="config[ftp_root_dir]" value="<?php echo $config['ftp_root_dir'];?>" /><br />
		<?php echo sprintf($lang['admin']['settings_seo_generate_pages_inst'],$glob['adminFile']."?_g=settings/sef_genpages");?>      </td>
    </tr>
<?php
	} else if ($config['sefserverconfig'] == 4) {
?>
	<tr>
	  <td valign="top" class="tdText"><p><strong>rewrite.script</strong>
	  <br />To use either "Zeus Rewrite Script" it is required that a "rewrite.script" file is created in the root directory of your store. To do this please open a text editor such as Notepad or TextEdit, copy and paste the contents of the text area opposite into it and save it as "rewrite.script.txt". Upload this file to your server and rename it to "rewrite.script".</td>
	  <td align="left" class="tdText">
	  	<textarea cols="50" rows="15" wrap="off"><?php
	  	$seo_rewrite_script = file_get_contents($glob['adminFolder'].CC_DS.'sources'.CC_DS.'settings'.CC_DS.'seo-rewrite.script.txt');
	  	echo str_replace("{VAL_ROOT_REL}",$glob['rootRel'],$seo_rewrite_script);
	  	?>
	  	</textarea><br />
		<br />
		<input type="submit" name="install_rewrite_script" class="submit" id="install_rewrite_script" value="Install rewrite.script" />
	  </td>
    </tr>
<?php
	}
}
?>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_meta_behaviour'];?></strong> <br />
<?php echo $lang['admin']['settings_meta_behaviour_desc'];?></td>
	  <td align="left">
	  <select name="config[seftags]" class="textbox">
        <option value="2" <?php if($config['seftags']==2) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_meta_or_glob_desc_key'];?></option>
        <option value="1" <?php if($config['seftags']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_meta_combined'];?></option>
        <option value="0" <?php if($config['seftags']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_meta_disabled'];?></option>
      </select></td>
    </tr>
    <?php
	if($config['seftags']) {
	?>
	<tr>
	  <td width="30%" class="tdText"><strong><?php echo $lang['admin']['settings_meta_browser_title_format'];?></strong> <br />
<?php echo $lang['admin']['settings_meta_browser_cat_and_prod'];?></td>
	  <td align="left">
	  <select name="config[sefprodnamefirst]" class="textbox">
        <option value="1" <?php if($config['sefprodnamefirst']==1) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_seo_prod_name_cat_cat'];?></option>
        <option value="0" <?php if($config['sefprodnamefirst']==0) echo 'selected="selected"'; ?>><?php echo $lang['admin']['settings_seo_cat_cat_prod'];?></option>
      </select></td>
    </tr>
<?php
	}
?>

<tr>
	<td width="30%" class="tdText">&nbsp;</td>
	  <td align="left">
	  <input name="submit" type="submit" class="submit" id="submit" value="<?php echo $lang['admin']['settings_update_settings'];?>" /></td>
	</tr>
</table>
</form>