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
	KEY partner_id_provider_status (partner_id, provider, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
