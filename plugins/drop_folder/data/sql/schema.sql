
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- drop_folder
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `drop_folder`;


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
	`error_code` INTEGER,
	`error_description` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `status_index`(`status`),
	KEY `dc_index`(`dc`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- drop_folder_file
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `drop_folder_file`;


CREATE TABLE `drop_folder_file`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`drop_folder_id` INTEGER  NOT NULL,
	`file_name` VARCHAR(500)  NOT NULL,
	`type` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`file_size` INTEGER  NOT NULL,
	`file_size_last_set_at` DATETIME,
	`error_code` INTEGER,
	`error_description` TEXT,
	`parsed_slug` VARCHAR(500),
	`parsed_flavor` VARCHAR(500),
	`lead_drop_folder_file_id` INTEGER,
	`deleted_drop_folder_file_id` INTEGER,
	`md5_file_name` VARCHAR(32)  NOT NULL,
	`entry_id` VARCHAR(20),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`upload_start_detected_at` DATETIME,
	`upload_end_detected_at` DATETIME,
	`import_started_at` DATETIME,
	`import_ended_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `file_name_in_drop_folder_unique` (`md5_file_name`, `drop_folder_id`, `deleted_drop_folder_file_id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `status_index`(`status`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
