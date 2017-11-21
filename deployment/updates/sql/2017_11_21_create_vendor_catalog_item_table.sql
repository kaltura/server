CREATE TABLE IF NOT EXISTS `vendor_catalog_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256)  NOT NULL,
	`system_name` VARCHAR(256),
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`is_default` TINYINT default 0,
	`status` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`vendor_partner_id` INTEGER  NOT NULL,
	`service_type` TINYINT  NOT NULL,
	`service_feature` TINYINT  NOT NULL,
	`turn_around_time` INTEGER  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `status_service_type_index`(`status`, `service_type`),
	KEY `status_service_type_service_feature_index`(`status`, `service_type`, `service_feature`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
