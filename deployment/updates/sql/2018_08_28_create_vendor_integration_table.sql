CREATE TABLE  IF NOT EXISTS `vendor_integration` (
	`id` int  NOT NULL AUTO_INCREMENT,
	`account_id` varchar(64) NOT NULL,
	`partner_id` int NOT NULL,
	`vendor_Type` smallint(6) NOT NULL,
	`custom_data` text, PRIMARY KEY (`id`),
	KEY `account_id_partner_id_vendor_Type_index`(`account_id`,`partner_id`,`vendor_Type`),
	KEY `partner_id_vendor_Type_index`(`partner_id`,`vendor_Type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


