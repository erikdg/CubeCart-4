CREATE TABLE IF NOT EXISTS `CubeCart_config` (
  `name` varchar(100) NOT NULL default '',
  `array` text NOT NULL,
  KEY `name` (`name`)
) TYPE=MyISAM ; #EOQ
	
CREATE TABLE IF NOT EXISTS `CubeCart_cats_lang` (
  `id` int(11) NOT NULL auto_increment,
  `cat_master_id` int(11) NOT NULL default '0',
  `cat_lang` varchar(20) NOT NULL default '',
  `cat_name` varchar(255) NOT NULL default '',
  `cat_desc` text  NOT NULL,
  PRIMARY KEY `id` (`id`),
  INDEX (`cat_master_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ		
 
CREATE TABLE IF NOT EXISTS `CubeCart_admin_log` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(255) NOT NULL default '',
  `desc` text NULL NOT NULL,
  `time` int(11) NOT NULL default '0',
  `ipAddress` varchar(45) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM  AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_Coupons` (
  `id` int(10) NOT NULL auto_increment,
  `status` tinyint(1) NOT NULL default '1',
  `code` varbinary(25) NOT NULL default '',
  `product_id` int(10) NOT NULL default '0',
  `discount_percent` decimal(30,2) NOT NULL default '0.00',
  `discount_price` decimal(30,2) NOT NULL default '0.00',
  `expires` varbinary(10) NOT NULL default '',
  `allowed_uses` smallint(1) NOT NULL default '0',
  `count` smallint(1) NOT NULL default '0',
  `desc` blob NOT NULL,
  `cart_order_id` varbinary(30) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_lang` (
  `identifier` varchar(50) NOT NULL default '',
  `langArray` longtext NOT NULL,
  UNIQUE KEY `identifier` (`identifier`)
) TYPE=MyISAM ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_tax_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `display` varchar(128) NOT NULL default '',
  `reg_number` varchar(128) NOT NULL default '',
  `status` smallint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_tax_rates` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL default '1',
  `details_id` int(11) NOT NULL default '0',
  `country_id` int(11) NOT NULL default '0',
  `county_id` int(11) NOT NULL default '0',
  `tax_percent` decimal(7,4) NOT NULL default '0.0000',
  `goods` int(11) NOT NULL default '0',
  `shipping` int(11) NOT NULL default '0',
  `active` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type_id` (`type_id`,`details_id`,`country_id`,`county_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_reviews` (
  `id` int(11) NOT NULL auto_increment,
  `approved` smallint(1) NOT NULL default '0',
  `productId` int(11) NOT NULL,
  `type` smallint(1) NOT NULL,
  `rating` smallint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `title` varchar(255) NOT NULL,
  `review` text NOT NULL,
  `ip` varchar(45) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_alt_shipping` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `status` smallint(1) NOT NULL default '0',
  `byprice` smallint(1) NOT NULL,
  `global` smallint(1) NOT NULL,
  `notes` varchar(255) default NULL,
  `order` int(11) default '0',
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_alt_shipping_prices` (
  `id` int(11) NOT NULL auto_increment,
  `alt_ship_id` int(11) NOT NULL,
  `low` decimal(30,3) NOT NULL default '0.000',
  `high` decimal(30,3) NOT NULL default '0.000',
  `price` decimal(30,3) NOT NULL default '0.000',
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_transactions` (
  `id` int(11) NOT NULL auto_increment,
  `gateway` varchar(255) default NULL,
  `extra` varchar(255) default NULL,
  `status` varchar(50) default NULL,
  `customer_id` int(11) default NULL,
  `order_id` varchar(255) default NULL,
  `trans_id` varchar(50) default NULL,
  `time` int(10) default NULL,
  `amount` decimal(30,2) default NULL,
  `remainder` decimal(30,2) NOT NULL DEFAULT '0.00',
  `notes` text,
  PRIMARY KEY `id` (`id`),
  INDEX (`customer_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_blocker` (
  `id` int(11) NOT NULL auto_increment,
  `browser` text NOT NULL,
  `ip` varchar(45) NOT NULL,
  `username` varchar(50) NOT NULL,
  `blockTime` int(10) NOT NULL default '0',
  `blockLevel` smallint(1) NOT NULL default '0',
  `loc` char(1) NOT NULL,
  `lastTime` int(10) NOT NULL,
  KEY `id` (`id`)
) TYPE=MyISAM  AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_category` (
  `cat_id` int(16) UNSIGNED NOT NULL auto_increment,
  `cat_name` varchar(100) NULL,
  `cat_desc` text NULL,
  `cat_father_id` int(16) UNSIGNED NOT NULL DEFAULT '0',
  `cat_image` varbinary(250) NULL,
  `per_ship` decimal(20,2) NOT NULL DEFAULT '0.00',
  `item_ship` decimal(20,2) NOT NULL DEFAULT '0.00',
  `item_int_ship` decimal(20,2) NOT NULL DEFAULT '0.00',
  `per_int_ship` decimal(20,2) NOT NULL DEFAULT '0.00',
  `noProducts` INT(11) NOT NULL DEFAULT '0',
  `hide` TINYINT(1) NOT NULL DEFAULT '0',
  `cat_metatitle` text NULL,
  `cat_metadesc` text NULL,
  `cat_metakeywords` text NULL,
  `seo_custom_url` TEXT NULL,
  `priority` smallint(6) NULL,
  PRIMARY KEY  (`cat_id`),
  INDEX (`cat_father_id`)
) ENGINE=MyISAM ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_SpamBot` (
  `uniqueId` varchar(32) NOT NULL default '',
  `spamCode` varchar(5) NOT NULL default '',
  `userIp` varchar(45) NOT NULL default '',
  `time` int(10) NOT NULL default '0',
  PRIMARY KEY  (`uniqueId`),
  UNIQUE KEY `uniqueId` (`uniqueId`)
) TYPE=MyISAM ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_history` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(50) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ  

CREATE TABLE IF NOT EXISTS `CubeCart_Downloads` (
  `id` int(11) NOT NULL auto_increment,
  `customerId` int(11) NOT NULL default '0',
  `cart_order_id` varchar(32) NOT NULL default '',
  `noDownloads` int(11) NOT NULL default '0',
  `expire` int(11) NOT NULL default '0',
  `productId` int(11) NOT NULL default '0',
  `accessKey` varchar(10) NOT NULL default '',
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_Modules` (
  `moduleId` int(11) NOT NULL auto_increment,
  `module` varchar(25) collate utf8_unicode_ci NOT NULL default '',
  `folder` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `status` smallint(1) NOT NULL default '0',
  `default` smallint(1) NOT NULL default '0',
  UNIQUE KEY `folder` (`folder`),
  KEY `moduleId` (`moduleId`)
) TYPE=MyISAM AUTO_INCREMENT=1; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_admin_permissions` (
`permissionId` SMALLINT NOT NULL AUTO_INCREMENT ,
`sectId` SMALLINT NOT NULL DEFAULT '0',
`adminId` SMALLINT NOT NULL DEFAULT '0',
`read` SMALLINT NOT NULL DEFAULT '0',
`write` SMALLINT NOT NULL DEFAULT '0',
`edit` SMALLINT NOT NULL DEFAULT '0',
`delete` SMALLINT NOT NULL DEFAULT '0',
  PRIMARY KEY  (`permissionId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_admin_sections` (
  `sectId` smallint(6) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  KEY `sectId` (`sectId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_admin_sessions` (
  `loginId` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `ipAddress` varchar(45) NOT NULL default '',
  `success` int(11) NOT NULL default '0',
  KEY `loginId` (`loginId`)
) TYPE=MyISAM AUTO_INCREMENT=1; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_admin_users` (
  `adminId` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(255) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `salt` VARCHAR( 6 ) NOT NULL default '',
  `email` varchar(254) NOT NULL default '',
  `noLogins` int(11) NOT NULL default '0',
  `isSuper` int(11) NOT NULL default '0',
  `notes` text,
  `sessId` varchar(32) NULL,
  `browser` text NULL,
  `sessIp` varchar(45) NULL,
  `failLevel` smallint( 1 ) NOT NULL DEFAULT '0',
  `blockTime` INT( 10 ) NOT NULL DEFAULT '0',
  `lastTime` INT( 10 ) NOT NULL DEFAULT '0',
  KEY `adminId` (`adminId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_cats_idx` (
  `id` int(11) NOT NULL auto_increment,
  `cat_id` int(11) NOT NULL default '0',
  `productId` int(11) NOT NULL default '0',
  PRIMARY KEY `id` (`id`),
  INDEX (`cat_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_currencies` (
  `currencyId` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `code` varchar(5) NOT NULL default '',
  `symbolLeft` varbinary(10) default NULL,
  `symbolRight` varbinary(10) default NULL,
  `value` decimal(10,5) NOT NULL default '0.00000',
  `decimalPlaces` int(11) NOT NULL default '0',
  `lastUpdated` int(10) NOT NULL default '0',
  `active` smallint(1) NOT NULL default '0',
  `decimalSymbol` smallint(1) NOT NULL default '0',
  KEY `curencyId` (`currencyId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_customer` (
  `email` varchar(254) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `salt` VARCHAR( 6 ) NOT NULL default '',
  `title` varchar(10) default NULL,
  `firstName` varchar(255) NOT NULL default '',
  `lastName` varchar(255) NOT NULL default '',
  `companyName` varchar(150) NOT NULL default '',
  `add_1` varchar(100) NOT NULL default '',
  `add_2` varchar(100) NOT NULL default '',
  `town` varchar(100) NOT NULL default '',
  `county` varchar(100) NOT NULL default '',
  `postcode` varchar(15) NOT NULL default '',
  `country` int(3) NOT NULL,
  `phone` varchar(20) NOT NULL default '',
  `mobile` varchar(50) default NULL,
  `customer_id` int(11) NOT NULL auto_increment,
  `regTime` int(10) NOT NULL default '0',
  `ipAddress` varchar(45) NOT NULL default '',
  `noOrders` int(11) default '0',
  `optIn1st` int(11) NOT NULL default '0',
  `htmlEmail` int(11) NOT NULL default '1',
  `type` int(11) default '0',
  PRIMARY KEY  (`customer_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_docs` (
  `doc_name` varchar(255) NOT NULL default '',
  `doc_content` text NOT NULL,
  `doc_id` int(16) NOT NULL auto_increment,
  `doc_metatitle` text NOT NULL,
  `doc_metadesc` text NOT NULL,
  `doc_metakeywords` text NOT NULL,
  `doc_order` int(11) default '0',
  `doc_terms` smallint(1) default '0',
  `doc_url` TEXT NULL,
  `doc_url_openin` smallint(1) default '0',
  KEY `doc_id` (`doc_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_docs_lang` (
  `id` int(11) NOT NULL auto_increment,
  `doc_master_id` int(11) NOT NULL default '0',
  `doc_lang` varchar(20) NOT NULL default '',
  `doc_name` varchar(255) NOT NULL default '',
  `doc_content` text NOT NULL,
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

CREATE TABLE IF NOT EXISTS `CubeCart_img_idx` (
  `id` int(11) NOT NULL auto_increment,
  `productId` int(11) NOT NULL default '0',
  `img` varchar(255) NOT NULL default '',
  PRIMARY KEY `id` (`id`),
  INDEX (`productId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_inv_lang` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `prod_lang` varchar(20) NOT NULL,
  `prod_master_id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  PRIMARY KEY `id` (`id`),
  INDEX (`prod_master_id`),
  FULLTEXT KEY `fulltext` (`name`,`description`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


CREATE TABLE IF NOT EXISTS `CubeCart_inventory` (
  `productId` int(11) NOT NULL AUTO_INCREMENT,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `productCode` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(16) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci,
  `image` varbinary(250) DEFAULT NULL,
  `noImages` int(11) NOT NULL DEFAULT '0',
  `price` decimal(30,2) NOT NULL DEFAULT '0.00',
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cat_id` int(16) NOT NULL DEFAULT '0',
  `popularity` bigint(64) NOT NULL DEFAULT '0',
  `sale_price` decimal(30,2) NOT NULL DEFAULT '0.00',
  `stock_level` int(11) NOT NULL DEFAULT '0',
  `stockWarn` tinyint(1) NOT NULL DEFAULT '0',
  `useStockLevel` int(11) NOT NULL DEFAULT '1',
  `digital` int(11) NOT NULL DEFAULT '0',
  `digitalDir` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prodWeight` decimal(10,3) DEFAULT NULL,
  `taxType` int(11) DEFAULT NULL,
  `tax_inclusive` tinyint(1) NOT NULL DEFAULT '0',
  `showFeatured` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `prod_metatitle` text COLLATE utf8_unicode_ci,
  `prod_metadesc` text COLLATE utf8_unicode_ci,
  `prod_metakeywords` text COLLATE utf8_unicode_ci,
  `upc` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ean` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jan` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isbn` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seo_custom_url` text COLLATE utf8_unicode_ci,
  PRIMARY KEY  (`productId`),
  KEY `popularity` (`popularity`),
  INDEX (`cat_id`),
  FULLTEXT KEY `fulltext` (`productCode`,`description`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_iso_counties` (
  `id` int(11) NOT NULL auto_increment,
  `countryId` smallint(4) NOT NULL default '0',
  `abbrev` varchar(4) NOT NULL default '',
  `name` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`),
  INDEX (`countryId`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_iso_countries` (
  `id` int(11) NOT NULL auto_increment,
  `iso` char(2) NOT NULL default '',
  `printable_name` varchar(80) NOT NULL default '',
  `iso3` char(3) default NULL,
  `numcode` smallint(6) default NULL,
  PRIMARY KEY  (`iso`),
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_options_bot` (
  `assign_id` int(32) NOT NULL auto_increment,
  `product` int(11) NOT NULL default '0',
  `option_id` int(32) NOT NULL default '0',
  `value_id` int(32) NOT NULL default '0',
  `option_price` decimal(30,2) NOT NULL default '0.00',
  `option_symbol` char(1) NOT NULL default '',
  PRIMARY KEY  (`assign_id`),
  INDEX (`product`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_options_mid` (
  `value_id` int(16) NOT NULL auto_increment,
  `value_name` varchar(30) NOT NULL default '',
  `father_id` int(16) NOT NULL default '0',
  PRIMARY KEY  (`value_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_options_top` (
  `option_id` int(16) NOT NULL auto_increment,
  `option_name` varchar(30) NOT NULL default '',
  `option_type` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY  (`option_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_order_inv` (
  `productId` int(11) NOT NULL default '0',
  `productCode` varchar(255) NOT NULL default '',
  `name` varchar(225) NOT NULL default '',
  `quantity` smallint(4) NOT NULL default '0',
  `price` decimal(30,2) NOT NULL default '0.00',
  `cart_order_id` varchar(30) NOT NULL default '',
  `id` int(32) NOT NULL auto_increment,
  `product_options` text NOT NULL,
  `digital` smallint(1) NOT NULL default '0',
  `stockUpdated` int(11) NOT NULL default '0',
  `custom` text NOT NULL, 
  `couponId` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  INDEX (`productId`),
  INDEX (`cart_order_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_order_sum` (
  `cart_order_id` varchar(30) NOT NULL,
  `customer_id` int(11) NOT NULL default '0',
  `name` varchar(225) NULL,
  `add_1` varchar(225) NULL,
  `add_2` varchar(225) NULL,
  `town` varchar(225) NULL,
  `county` varchar(225) NULL,
  `postcode` varchar(225) NULL,
  `country` varchar(225) NULL,
  `name_d` varchar(225) NULL,
  `add_1_d` varchar(225) NULL,
  `add_2_d` varchar(225) NULL,
  `town_d` varchar(225) NULL,
  `county_d` varchar(225) NULL,
  `postcode_d` varchar(225) NULL,
  `country_d` varchar(225) NULL,
  `phone` varchar(225) NULL,
  `mobile` varchar(255) NULL,
  `subtotal` decimal(30,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(30,2) NOT NULL DEFAULT '0.00',
  `prod_total` decimal(30,2) NOT NULL DEFAULT '0.00',
  `total_tax` decimal(30,2) NOT NULL DEFAULT '0.00',
  `total_ship` decimal(30,2) NOT NULL DEFAULT '0.00',
  `status` int(16) NOT NULL DEFAULT '1',
  `sec_order_id` varchar(30) NULL,
  `ip` varchar(45) NOT NULL DEFAULT '0.0.0.0',
  `time` int(10) NOT NULL DEFAULT '0',
  `email` varchar(254) NOT NULL,
  `comments` text NULL,
  `ship_date` varchar(50) default NULL,
  `shipMethod` varchar(255) default NULL,
  `gateway` varchar(50) NOT NULL,
  `currency` varchar(5) NOT NULL,
  `customer_comments` text,
  `extra_notes` TEXT NOT NULL,
  `tax1_disp` varchar(128) NULL,
  `tax1_amt` decimal(30,2) NOT NULL default '0.00',
  `tax2_disp` varchar(128) NULL,
  `tax2_amt` decimal(30,2) NOT NULL default '0.00',
  `tax3_disp` varchar(128) NULL,
  `tax3_amt` decimal(30,2) NOT NULL default '0.00',
  `offline_capture` blob,
  `courier_tracking` text,
  `companyName` varchar(150) NULL,
  `companyName_d` varchar(150) NULL,
  `basket` text default NULL,
  `lang` varchar(2) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`cart_order_id`)
) TYPE=MyISAM ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_search` (
  `searchstr` varchar(255) NOT NULL default '',
  `hits` bigint(64) NOT NULL default '0',
  `id` bigint(64) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_sessions` (
  `sessId` varchar(32) NOT NULL default '',
  `basket` text,
  `timeStart` int(11) NOT NULL default '0',
  `timeLast` int(11) NOT NULL default '0',
  `customer_id` int(11) NOT NULL default '0',
  `location` varchar(255) default NULL,
  `lang` varchar(20) default NULL,
  `currency` char(3) default NULL,
  `skin` varchar(25) default NULL,
  `ip`  varchar(45) default NULL,
  `browser` text NOT NULL,
  PRIMARY KEY  (`sessId`)
) TYPE=MyISAM ; #EOQ

CREATE TABLE IF NOT EXISTS `CubeCart_taxes` (
  `id` int(11) NOT NULL auto_increment,
  `taxName` varchar(50) NOT NULL default '',
  `percent` decimal(7,4) NOT NULL default '0.0000',
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ; #EOQ


INSERT INTO `CubeCart_Modules` VALUES (1, 'shipping', 'Free_Shipping', '1', '1') ; #EOQ
INSERT INTO `CubeCart_Modules` VALUES (2, 'gateway', 'Print_Order_Form', '1', '1') ; #EOQ
INSERT INTO `CubeCart_config` (`name`, `array`) VALUES('Free_Shipping', 'a:1:{s:6:\"status\";s:1:\"1\";}') ; #EOQ
INSERT INTO `CubeCart_config` (`name`, `array`) VALUES  ('Print_Order_Form', 'a:16:{s:6:\"status\";s:1:\"1\";s:7:\"default\";s:1:\"1\";s:13:\"multiCurrency\";s:1:\"1\";s:4:\"desc\";s:17:\"Postal Order Form\";s:6:\"cheque\";s:1:\"1\";s:9:\"payableTo\";s:16:\"Company Name Ltd\";s:4:\"card\";s:1:\"1\";s:5:\"cards\";s:35:\"Mastercard,Visa,Switch,Maestro,AMEX\";s:4:\"bank\";s:1:\"1\";s:8:\"bankName\";s:13:\"Your Bank Plc\";s:7:\"accName\";s:12:\"Company Name\";s:8:\"sortCode\";s:8:\"xx-xx-xx\";s:4:\"acNo\";s:8:\"xxxxxxxx\";s:9:\"swiftCode\";s:8:\"xxxxxxxx\";s:7:\"address\";s:70:\"Address Line 1\r\nAddress Line 2\r\nAddress Line 3\r\nAddress Line 4\r\nxxxxxx\";s:5:\"notes\";s:44:\"We can only accept payments in xxx currency.\";}') ; #EOQ

INSERT INTO `CubeCart_admin_sections` (`sectId`, `name`, `description`) VALUES 
(1, 'users', 'For the administration of admin users and their permissions.'),
(2, 'products', 'For the administration of products.'),
(3, 'categories', 'For the administration of categories.'),
(4, 'documents', 'For the administration of site documents.'),
(5, 'customers', 'For the administration of customers details.'),
(6, 'shipping', 'For the administration of shipping methods.'),
(7, 'filemanager', 'For the administration of the website images.'),
(8, 'statistics', 'This section displays store statistics.'),
(9, 'settings', 'For the administration of the code store settings.'),
(10, 'orders', 'Access rights for the orders section.'),
(11, 'offers', 'Special offers &amp; promotions.'),
(12, 'reviews', 'Customer reviews &amp; comments.'), 
(13, 'gateways', 'For the administration of payment gateways.') ; #EOQ

INSERT INTO `CubeCart_category` (`cat_name`, `cat_desc`, `noProducts`, `priority`) VALUES ('Test Category', 'This is a test category setup during install. It can be edited or deleted from the store admin control panel.', 1, 1) ; #EOQ
	
INSERT INTO `CubeCart_cats_idx` (`cat_id`, `productId`) VALUES (1, 1) ; #EOQ
	
INSERT INTO `CubeCart_docs` (`doc_name`, `doc_content`, `doc_order`, `doc_terms`) VALUES ('About Us', 'This can be managed under <span class=\"navTitle\">Documents - Site Documents in the admin control panel.<br/>\r\n</span>', 1, 0) ; #EOQ
INSERT INTO `CubeCart_docs` (`doc_name`, `doc_content`, `doc_order`, `doc_terms`) VALUES ('Contact Us', 'This can be managed under <span class=\"navTitle\">Documents - Site Documents in the admin control panel.</span><span class=\"navTitle\"/>', 2, 0) ; #EOQ
INSERT INTO `CubeCart_docs` (`doc_name`, `doc_content`, `doc_order`, `doc_terms`) VALUES ('Terms & Conditions', 'This can be managed under <span class=\"navTitle\">Documents - Site Documents in the admin control panel.</span>', 3, 1) ; #EOQ
INSERT INTO `CubeCart_docs` (`doc_name`, `doc_content`, `doc_order`, `doc_terms`) VALUES ('Privacy Policy', 'This can be managed under <span class=\"navTitle\">Documents - Site Documents in the admin control panel.</span>', 4, 0) ; #EOQ
	
INSERT INTO `CubeCart_inventory` (`productCode`, `quantity`, `description`, `price`, `name`, `cat_id`, `sale_price`, `useStockLevel`, `prodWeight`, `taxType`, `date_added`) VALUES ('TESA31', 1, 'This is the main copy for the product.', 10.00, 'Test Product', '1', '6.99', '0', '4.00', '1', CURRENT_TIMESTAMP); #EOQ	

INSERT INTO `CubeCart_iso_counties` VALUES (1, 226, 'AL', 'Alabama') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (2, 226, 'AK', 'Alaska') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (3, 226, 'AS', 'American Samoa') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (4, 226, 'AZ', 'Arizona') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (5, 226, 'AR', 'Arkansas') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (6, 226, 'AF', 'Armed Forces Africa') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (7, 226, 'AA', 'Armed Forces Americas') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (8, 226, 'AC', 'Armed Forces Canada') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (9, 226, 'AE', 'Armed Forces Europe') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (10, 226, 'AM', 'Armed Forces Middle East') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (11, 226, 'AP', 'Armed Forces Pacific') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (12, 226, 'CA', 'California') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (13, 226, 'CO', 'Colorado') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (14, 226, 'CT', 'Connecticut') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (15, 226, 'DE', 'Delaware') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (16, 226, 'DC', 'District of Columbia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (17, 226, 'FM', 'Federated States Of Micronesia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (18, 226, 'FL', 'Florida') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (19, 226, 'GA', 'Georgia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (20, 226, 'GU', 'Guam') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (21, 226, 'HI', 'Hawaii') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (22, 226, 'ID', 'Idaho') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (23, 226, 'IL', 'Illinois') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (24, 226, 'IN', 'Indiana') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (25, 226, 'IA', 'Iowa') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (26, 226, 'KS', 'Kansas') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (27, 226, 'KY', 'Kentucky') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (28, 226, 'LA', 'Louisiana') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (29, 226, 'ME', 'Maine') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (30, 226, 'MH', 'Marshall Islands') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (31, 226, 'MD', 'Maryland') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (32, 226, 'MA', 'Massachusetts') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (33, 226, 'MI', 'Michigan') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (34, 226, 'MN', 'Minnesota') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (35, 226, 'MS', 'Mississippi') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (36, 226, 'MO', 'Missouri') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (37, 226, 'MT', 'Montana') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (38, 226, 'NE', 'Nebraska') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (39, 226, 'NV', 'Nevada') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (40, 226, 'NH', 'New Hampshire') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (41, 226, 'NJ', 'New Jersey') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (42, 226, 'NM', 'New Mexico') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (43, 226, 'NY', 'New York') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (44, 226, 'NC', 'North Carolina') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (45, 226, 'ND', 'North Dakota') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (46, 226, 'MP', 'Northern Mariana Islands') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (47, 226, 'OH', 'Ohio') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (48, 226, 'OK', 'Oklahoma') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (49, 226, 'OR', 'Oregon') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (50, 226, 'PW', 'Palau') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (51, 226, 'PA', 'Pennsylvania') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (52, 226, 'PR', 'Puerto Rico') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (53, 226, 'RI', 'Rhode Island') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (54, 226, 'SC', 'South Carolina') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (55, 226, 'SD', 'South Dakota') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (56, 226, 'TN', 'Tennessee') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (57, 226, 'TX', 'Texas') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (58, 226, 'UT', 'Utah') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (59, 226, 'VT', 'Vermont') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (60, 226, 'VI', 'Virgin Islands') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (61, 226, 'VA', 'Virginia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (62, 226, 'WA', 'Washington') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (63, 226, 'WV', 'West Virginia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (64, 226, 'WI', 'Wisconsin') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (65, 226, 'WY', 'Wyoming') ; #EOQ

INSERT INTO `CubeCart_iso_counties` VALUES (66, 38, 'AB', 'Alberta') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (67, 38, 'BC', 'British Columbia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (68, 38, 'MB', 'Manitoba') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (69, 38, 'NL', 'Newfoundland') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (70, 38, 'NB', 'New Brunswick') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (71, 38, 'NS', 'Nova Scotia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (72, 38, 'NT', 'Northwest Territories') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (73, 38, 'NU', 'Nunavut') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (74, 38, 'ON', 'Ontario') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (75, 38, 'PE', 'Prince Edward Island') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (76, 38, 'QC', 'Quebec') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (77, 38, 'SK', 'Saskatchewan') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (78, 38, 'YT', 'Yukon Territory') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (79, 80, 'NDS', 'Niedersachsen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (80, 80, 'BAW', 'Baden-Württemberg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (81, 80, 'BAY', 'Bayern') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (82, 80, 'BER', 'Berlin') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (83, 80, 'BRG', 'Brandenburg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (84, 80, 'BRE', 'Bremen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (85, 80, 'HAM', 'Hamburg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (86, 80, 'HES', 'Hessen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (87, 80, 'MEC', 'Mecklenburg-Vorpommern') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (88, 80, 'NRW', 'Nordrhein-Westfalen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (89, 80, 'RHE', 'Rheinland-Pfalz') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (90, 80, 'SAR', 'Saarland') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (91, 80, 'SAS', 'Sachsen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (92, 80, 'SAC', 'Sachsen-Anhalt') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (93, 80, 'SCN', 'Schleswig-Holstein') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (94, 80, 'THE', 'Thüringen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (95, 14, 'WIE', 'Wien') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (96, 14, 'NO', 'NiederÖsterreich') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (97, 14, 'OO', 'OberÖsterreich') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (98, 14, 'SB', 'Salzburg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (99, 14, 'KN', 'Kärnten') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (100, 14, 'ST', 'Steiermark') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (101, 14, 'TI', 'Tirol') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (102, 14, 'BL', 'Burgenland') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (103, 14, 'VB', 'Voralberg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (104, 206, 'AG', 'Aargau') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (105, 206, 'AI', 'Appenzell Innerrhoden') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (106, 206, 'APP', 'Appenzell Ausserrhoden') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (107, 206, 'BE', 'Bern') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (108, 206, 'BLA', 'Basel-Landschaft') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (109, 206, 'BS', 'Basel-Stadt') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (110, 206, 'FR', 'Freiburg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (111, 206, 'GE', 'Genf') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (112, 206, 'GL', 'Glarus') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (113, 206, 'JUB', 'Graubünden') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (114, 206, 'JU', 'Jura') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (115, 206, 'LU', 'Luzern') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (116, 206, 'NEU', 'Neuenburg') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (117, 206, 'NW', 'Nidwalden') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (118, 206, 'OW', 'Obwalden') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (119, 206, 'SG', 'St. Gallen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (120, 206, 'SH', 'Schaffhausen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (121, 206, 'SO', 'Solothurn') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (122, 206, 'SZ', 'Schwyz') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (123, 206, 'TG', 'Thurgau') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (124, 206, 'TE', 'Tessin') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (125, 206, 'UR', 'Uri') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (126, 206, 'VD', 'Waadt') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (127, 206, 'VS', 'Wallis') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (128, 206, 'ZG', 'Zug') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (129, 206, 'ZH', 'Zürich') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (130, 199, 'ACOR', 'A Coruña') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (131, 199, 'ALAV', 'Alava') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (132, 199, 'ALBA', 'Albacete') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (133, 199, 'ALIC', 'Alicante') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (134, 199, 'ALME', 'Almeria') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (135, 199, 'ASTU', 'Asturias') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (136, 199, 'AVIL', 'Avila') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (137, 199, 'BADA', 'Badajoz') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (138, 199, 'BALE', 'Baleares') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (139, 199, 'BARC', 'Barcelona') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (140, 199, 'BURG', 'Burgos') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (141, 199, 'CACE', 'Caceres') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (142, 199, 'CADI', 'Cadiz') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (143, 199, 'CANT', 'Cantabria') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (144, 199, 'CAST', 'Castellon') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (145, 199, 'CEUT', 'Ceuta') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (146, 199, 'CIUD', 'Ciudad Real') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (147, 199, 'CORD', 'Cordoba') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (148, 199, 'CUEN', 'Cuenca') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (149, 199, 'GIRO', 'Girona') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (150, 199, 'GRAN', 'Granada') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (151, 199, 'GUAD', 'Guadalajara') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (152, 199, 'GUIP', 'Guipuzcoa') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (153, 199, 'HUEL', 'Huelva') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (154, 199, 'HUES', 'Huesca') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (155, 199, 'JAEN', 'Jaen') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (156, 199, 'LAR', 'La Rioja') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (157, 199, 'LAS', 'Las Palmas') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (158, 199, 'LEON', 'Leon') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (159, 199, 'LLEI', 'Lleida') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (160, 199, 'LUGO', 'Lugo') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (161, 199, 'MADR', 'Madrid') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (162, 199, 'MALA', 'Malaga') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (163, 199, 'MELI', 'Melilla') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (164, 199, 'MURC', 'Murcia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (165, 199, 'NAVA', 'Navarra') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (166, 199, 'OURE', 'Ourense') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (167, 199, 'PALE', 'Palencia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (168, 199, 'PONT', 'Pontevedra') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (169, 199, 'SALA', 'Salamanca') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (170, 199, 'SANT', 'Santa Cruz de Tenerife') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (171, 199, 'SEGO', 'Segovia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (172, 199, 'SEVI', 'Sevilla') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (173, 199, 'SORI', 'Soria') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (174, 199, 'TARR', 'Tarragona') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (175, 199, 'TERU', 'Teruel') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (176, 199, 'TOLE', 'Toledo') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (177, 199, 'VALE', 'Valencia') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (178, 199, 'VALL', 'Valladolid') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (179, 199, 'VIZC', 'Vizcaya') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (180, 199, 'ZAMO', 'Zamora') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (181, 199, 'ZARA', 'Zaragoza') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (415, 103, 'CW', 'Carlow') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (416, 103, 'CN', 'Cavan') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (417, 103, 'CE', 'Clare') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (418, 103, 'C', 'Cork') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (419, 103, 'DL', 'Donegal') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (420, 103, 'D', 'Dublin') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (421, 103, 'G', 'Galway') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (422, 103, 'KY', 'Kerry') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (423, 103, 'KE', 'Kildare') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (424, 103, 'KK', 'Kilkenny') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (425, 103, 'LS', 'Laoighis') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (426, 103, 'LM', 'Leitrim') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (427, 103, 'LK', 'Limerick') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (428, 103, 'LD', 'Longford') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (429, 103, 'LH', 'Louth') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (430, 103, 'MO', 'Mayo') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (431, 103, 'MH', 'Meath') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (432, 103, 'MN', 'Monaghan') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (433, 103, 'OY', 'Offaly') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (434, 103, 'RN', 'Roscommon') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (435, 103, 'SO', 'Sligo') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (436, 103, 'TA', 'Tipperary') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (437, 103, 'WD', 'Waterford') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (438, 103, 'WH', 'Westmeath') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (439, 103, 'WX', 'Wexford') ; #EOQ
INSERT INTO `CubeCart_iso_counties` VALUES (440, 103, 'WW', 'Wicklow') ; #EOQ

INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"AVN","Avon"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BDF","Bedfordshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BRK","Berkshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BKM","Buckinghamshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CAM","Cambridgeshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CHS","Cheshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CLV","Cleveland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CON","Cornwall"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CUL","Cumberland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CMA","Cumbria"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DBY","Derbyshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DEV","Devon"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DOR","Dorset"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DUR","County Durham"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ESX","East Sussex"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ESS","Essex"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GLS","Gloucestershire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"HAM","Hampshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"HWR","Hereford and Worcester"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"HEF","Herefordshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"HRT","Hertfordshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"HUM","Humberside"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"HUN","Huntingdonshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"IOW","Isle of Wight"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"KEN","Kent"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"LAN","Lancashire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"LEI","Leicestershire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"LIN","Lincolnshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GTM","Greater Manchester"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GTL","Greater London"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MSY","Merseyside"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MDX","Middlesex"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"NFK","Norfolk"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"NTH","Northamptonshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"NBL","Northumberland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"NTT","Nottinghamshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"OXF","Oxfordshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"RUT","Rutland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SAL","Shropshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SOM","Somerset"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"STS","Staffordshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SFK","Suffolk"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SRY","Surrey"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SSX","Sussex"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"TWR","Tyne and Wear"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WAR","Warwickshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WMD","West Midlands"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WES","Westmorland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WIL","Wiltshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WOR","Worcestershire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"YOK","Yorkshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ABD","Aberdeenshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ANS","Angus"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ARL","Argyll"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"AYR","Ayrshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BAN","Banffshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BEW","Berwickshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BUT","Bute"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CAI","Caithness"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CLK","Clackmannanshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CRO","Cromartyshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DFS","Dumfriesshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DNB","Dunbartonshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ELN","East Lothian"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"FIF","Fife"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"INV","Inverness-shire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"KRS","Kinross-shire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"KKD","Kirkcudbrightshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"LAN","Lanarkshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MLN","Midlothian"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MOR","Moray"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"NAI","Nairnshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"OKI","Orkney"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"PEE","Peeblesshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"PER","Perthshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"RFW","Renfrewshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ROC","Ross"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ROX","Roxburghshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SEL","Selkirkshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SHI","Shetland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"STI","Stirlingshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"SUT","Sutherland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WLN","West Lothian"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"WIG","Wigtownshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"AGY","Anglesey"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"BRN","Brecknockshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CAE","Caernarfonshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CAD","Cardiganshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CRR","Carmarthenshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"CLW","Clwyd"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DEN","Denbighshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DFD","Dyfed"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"FLN","Flintshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GLA","Glamorgan"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GNT","Gwent"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GWN","Gwynedd"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MER","Merionethshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MON","Monmouthshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"MGY","Montgomeryshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"PEM","Pembrokeshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"POW","Powys"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"RAD","Radnorshire"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ANT","Antrim"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"ARM","Armagh"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"LDY","Londonderry"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"DOW","Down"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"FER","Fermanagh"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"TYR","Tyrone"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"JEY","Guernsey"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,225,"GGY","Channel Islands"); #EOQ

INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"DR","Drenthe"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"FL","Flevoland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"FR","Friesland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"GLD","Gelderland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"GR","Groningen"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"LI","Limburg"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"NB","Noord-Brabant"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"NH","Noord-Holland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"OV","Overijssel"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"UT","Utrecht"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"ZL","Zeeland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,150,"ZH","Zuid-Holland"); #EOQ

INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"ACT","Australian Capital Territory"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"NSW","New South Wales"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"NT","Northern Territory"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"QLD","Queensland"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"SA","South Australia"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"TAS","Tasmania"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"VIC","Victoria"); #EOQ
INSERT INTO `CubeCart_iso_counties` (`id`,`countryId`,`abbrev`,`name`) VALUES (NULL,13,"WA","Western Australia"); #EOQ

INSERT INTO `CubeCart_iso_countries` VALUES (1, 'AF', 'Afghanistan', 'AFG', 4) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (2, 'AL', 'Albania', 'ALB', 8) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (3, 'DZ', 'Algeria', 'DZA', 12) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (4, 'AS', 'American Samoa', 'ASM', 16) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (5, 'AD', 'Andorra', 'AND', 20) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (6, 'AO', 'Angola', 'AGO', 24) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (7, 'AI', 'Anguilla', 'AIA', 660) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (8, 'AQ', 'Antarctica', 'ATA', 10) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (9, 'AG', 'Antigua and Barbuda', 'ATG', 28) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (10, 'AR', 'Argentina', 'ARG', 32) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (11, 'AM', 'Armenia', 'ARM', 51) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (12, 'AW', 'Aruba', 'ABW', 533) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (13, 'AU', 'Australia', 'AUS', 36) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (14, 'AT', 'Austria', 'AUT', 40) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (15, 'AZ', 'Azerbaijan', 'AZE', 31) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (16, 'BS', 'Bahamas', 'BHS', 44) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (17, 'BH', 'Bahrain', 'BHR', 48) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (18, 'BD', 'Bangladesh', 'BGD', 50) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (19, 'BB', 'Barbados', 'BRB', 52) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (20, 'BY', 'Belarus', 'BLR', 112) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (21, 'BE', 'Belgium', 'BEL', 56) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (22, 'BZ', 'Belize', 'BLZ', 84) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (23, 'BJ', 'Benin', 'BEN', 204) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (24, 'BM', 'Bermuda', 'BMU', 60) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (25, 'BT', 'Bhutan', 'BTN', 64) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (26, 'BO', 'Bolivia', 'BOL', 68) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (27, 'BA', 'Bosnia and Herzegovina', 'BIH', 70) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (28, 'BW', 'Botswana', 'BWA', 72) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (29, 'BV', 'Bouvet Island', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (30, 'BR', 'Brazil', 'BRA', 76) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (31, 'IO', 'British Indian Ocean Territory', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (32, 'BN', 'Brunei Darussalam', 'BRN', 96) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (33, 'BG', 'Bulgaria', 'BGR', 100) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (34, 'BF', 'Burkina Faso', 'BFA', 854) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (35, 'BI', 'Burundi', 'BDI', 108) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (36, 'KH', 'Cambodia', 'KHM', 116) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (37, 'CM', 'Cameroon', 'CMR', 120) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (38, 'CA', 'Canada', 'CAN', 124) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (39, 'CV', 'Cape Verde', 'CPV', 132) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (40, 'KY', 'Cayman Islands', 'CYM', 136) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (41, 'CF', 'Central African Republic', 'CAF', 140) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (42, 'TD', 'Chad', 'TCD', 148) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (43, 'CL', 'Chile', 'CHL', 152) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (44, 'CN', 'China', 'CHN', 156) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (45, 'CX', 'Christmas Island', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (46, 'CC', 'Cocos (Keeling) Islands', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (47, 'CO', 'Colombia', 'COL', 170) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (48, 'KM', 'Comoros', 'COM', 174) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (49, 'CG', 'Congo', 'COG', 178) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (50, 'CD', 'Congo, the Democratic Republic of the', 'COD', 180) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (51, 'CK', 'Cook Islands', 'COK', 184) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (52, 'CR', 'Costa Rica', 'CRI', 188) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (53, 'CI', 'Cote D''Ivoire', 'CIV', 384) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (54, 'HR', 'Croatia', 'HRV', 191) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (55, 'CU', 'Cuba', 'CUB', 192) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (56, 'CY', 'Cyprus', 'CYP', 196) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (57, 'CZ', 'Czech Republic', 'CZE', 203) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (58, 'DK', 'Denmark', 'DNK', 208) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (59, 'DJ', 'Djibouti', 'DJI', 262) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (60, 'DM', 'Dominica', 'DMA', 212) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (61, 'DO', 'Dominican Republic', 'DOM', 214) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (62, 'EC', 'Ecuador', 'ECU', 218) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (63, 'EG', 'Egypt', 'EGY', 818) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (64, 'SV', 'El Salvador', 'SLV', 222) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (65, 'GQ', 'Equatorial Guinea', 'GNQ', 226) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (66, 'ER', 'Eritrea', 'ERI', 232) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (67, 'EE', 'Estonia', 'EST', 233) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (68, 'ET', 'Ethiopia', 'ETH', 231) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (69, 'FK', 'Falkland Islands (Malvinas)', 'FLK', 238) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (70, 'FO', 'Faroe Islands', 'FRO', 234) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (71, 'FJ', 'Fiji', 'FJI', 242) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (72, 'FI', 'Finland', 'FIN', 246) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (73, 'FR', 'France', 'FRA', 250) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (74, 'GF', 'French Guiana', 'GUF', 254) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (75, 'PF', 'French Polynesia', 'PYF', 258) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (76, 'TF', 'French Southern Territories', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (77, 'GA', 'Gabon', 'GAB', 266) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (78, 'GM', 'Gambia', 'GMB', 270) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (79, 'GE', 'Georgia', 'GEO', 268) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (80, 'DE', 'Germany', 'DEU', 276) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (81, 'GH', 'Ghana', 'GHA', 288) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (82, 'GI', 'Gibraltar', 'GIB', 292) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (83, 'GR', 'Greece', 'GRC', 300) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (84, 'GL', 'Greenland', 'GRL', 304) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (85, 'GD', 'Grenada', 'GRD', 308) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (86, 'GP', 'Guadeloupe', 'GLP', 312) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (87, 'GU', 'Guam', 'GUM', 316) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (88, 'GT', 'Guatemala', 'GTM', 320) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (89, 'GN', 'Guinea', 'GIN', 324) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (90, 'GW', 'Guinea-Bissau', 'GNB', 624) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (91, 'GY', 'Guyana', 'GUY', 328) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (92, 'HT', 'Haiti', 'HTI', 332) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (93, 'HM', 'Heard Island and Mcdonald Islands', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (94, 'VA', 'Holy See (Vatican City State)', 'VAT', 336) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (95, 'HN', 'Honduras', 'HND', 340) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (96, 'HK', 'Hong Kong', 'HKG', 344) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (97, 'HU', 'Hungary', 'HUN', 348) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (98, 'IS', 'Iceland', 'ISL', 352) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (99, 'IN', 'India', 'IND', 356) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (100, 'ID', 'Indonesia', 'IDN', 360) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (101, 'IR', 'Iran, Islamic Republic of', 'IRN', 364) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (102, 'IQ', 'Iraq', 'IRQ', 368) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (103, 'IE', 'Ireland', 'IRL', 372) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (104, 'IL', 'Israel', 'ISR', 376) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (105, 'IT', 'Italy', 'ITA', 380) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (106, 'JM', 'Jamaica', 'JAM', 388) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (107, 'JP', 'Japan', 'JPN', 392) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (108, 'JO', 'Jordan', 'JOR', 400) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (109, 'KZ', 'Kazakhstan', 'KAZ', 398) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (110, 'KE', 'Kenya', 'KEN', 404) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (111, 'KI', 'Kiribati', 'KIR', 296) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (112, 'KP', 'Korea, Democratic People''s Republic of', 'PRK', 408) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (113, 'KR', 'Korea, Republic of', 'KOR', 410) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (114, 'KW', 'Kuwait', 'KWT', 414) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (115, 'KG', 'Kyrgyzstan', 'KGZ', 417) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (116, 'LA', 'Lao People''s Democratic Republic', 'LAO', 418) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (117, 'LV', 'Latvia', 'LVA', 428) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (118, 'LB', 'Lebanon', 'LBN', 422) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (119, 'LS', 'Lesotho', 'LSO', 426) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (120, 'LR', 'Liberia', 'LBR', 430) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (121, 'LY', 'Libyan Arab Jamahiriya', 'LBY', 434) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (122, 'LI', 'Liechtenstein', 'LIE', 438) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (123, 'LT', 'Lithuania', 'LTU', 440) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (124, 'LU', 'Luxembourg', 'LUX', 442) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (125, 'MO', 'Macao', 'MAC', 446) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (126, 'MK', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (127, 'MG', 'Madagascar', 'MDG', 450) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (128, 'MW', 'Malawi', 'MWI', 454) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (129, 'MY', 'Malaysia', 'MYS', 458) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (130, 'MV', 'Maldives', 'MDV', 462) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (131, 'ML', 'Mali', 'MLI', 466) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (132, 'MT', 'Malta', 'MLT', 470) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (133, 'MH', 'Marshall Islands', 'MHL', 584) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (134, 'MQ', 'Martinique', 'MTQ', 474) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (135, 'MR', 'Mauritania', 'MRT', 478) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (136, 'MU', 'Mauritius', 'MUS', 480) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (137, 'YT', 'Mayotte', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (138, 'MX', 'Mexico', 'MEX', 484) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (139, 'FM', 'Micronesia, Federated States of', 'FSM', 583) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (140, 'MD', 'Moldova, Republic of', 'MDA', 498) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (141, 'MC', 'Monaco', 'MCO', 492) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (142, 'MN', 'Mongolia', 'MNG', 496) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (143, 'MS', 'Montserrat', 'MSR', 500) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (144, 'MA', 'Morocco', 'MAR', 504) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (145, 'MZ', 'Mozambique', 'MOZ', 508) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (146, 'MM', 'Myanmar', 'MMR', 104) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (147, 'NA', 'Namibia', 'NAM', 516) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (148, 'NR', 'Nauru', 'NRU', 520) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (149, 'NP', 'Nepal', 'NPL', 524) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (150, 'NL', 'Netherlands', 'NLD', 528) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (151, 'AN', 'Netherlands Antilles', 'ANT', 530) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (152, 'NC', 'New Caledonia', 'NCL', 540) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (153, 'NZ', 'New Zealand', 'NZL', 554) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (154, 'NI', 'Nicaragua', 'NIC', 558) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (155, 'NE', 'Niger', 'NER', 562) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (156, 'NG', 'Nigeria', 'NGA', 566) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (157, 'NU', 'Niue', 'NIU', 570) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (158, 'NF', 'Norfolk Island', 'NFK', 574) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (159, 'MP', 'Northern Mariana Islands', 'MNP', 580) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (160, 'NO', 'Norway', 'NOR', 578) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (161, 'OM', 'Oman', 'OMN', 512) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (162, 'PK', 'Pakistan', 'PAK', 586) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (163, 'PW', 'Palau', 'PLW', 585) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (164, 'PS', 'Palestinian Territory, Occupied', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (165, 'PA', 'Panama', 'PAN', 591) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (166, 'PG', 'Papua New Guinea', 'PNG', 598) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (167, 'PY', 'Paraguay', 'PRY', 600) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (168, 'PE', 'Peru', 'PER', 604) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (169, 'PH', 'Philippines', 'PHL', 608) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (170, 'PN', 'Pitcairn', 'PCN', 612) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (171, 'PL', 'Poland', 'POL', 616) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (172, 'PT', 'Portugal', 'PRT', 620) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (173, 'PR', 'Puerto Rico', 'PRI', 630) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (174, 'QA', 'Qatar', 'QAT', 634) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (175, 'RE', 'Reunion', 'REU', 638) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (176, 'RO', 'Romania', 'ROM', 642) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (177, 'RU', 'Russian Federation', 'RUS', 643) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (178, 'RW', 'Rwanda', 'RWA', 646) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (179, 'SH', 'Saint Helena', 'SHN', 654) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (180, 'KN', 'Saint Kitts and Nevis', 'KNA', 659) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (181, 'LC', 'Saint Lucia', 'LCA', 662) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (182, 'PM', 'Saint Pierre and Miquelon', 'SPM', 666) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (183, 'VC', 'Saint Vincent and the Grenadines', 'VCT', 670) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (184, 'WS', 'Samoa', 'WSM', 882) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (185, 'SM', 'San Marino', 'SMR', 674) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (186, 'ST', 'Sao Tome and Principe', 'STP', 678) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (187, 'SA', 'Saudi Arabia', 'SAU', 682) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (188, 'SN', 'Senegal', 'SEN', 686) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (189, 'RS', 'Serbia', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (190, 'SC', 'Seychelles', 'SYC', 690) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (191, 'SL', 'Sierra Leone', 'SLE', 694) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (192, 'SG', 'Singapore', 'SGP', 702) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (193, 'SK', 'Slovakia', 'SVK', 703) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (194, 'SI', 'Slovenia', 'SVN', 705) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (195, 'SB', 'Solomon Islands', 'SLB', 90) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (196, 'SO', 'Somalia', 'SOM', 706) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (197, 'ZA', 'South Africa', 'ZAF', 710) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (198, 'GS', 'South Georgia and the South Sandwich Islands', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (199, 'ES', 'Spain', 'ESP', 724) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (200, 'LK', 'Sri Lanka', 'LKA', 144) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (201, 'SD', 'Sudan', 'SDN', 736) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (202, 'SR', 'Suriname', 'SUR', 740) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (203, 'SJ', 'Svalbard and Jan Mayen', 'SJM', 744) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (204, 'SZ', 'Swaziland', 'SWZ', 748) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (205, 'SE', 'Sweden', 'SWE', 752) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (206, 'CH', 'Switzerland', 'CHE', 756) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (207, 'SY', 'Syrian Arab Republic', 'SYR', 760) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (208, 'TW', 'Taiwan', 'TWN', 158) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (209, 'TJ', 'Tajikistan', 'TJK', 762) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (210, 'TZ', 'Tanzania, United Republic of', 'TZA', 834) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (211, 'TH', 'Thailand', 'THA', 764) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (212, 'TL', 'Timor-Leste', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (213, 'TG', 'Togo', 'TGO', 768) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (214, 'TK', 'Tokelau', 'TKL', 772) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (215, 'TO', 'Tonga', 'TON', 776) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (216, 'TT', 'Trinidad and Tobago', 'TTO', 780) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (217, 'TN', 'Tunisia', 'TUN', 788) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (218, 'TR', 'Turkey', 'TUR', 792) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (219, 'TM', 'Turkmenistan', 'TKM', 795) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (220, 'TC', 'Turks and Caicos Islands', 'TCA', 796) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (221, 'TV', 'Tuvalu', 'TUV', 798) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (222, 'UG', 'Uganda', 'UGA', 800) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (223, 'UA', 'Ukraine', 'UKR', 804) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (224, 'AE', 'United Arab Emirates', 'ARE', 784) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (225, 'GB', 'United Kingdom', 'GBR', 826) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (226, 'US', 'United States', 'USA', 840) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (227, 'UM', 'United States Minor Outlying Islands', NULL, NULL) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (228, 'UY', 'Uruguay', 'URY', 858) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (229, 'UZ', 'Uzbekistan', 'UZB', 860) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (230, 'VU', 'Vanuatu', 'VUT', 548) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (231, 'VE', 'Venezuela', 'VEN', 862) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (232, 'VN', 'Viet Nam', 'VNM', 704) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (233, 'VG', 'Virgin Islands, British', 'VGB', 92) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (234, 'VI', 'Virgin Islands, U.s.', 'VIR', 850) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (235, 'WF', 'Wallis and Futuna', 'WLF', 876) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (236, 'EH', 'Western Sahara', 'ESH', 732) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (237, 'YE', 'Yemen', 'YEM', 887) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (238, 'ZM', 'Zambia', 'ZMB', 894) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (239, 'ZW', 'Zimbabwe', 'ZWE', 716) ; #EOQ
INSERT INTO `CubeCart_iso_countries` VALUES (240, 'ME', 'Montenegro', NULL, NULL) ; #EOQ