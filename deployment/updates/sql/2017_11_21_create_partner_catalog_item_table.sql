CREATE TABLE IF NOT EXISTS `partner_catalog_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`status` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`catalog_item_id` INTEGER  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
