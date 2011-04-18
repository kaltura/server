
#-----------------------------------------------------------------------------
#-- drop_folder
#-----------------------------------------------------------------------------


CREATE TABLE `drop_folder`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(100)  NOT NULL,
	`description` TEXT,
	`type` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`dc` INTEGER  NOT NULL,
	`path` TEXT  NOT NULL,
	`conversion_profile_id` INTEGER,
	`file_delete_policy` INTEGER,
	`file_handler_type` INTEGER,
	`file_name_patterns` TEXT  NOT NULL,
	`file_handler_config` TEXT  NOT NULL,
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `status_index`(`status`),
	KEY `dc_index`(`dc`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- drop_folder_file
#-----------------------------------------------------------------------------


CREATE TABLE `drop_folder_file`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`drop_folder_id` INTEGER  NOT NULL,
	`file_name` VARCHAR(500)  NOT NULL,
	`status` INTEGER  NOT NULL,
	`file_size` INTEGER  NOT NULL,
	`file_size_last_set_at` DATETIME,
	`error_description` TEXT,
	`parsed_slug` VARCHAR(500),
	`parsed_flavor` VARCHAR(500),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `status_index`(`status`)
)Type=MyISAM;
