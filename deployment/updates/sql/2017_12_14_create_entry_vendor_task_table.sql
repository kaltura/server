CREATE TABLE IF NOT EXISTS `entry_vendor_task`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`queue_time` DATETIME  NOT NULL,
	`finish_time` DATETIME  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`vendor_partner_id` INTEGER  NOT NULL,
	`entry_id` VARCHAR(31)  NOT NULL,
	`status` TINYINT  NOT NULL,
	`price` INTEGER  NOT NULL,
	`catalog_item_id` INTEGER  NOT NULL,
	`vendor_profile_id` INTEGER  NOT NULL,
	`kuser_id` INTEGER  NOT NULL,
	`version` INTEGER  NOT NULL,
	`context` VARCHAR(31)  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `vendor_partner_id_status_index`(`vendor_partner_id`, `status`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;