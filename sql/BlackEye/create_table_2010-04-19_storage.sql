
CREATE TABLE `storage_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`desciption` VARCHAR(127),
	`status` TINYINT,
	`protocol` TINYINT,
	`storage_url` VARCHAR(127),
	`storage_base_dir` VARCHAR(127),
	`storage_username` VARCHAR(31),
	`storage_password` VARCHAR(31),
	`storage_ftp_passive_mode` TINYINT,
	`delivery_http_base_url` VARCHAR(127),
	`delivery_rmp_base_url` VARCHAR(127),
	`delivery_iis_base_url` VARCHAR(127),
	`min_file_size` INTEGER,
	`max_file_size` INTEGER,
	`flavor_params_ids` VARCHAR(127),
	`max_concurrent_connections` INTEGER,
	`custom_data` TEXT,
	`path_manager_class` VARCHAR(127),
	PRIMARY KEY (`id`)
)Type=MyISAM;
