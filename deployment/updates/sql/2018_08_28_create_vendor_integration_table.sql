CREATE TABLE  IF NOT EXISTS `vendor_integration` (
	`id` int  NOT NULL AUTO_INCREMENT,
	`account_id` varchar(64) NOT NULL,
	`partner_id` int NOT NULL,
	`vendor_Type` smallint(6) NOT NULL,
	`custom_data` text,
	`status` TINYINT NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_vendor_Type_status_index`(`partner_id`,`vendor_Type`,`status`),
	KEY `account_id_vendor_Type_index`(`account_id`,`vendor_Type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


