CREATE TABLE IF NOT EXISTS `vendor_catalog_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256)  NOT NULL,
	`system_name` VARCHAR(256)  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`status` TINYINT  NOT NULL,
	`vendor_partner_id` INTEGER  NOT NULL,
	`service_type` TINYINT  NOT NULL,
	`service_feature` TINYINT  NOT NULL,
	`turn_around_time` INTEGER  NOT NULL,
	`source_language` VARCHAR(256),
	`target_language` VARCHAR(256),
	`output_format` VARCHAR(256),
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `status_service_type_index`(`status`, `service_type`),
	KEY `status_service_type_service_feature_index`(`status`, `service_type`, `service_feature`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
