CREATE TABLE IF NOT EXISTS `partner_catalog_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`status` TINYINT  NOT NULL,
	`vendor_catalog_item_id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;