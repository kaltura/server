CREATE TABLE `drm_policy`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`profile_id` INTEGER  NOT NULL,
	`name` TEXT  NOT NULL,
	`system_name` VARCHAR(128) default '' NOT NULL,
	`description` TEXT,
	`provider` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`scenario` INTEGER  NOT NULL,
	`license_type` INTEGER,
	`license_expiration_policy` INTEGER,
	`duration` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY partner_id_provider_status (partner_id, provider, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;