CREATE TABLE IF NOT EXISTS `CubeCart_admin_log` (
`id` int(11) UNSIGNED NOT NULL auto_increment,
`user` varchar(255) NULL,
`desc` text NULL,
`time` int(11) NOT NULL DEFAULT '0',
`ipAddress` varchar(45) NOT NULL DEFAULT '0.0.0.0',
PRIMARY KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_filemanager` (
  `file_id` int(10) unsigned NOT NULL auto_increment,
  `type` int(1) NOT NULL default '1',
  `disabled` int(1) NOT NULL default '0',
  `filepath` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `mimetype` varchar(50) NOT NULL,
  `md5hash` varchar(32) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1; #EOQ


ALTER TABLE `CubeCart_admin_permissions` CHANGE `permissionId` `permissionId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT ; #EOQ
ALTER TABLE `CubeCart_admin_permissions` CHANGE `sectId` `sectId` int(11) UNSIGNED NOT NULL DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_admin_permissions` CHANGE `adminId` `adminId` INT(11) UNSIGNED NOT NULL DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_admin_permissions` CHANGE `read` `read` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_admin_permissions` CHANGE `write` `write` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_admin_permissions` CHANGE `edit` `edit` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_admin_permissions` CHANGE `delete` `delete` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ


ALTER TABLE `CubeCart_admin_sections`
CHANGE `sectId` `sectId` smallint(6) UNSIGNED NOT NULL auto_increment,
CHANGE `name` `name` varchar(50) NULL ; #EOQ
 

ALTER TABLE `CubeCart_admin_sessions`
CHANGE `username` `username` varchar(255) NULL,
CHANGE `success` `success` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ

ALTER TABLE `CubeCart_admin_users` CHANGE `notes` `notes` text NULL; #EOQ
ALTER TABLE `CubeCart_admin_users` CHANGE `isSuper` `isSuper` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_admin_users` ADD `failLevel` tinyint(1) NOT NULL DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_admin_users` ADD `blockTime` int(10) NOT NULL DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_admin_users` ADD `lastTime` int(10) NOT NULL DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_admin_users` ADD `sessId` varchar(32) NULL ; #EOQ
ALTER TABLE `CubeCart_admin_users` ADD `browser` text NULL; #EOQ
ALTER TABLE `CubeCart_admin_users` ADD `sessIp` varchar(45) NOT NULL DEFAULT '0.0.0.0'; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_alt_shipping` (
`id` int(11) UNSIGNED NOT NULL auto_increment,
`name` varchar(255) NULL,
`status` TINYINT(1) NOT NULL DEFAULT '0',
`byprice` TINYINT(1) NOT NULL DEFAULT '0',
`global` TINYINT(1) NOT NULL DEFAULT '0',
`notes` varchar(255) NULL,
`order` int(11) UNSIGNED DEFAULT '0',
KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_alt_shipping_prices` (
`id` int(11) UNSIGNED NOT NULL auto_increment,
`alt_ship_id` int(11) NULL,
`low` decimal(30,3) NOT NULL default '0.000',
`high` decimal(30,3) NOT NULL default '0.000',
`price` decimal(30,2) NOT NULL default '0.00',
KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

ALTER TABLE `CubeCart_alt_shipping_prices`
CHANGE `low` `low` DECIMAL(30,3) NOT NULL DEFAULT '0.000',
CHANGE `high` `high` DECIMAL(30,3) NOT NULL DEFAULT '0.000' ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_blocker` (
`id` int(11) UNSIGNED NOT NULL auto_increment,
`browser` text NULL,
`ip` varchar(45) NOT NULL DEFAULT '0.0.0.0',
`username` varchar(50) NULL,
`blockTime` int(10) NOT NULL default '0',
`blockLevel` tinyint(1) NOT NULL default '0',
`loc` char(1) NULL,
`lastTime` int(10) NULL,
KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


ALTER TABLE `CubeCart_category` CHANGE `cat_name` `cat_name` varchar(100) NULL ; #EOQ
ALTER TABLE `CubeCart_category` ADD `cat_desc` text NULL AFTER `cat_name` ; #EOQ
ALTER TABLE `CubeCart_category` CHANGE `cat_image` `cat_image` varchar(250) NULL; #EOQ
ALTER TABLE `CubeCart_category` ADD `noProducts` INT(11) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_category` ADD `hide` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_category` ADD `cat_metatitle` text NULL ; #EOQ
ALTER TABLE `CubeCart_category` ADD `cat_metadesc` text NULL ; #EOQ
ALTER TABLE `CubeCart_category` ADD `cat_metakeywords` text NULL ; #EOQ
ALTER TABLE `CubeCart_category` ADD `seo_custom_url` TEXT NULL; #EOQ
ALTER TABLE `CubeCart_category` ADD `priority` smallint(6) DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_category` ADD INDEX (`cat_father_id`); # EOQ

UPDATE `CubeCart_category` SET `cat_desc` = NULL WHERE `cat_desc` = ''; #EOQ


ALTER TABLE `CubeCart_cats_lang` ADD `cat_desc` text NULL; #EOQ
ALTER TABLE `CubeCart_cats_lang` ADD INDEX (`cat_master_id`); #EOQ
CREATE TABLE IF NOT EXISTS `CubeCart_cats_lang` (
`id` int(11) UNSIGNED NOT NULL auto_increment,
`cat_master_id` int(11) UNSIGNED NOT NULL default '0',
`cat_lang` varchar(20) NULL,
`cat_name` varchar(255) NULL,
`cat_desc` text NOT NULL default '',
KEY `id` (`id`),
INDEX (`cat_master_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_Coupons` (
`id` INT(10) UNSIGNED NOT NULL auto_increment,
`status` TINYINT(1) NOT NULL DEFAULT '0',
`code` VARCHAR(25) NULL,
`product_id` INT(10) NOT NULL default '0',
`discount_percent` FLOAT NOT NULL default '0',
`discount_price` FLOAT NOT NULL default '0',
`expires` VARCHAR(10) NOT NULL default '',
`allowed_uses` SMALLINT(3) NOT NULL default '0',
`count` SMALLINT(3) NOT NULL default '0',
`desc` TEXT NULL,
`cart_order_id` VARCHAR(30) NULL,
PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


ALTER TABLE `CubeCart_currencies` ADD `decimalSymbol` smallint(1) NOT NULL default '0' ; #EOQ
ALTER TABLE `CubeCart_currencies` CHANGE `symbolLeft` `symbolLeft` varbinary(10) default NULL ; #EOQ
ALTER TABLE `CubeCart_currencies` CHANGE `symbolRight` `symbolRight` varbinary(10) default NULL ; #EOQ


ALTER TABLE `CubeCart_customer` ADD `companyName` VARCHAR(150) NOT NULL default '' AFTER `lastName` ; #EOQ
ALTER TABLE `CubeCart_customer` DROP COLUMN `zoneId` ; #EOQ
ALTER TABLE `CubeCart_customer` CHANGE `country` `country` SMALLINT(3) NOT NULL ; #EOQ
ALTER TABLE `CubeCart_customer` CHANGE `email` `email` varchar(254) NOT NULL default ''; #EOQ


ALTER TABLE `CubeCart_docs` CHANGE `doc_content` `doc_content` text NULL ; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_metatitle` text NULL ; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_metadesc` text NULL ; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_metakeywords` text NULL ; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_order` int(3) default '0' ; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_terms` smallint(1) default '0'; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_url` TEXT NULL; #EOQ
ALTER TABLE `CubeCart_docs` ADD `doc_url_openin` smallint(1) NULL; #EOQ


UPDATE `CubeCart_docs` SET doc_order = doc_id WHERE 1; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_history` (
`id` int(11) UNSIGNED NOT NULL auto_increment,
`version` varchar(50) NULL,
`time` int(11) NULL,
PRIMARY KEY(`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


ALTER TABLE `CubeCart_inventory` ADD `disabled` TINYINT(1) NOT NULL DEFAULT '0' AFTER `productId`; #EOQ
ALTER TABLE `CubeCart_inventory` ADD `stockWarn` INT(3) NULL AFTER `stock_level` ; #EOQ 
ALTER TABLE `CubeCart_inventory` ADD `prod_metatitle` TEXT NULL ; #EOQ
ALTER TABLE `CubeCart_inventory` ADD `prod_metadesc` TEXT NULL ; #EOQ 
ALTER TABLE `CubeCart_inventory` ADD `prod_metakeywords` TEXT NULL ; #EOQ 
ALTER TABLE `CubeCart_inventory` ADD `tax_inclusive` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ
ALTER TABLE `CubeCart_inventory` ADD `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ; #EOQ
ALTER TABLE `CubeCart_inventory` ADD `seo_custom_url` TEXT NULL ; #EOQ
ALTER TABLE `CubeCart_inventory` ADD FULLTEXT `fulltext` (`productCode`,`description`,`name`) ; #EOQ
ALTER TABLE `CubeCart_inventory` CHANGE `prodWeight` `prodWeight` DECIMAL(10,3) NULL ; #EOQ
ALTER TABLE `CubeCart_inventory` CHANGE `showFeatured` `showFeatured` TINYINT(1) NOT NULL DEFAULT '0'; #EOQ

ALTER TABLE `CubeCart_inv_lang` CHANGE `id` `id` int(11) UNSIGNED NOT NULL auto_increment ; #EOQ
ALTER TABLE `CubeCart_inv_lang` ADD FULLTEXT `fulltext` (`name`,`description`) ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_lang` (
`identifier` varchar(50) NOT NULL default '', 
`langArray` longtext NOT NULL, 
UNIQUE KEY `identifier` (`identifier`)
) TYPE=MyISAM ; #EOQ


ALTER TABLE `CubeCart_order_inv` ADD `stockUpdated` TINYINT(1) NOT NULL DEFAULT '0' ; #EOQ
ALTER TABLE `CubeCart_order_inv` ADD `custom` text NULL ; #EOQ
ALTER TABLE `CubeCart_order_inv` ADD `couponId` smallint(6) NULL; #EOQ
ALTER TABLE `CubeCart_order_inv` ADD INDEX (`productId`); # EOQ
ALTER TABLE `CubeCart_order_inv` ADD INDEX (`cart_order_id`); # EOQ

DROP TABLE IF EXISTS `CubeCart_order_state` ; #EOQ


ALTER TABLE `CubeCart_order_sum` ADD `discount` decimal(30,2) default '0.00' AFTER `subtotal` ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `tax1_disp` varchar(128) NULL ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `tax1_amt` decimal(30,2) NOT NULL default '0.00' ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `tax2_disp` varchar(128) NULL ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `tax2_amt` decimal(30,2) NOT NULL default '0.00' ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `tax3_disp` varchar(128) NULL ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `tax3_amt` decimal(30,2) NOT NULL default '0.00' ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `offline_capture` blob NULL ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `courier_tracking` text NULL; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `companyName` varchar(150) NULL ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `companyName_d` varchar(150) NULL ; #EOQ

ALTER TABLE `CubeCart_order_sum` ADD `extra_notes` TEXT NOT NULL DEFAULT '' AFTER `customer_comments` ; #EOQ
ALTER TABLE `CubeCart_order_sum` ADD `basket` TEXT NULL ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_reviews` (
  `id` int(11) UNSIGNED NOT NULL auto_increment,
  `approved` TINYINT(1) NOT NULL DEFAULT '0',
  `productId` int(11) UNSIGNED NOT NULL,
  `type` TINYINT(1) NOT NULL DEFAULT '0',
  `rating` smallint(1) NULL,
  `name` varchar(255) NULL,
  `email` varchar(255) NULL,
  `title` varchar(255) NULL,
  `review` text NULL,
  `ip` varchar(45) NOT NULL DEFAULT '0.0.0.0',
  `time` int(10) NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


ALTER TABLE `CubeCart_sessions` ADD `skin` varchar(25) NULL AFTER `location` ; #EOQ
ALTER TABLE `CubeCart_sessions` ADD `ip` varchar(45) NOT NULL DEFAULT '0.0.0.0' ; #EOQ
ALTER TABLE `CubeCart_sessions` ADD `browser` text NULL ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_SpamBot` (
  `uniqueId` varchar(32) NULL,
  `spamCode` varchar(5) NULL,
  `userIp` varchar(45) NOT NULL DEFAULT '0.0.0.0',
  `time` int(10) NULL,
  PRIMARY KEY  (`uniqueId`),
  UNIQUE KEY `uniqueId` (`uniqueId`)
) TYPE=MyISAM ; #EOQ


ALTER TABLE `CubeCart_taxes` CHANGE `percent` `percent` decimal(7,4) NOT NULL default '0.0000' ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_tax_details` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(128) NULL,
`display` varchar(128) NULL,
`reg_number` varchar(128) NULL,
`status` TINYINT(1) NOT NULL DEFAULT '1',
PRIMARY KEY  (`id`),
UNIQUE KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_tax_rates` (
  `id` int(11) UNSIGNED NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '1',
  `details_id` int(11) NOT NULL default '0',
  `country_id` int(11) NOT NULL default '0',
  `county_id` int(11) NOT NULL default '0',
  `tax_percent` decimal(7,4) NOT NULL default '0.0000',
  `goods` TINYINT(1) NOT NULL DEFAULT '0',
  `shipping` TINYINT(1) NOT NULL DEFAULT '0',
  `active` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type_id` (`type_id`,`details_id`,`country_id`,`county_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_transactions` (
  `id` int(11) UNSIGNED NOT NULL auto_increment,
  `gateway` varchar(255) NULL,
  `status` varchar(50) NULL,
  `customer_id` int(11) UNSIGNED NULL,
  `order_id` varchar(255) NULL,
  `trans_id` varchar(50) NULL,
  `time` int(10) NULL,
  `amount` decimal(30,2) NULL,
  `notes` text NULL,
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


ALTER TABLE `CubeCart_options_top` ADD `option_type` TINYINT NOT NULL DEFAULT '0'; #EOQ

ALTER TABLE `CubeCart_Coupons` CHANGE `discount_percent` `discount_percent` DECIMAL( 30, 2 ) NOT NULL DEFAULT '0.00' ; #EOQ
ALTER TABLE `CubeCart_Coupons` CHANGE `discount_price` `discount_price` DECIMAL( 30, 2 ) NOT NULL DEFAULT '0.00' ; #EOQ

ALTER TABLE `CubeCart_Modules` CHANGE `module` `module` VARCHAR( 25 ) NULL ; #EOQ

DELETE FROM `CubeCart_Modules` WHERE `folder` = "DirectPayment" ; #EOQ

DELETE FROM `CubeCart_Config` WHERE `name` = "DirectPayment" ; #EOQ

ALTER TABLE `CubeCart_transactions` ADD `remainder` DECIMAL( 30, 2 ) NOT NULL DEFAULT '0.00' AFTER `amount` ; #EOQ

ALTER TABLE `CubeCart_transactions` ADD `extra` VARCHAR( 255 ) NULL AFTER `gateway` ; #EOQ

DELETE FROM `CubeCart_admin_sections` WHERE `name` = 'offers'; #EOQ
DELETE FROM `CubeCart_admin_sections` WHERE `name` = 'reviews'; #EOQ
DELETE FROM `CubeCart_admin_sections` WHERE `name` = 'gateway'; #EOQ
INSERT INTO `CubeCart_admin_sections` (`sectId`, `name`, `description`) VALUES (11, 'offers', 'Special offers &amp; promotions.'); #EOQ
INSERT INTO `CubeCart_admin_sections` (`sectId`, `name`, `description`) VALUES (12, 'reviews', 'Customer reviews &amp; comments.'); #EOQ
INSERT INTO `CubeCart_admin_sections` (`sectId`, `name`, `description`) VALUES (13, 'gateway', 'For the administration of payment gateways.'); #EOQ

UPDATE `CubeCart_customer` AS C SET C.county = (SELECT name FROM `CubeCart_iso_counties` WHERE abbrev != '' AND abbrev = UPPER(C.county) LIMIT 1) WHERE UPPER(C.county) IN (SELECT abbrev FROM `CubeCart_iso_counties` WHERE 1); #EOQ

ALTER TABLE `CubeCart_customer` ADD `salt` VARCHAR( 6 ) NOT NULL AFTER `password`; #EOQ

ALTER TABLE `CubeCart_admin_users` ADD `salt` VARCHAR( 6 ) NOT NULL AFTER `password`; #EOQ

ALTER TABLE `CubeCart_Modules` ADD UNIQUE (`folder`); #EOQ

ALTER TABLE `CubeCart_order_sum` ADD `lang` VARCHAR( 2 ) NULL ; #EOQ

ALTER TABLE `CubeCart_inventory` CHANGE `eanupcCode` `eanupcCode` VARCHAR( 20 ) NULL DEFAULT NULL ; #EOQ 

UPDATE `CubeCart_config` SET `name` = 'eway' WHERE `name` = 'eWay' ; #EOQ 

UPDATE `CubeCart_Modules` SET `folder` = 'eway' WHERE `folder` = 'eWay' ; #EOQ 

ALTER TABLE  `CubeCart_inventory` CHANGE  `eanupcCode`  `upc` VARCHAR( 12 ) NULL ; #EOQ 

ALTER TABLE  `CubeCart_inventory` ADD  `ean` VARCHAR( 14 ) NULL AFTER  `upc` ; #EOQ 
ALTER TABLE  `CubeCart_inventory` ADD  `jan` VARCHAR( 13 ) NULL AFTER  `ean` ; #EOQ 
ALTER TABLE  `CubeCart_inventory` ADD  `isbn` VARCHAR( 13 ) NULL AFTER  `jan` ; #EOQ 