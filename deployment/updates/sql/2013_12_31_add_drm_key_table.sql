CREATE TABLE `drm_key`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`provider` INTEGER  NOT NULL,
	`object_id` VARCHAR(20)  NOT NULL,
	`object_type` TINYINT  NOT NULL,
	`key` VARCHAR(128)  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `partner_id_object_id_object_type_provider` (`partner_id`, `object_id`, `object_type`, `provider`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;