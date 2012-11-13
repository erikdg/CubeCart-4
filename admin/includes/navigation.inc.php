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
|	navigation.inc.php
|   ========================================
|	Admin Navigation links
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

$link401 = "href=\"javascript:alert('".$lang['admin_common']['nav_permission_error']."');\" class=\"txtNullLink\"";
?>
<div id="adminNavigation" style="width: 180px;">

	<div style="padding-left: 14px">
		<a href="http://www.cubecart.com" target="_blank">
		  <img src="<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/images/ccAdminLogo.gif" alt="" width="156" height="31" />
		</a>
	</div>

  <div id="menuList" class="navMenu" style="padding-top: 10px;">

	<span class="navTitle" onclick="Effect.toggle('navStoreLinks', 'blind');"><?php echo $lang['admin_common']['nav_navigation'];?></span>
	<ul id="navStoreLinks">
		<li><a href="<?php echo $GLOBALS['rootRel'].$glob['adminFile']; ?>" target="_self" class="txtLink"><?php echo $lang['admin_common']['nav_admin_home'];?></a></li>
		<li><a href="<?php echo $GLOBALS['rootRel']; ?>index.php" target="_blank" class="txtLink"><?php echo $lang['admin_common']['nav_store_home'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreConfig', 'blind');"><?php echo $lang['admin_common']['nav_store_config'];?></span>
	<ul class="navItem" id="navStoreConfig">
		<li><a <?php if(permission('settings','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_gen_settings'];?></a></li>
		<li><a <?php if(permission('settings','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/tax" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_taxes'];?></a></li>
		<li><a <?php if(permission('settings','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/geo" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_countries_zones'];?></a></li>
		<li><a <?php if(permission('settings','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/currency" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_currencies'];?></a></li>
		<li><a <?php if(permission('settings','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=settings/logo" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_logo'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreModules', 'blind');"><?php echo $lang['admin_common']['nav_modules'];?></span>
	<ul class="navItem" id="navStoreModules">
		<li><a <?php if(permission('shipping','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=modules&amp;module=shipping" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_shipping'];?></a></li>
		<li><a <?php if(permission('gateways','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=modules&amp;module=gateway" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_gateways'];?></a></li>
		<li><a <?php if(permission('gateways','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=modules&amp;module=affiliate" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_affiliates'];?></a></li>
		<li><a <?php if(permission('gateways','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=modules&amp;module=altCheckout" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_alt_checkout'];?></a></li>
		<li><a <?php if(permission('filemanager','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/language" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_edit_langs'];?></a></li>
		<li><a <?php if(permission('settings','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=modules&amp;module=installer" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_module_installer']; ?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreCatalog', 'blind');"><?php echo $lang['admin_common']['nav_catalog'];?></span>
	<ul class="navItem" id="navStoreCatalog">
		<li><a <?php if(permission('products','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_view_products'];?></a></li>
		<li><a <?php if(permission('products','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/index&amp;mode=new" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_add_product'];?></a></li>
		<li><a <?php if(permission('products','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/options" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_product_options'];?></a></li>
		<li><a <?php if(permission('reviews','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=reviews/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_prod_reviews'];?></a></li>
		<li><a <?php if(permission('offers','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/coupons" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_coupons'];?></a></li>

		<li><a <?php if(permission('products','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/giftCertificates" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_gift_certificates'];?></a></li>

		<li><a <?php if(permission('categories','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=categories/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_view_categories'];?></a></li>
		<li><a <?php if(permission('categories','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=categories/index&amp;mode=new" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_add_categories'];?></a></li>

		<li><a <?php if(permission('products','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/import" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_import_cat'];?></a></li>
		<li><a <?php if(permission('products','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=products/export" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_export_cat'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreCustomers', 'blind');"><?php echo $lang['admin_common']['nav_customers'];?></span>
	<ul class="navItem" id="navStoreCustomers">
		<li><a <?php if(permission('customers','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=customers/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_view_customers'];?></a></li>
		<li><a <?php if(permission('customers','write')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=customers/email" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_email_customers'];?></a></li>
		<li><a <?php if(permission('orders','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=orders/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_orders'];?></a></li>
		<li><a <?php if(permission('orders','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=orders/transLogs" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_transaction_logs'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreFilemanager', 'blind');"><?php echo $lang['admin_common']['nav_file_manager'];?></span>
	<ul class="navItem" id="navStoreFilemanager">
		<li><a <?php if(permission('filemanager','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_manage_images']?></a></li>
		<li><a <?php if(permission('filemanager','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=filemanager/index"  onclick="openPopUp('<?php echo $GLOBALS['rootRel'].$glob['adminFolder']; ?>/includes/rte/editor/filemanager/browser/default/browser.html?Type=uploads&amp;Connector=<?php echo urlencode($GLOBALS['rootRel'].$glob['adminFolder']); ?>%2Fincludes%2Frte%2Feditor%2Ffilemanager%2Fconnectors%2Fphp%2Fconnector.php','filemanager',700,600)" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_upload_images'];?></a></li>
	</ul>
	<span class="navTitle" onclick="Effect.toggle('navStoreStats', 'blind');"><?php echo $lang['admin_common']['nav_statistics'];?></span>
	<ul class="navItem" id="navStoreStats">
		<li><a <?php if(permission('statistics','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=stats/index" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_view_stats'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreDocuments', 'blind');"><?php echo $lang['admin_common']['nav_documents'];?></span>
	<ul class="navItem" id="navStoreDocuments">
		<li><a <?php if(permission('documents','edit')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=docs/home" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_homepage'];?></a></li>
		<li><a <?php if(permission('documents','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=docs/siteDocs" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_site_docs'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreMisc', 'blind');"><?php echo $lang['admin_common']['nav_misc'];?></span>
	<ul class="navItem" id="navStoreMisc">
		<li><a href="<?php echo $glob['adminFile']; ?>?_g=misc/serverInfo" class="txtLink"><?php echo $lang['admin_common']['nav_server_info'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreUsers', 'blind');"><?php echo $lang['admin_common']['nav_admin_users'];?></span>
	<ul class="navItem" id="navStoreUsers">
		<li><a <?php if(permission('administrators','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_administrators'];?></a></li>
		<li><a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/sessions" class="txtLink"><?php echo $lang['admin_common']['nav_admin_sessions'];?></a></li>
		<li><a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/logs" class="txtLink"><?php echo $lang['admin_common']['nav_admin_logs'];?></a></li>
	</ul>

	<span class="navTitle" onclick="Effect.toggle('navStoreMaintenance', 'blind');"><?php echo $lang['admin_common']['nav_maintenance'];?></span>
	<ul class="navItem" id="navStoreMaintenance">
		<li><a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/database" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_database'];?></a></li>
		<li><a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/backup" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_backup'];?></a></li>
		<li><a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/thumbnails" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_thumbnails'];?></a></li>
		<li><a <?php if(permission('maintenance','read')){ ?>href="<?php echo $glob['adminFile']; ?>?_g=maintenance/rebuild" class="txtLink"<?php } else { echo $link401; } ?>><?php echo $lang['admin_common']['nav_rebuild'];?></a></li>
	</ul>
  </div>
</div>