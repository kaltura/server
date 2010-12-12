
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- distribution_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `distribution_profile`;


CREATE TABLE `distribution_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`provider_type` INTEGER,
	`name` VARCHAR(31),
	`status` TINYINT,
	`submit_enabled` TINYINT,
	`update_enabled` TINYINT,
	`delete_enabled` TINYINT,
	`report_enabled` TINYINT,
	`auto_create_flavors` VARCHAR(255),
	`auto_create_thumb` VARCHAR(255),
	`optional_flavor_params_ids` VARCHAR(127),
	`required_flavor_params_ids` VARCHAR(127),
	`optional_thumb_dimensions` VARCHAR(255),
	`required_thumb_dimensions` VARCHAR(255),
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`),
	KEY `partner_status`(`partner_id`, `status`),
	KEY `partner_status_provider`(`partner_id`, `status`, `provider_type`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- entry_distribution
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `entry_distribution`;


CREATE TABLE `entry_distribution`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`submitted_at` DATETIME,
	`entry_id` VARCHAR(20),
	`partner_id` INTEGER,
	`distribution_profile_id` INTEGER,
	`status` TINYINT,
	`dirty_status` TINYINT,
	`thumb_asset_ids` VARCHAR(255),
	`flavor_asset_ids` VARCHAR(255),
	`sunrise` DATETIME,
	`sunset` DATETIME,
	`remote_id` VARCHAR(31),
	`plays` INTEGER,
	`views` INTEGER,
	`validation_errors` VARCHAR(1023),
	`error_type` INTEGER,
	`error_number` INTEGER,
	`error_description` VARCHAR(255),
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_entry_profile`(`partner_id`, `entry_id`, `distribution_profile_id`),
	KEY `partner_profile_status`(`partner_id`, `distribution_profile_id`, `status`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- generic_distribution_provider
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `generic_distribution_provider`;


CREATE TABLE `generic_distribution_provider`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`is_default` TINYINT,
	`status` TINYINT,
	`name` VARCHAR(127),
	`optional_flavor_params_ids` VARCHAR(127),
	`required_flavor_params_ids` VARCHAR(127),
	`optional_thumb_dimensions` VARCHAR(255),
	`required_thumb_dimensions` VARCHAR(255),
	`editable_fields` VARCHAR(255),
	`mandatory_fields` VARCHAR(255),
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_and_defaults`(`partner_id`, `is_default`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- generic_distribution_provider_action
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `generic_distribution_provider_action`;


CREATE TABLE `generic_distribution_provider_action`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`generic_distribution_provider_id` INTEGER,
	`action` TINYINT,
	`status` TINYINT,
	`results_parser` TINYINT,
	`protocol` INTEGER,
	`server_address` VARCHAR(255),
	`remote_path` VARCHAR(255),
	`remote_username` VARCHAR(127),
	`remote_password` VARCHAR(127),
	`editable_fields` VARCHAR(255),
	`mandatory_fields` VARCHAR(255),
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `generic_distribution_provider_id`(`generic_distribution_provider_id`),
	KEY `generic_distribution_provider_status`(`generic_distribution_provider_id`, `status`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
