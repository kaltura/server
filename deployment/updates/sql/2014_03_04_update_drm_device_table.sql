DROP TABLE IF EXISTS `drm_device`;

CREATE TABLE `drm_device`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`device_id` VARCHAR(128)  NOT NULL,
	`provider` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `device_id_partner_id_unique` (`device_id`, `partner_id`),
	KEY `partner_id_provider_status`(`partner_id`, `provider`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `drm_policy` DROP `profile_id`;