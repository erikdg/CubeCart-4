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
|	currency.inc.php
|   ========================================
|	Currency Jump Box
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

## include lang file
$lang = getLang('includes'.CC_DS.'boxes'.CC_DS.'currency.inc.php');

$cache = new cache('boxes.currency');
$currencies = $cache->readCache();

if (!$cache->cacheStatus) {
	$currencies = $db->select('SELECT `name`, `code` FROM '.$glob['dbprefix'].'CubeCart_currencies WHERE `active` = 1 ORDER BY `name` ASC');
	$cache->writeCache($currencies);
}

if ($currencies) {
	$box_content = new XTemplate ('boxes'.CC_DS.'currency.tpl');
	$box_content->assign('LANG_CURRENCY_TITLE', $lang['currency']['currency']);
	for ($i = 0, $maxi = count($currencies); $i < $maxi; ++$i){
		if ($cc_session->ccUserData['currency'] == $currencies[$i]['code']) {
			$box_content->assign('CURRENCY_SELECTED', 'selected="selected"');
		} else if (($config['defaultCurrency'] == $currencies[$i]['code']) && empty($cc_session->ccUserData['currency'])) {
			$box_content->assign('CURRENCY_SELECTED', 'selected="selected"');
		} else {
			$box_content->assign('CURRENCY_SELECTED', '');
		}

		$currencyName = (strlen($currencies[$i]['name'])>20) ? substr($currencies[$i]['name'], 0, 18).'&hellip;' : $currencies[$i]['name'];

		$box_content->assign('VAL_CURRENCY', $currencies[$i]['code']);
		$box_content->assign('CURRENCY_NAME', $currencyName);
		$box_content->assign('VAL_CURRENT_PAGE', $returnPage);
		$box_content->parse('currency.option');
	}

	$box_content->parse('currency');
	$box_content = $box_content->text('currency');
} else {
	$box_content = '';
}
?>