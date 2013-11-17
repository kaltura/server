CREATE TABLE IF NOT EXISTS `drm_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` TEXT  NOT NULL,
	`description` TEXT,
	`provider` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`license_server_url` TEXT,
	`default_policy` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `drm_profile_unique` (`partner_id`, `provider`, `status`),
	KEY `partner_id_index`(`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
