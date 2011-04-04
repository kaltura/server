
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
	`status` INTEGER  NOT NULL,
	`conversion_profile_id` INTEGER,
	`path` TEXT  NOT NULL,
	`dc` INTEGER  NOT NULL,
	`type` INTEGER  NOT NULL,
	`slug_field` VARCHAR(500),
	`slug_regex` VARCHAR(100),
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

DROP TABLE IF EXISTS `drop_folder_file`;


CREATE TABLE `drop_folder_file`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`drop_folder_id` INTEGER  NOT NULL,
	`file_name` VARCHAR(500)  NOT NULL,
	`status` INTEGER  NOT NULL,
	`file_size` INTEGER  NOT NULL,
	`last_file_size_check_at` DATETIME,
	`error_description` TEXT,
	`parsed_slug` VARCHAR(500),
	`parsed_flavor_id` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `status_index`(`status`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
