-- Cassiopea schema updates
ALTER TABLE  `flavor_params` ADD  `operators` TEXT NULL AFTER  `rotate` ,
ADD  `engine_version` SMALLINT NULL AFTER  `operators`;

ALTER TABLE  `flavor_params_output` ADD  `operators` TEXT NULL AFTER  `rotate` ,
ADD  `engine_version` SMALLINT NULL AFTER  `operators`;

ALTER TABLE  `batch_job` CHANGE  `data`  `data` VARCHAR( 8192 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `bulk_upload_result` ADD  `plugins_data` VARCHAR( 9182 ) NOT NULL AFTER  `error_description`;

CREATE TABLE `sphinx_log`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`entry_id` VARCHAR(20),
	`partner_id` INTEGER default 0,
	`dc` INTEGER,
	`sql` LONGTEXT,
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `entry_id`(`entry_id`),
	KEY `creatd_at`(`created_at`),
	INDEX `sphinx_log_FI_1` (`partner_id`),
	CONSTRAINT `sphinx_log_FK_1`
		FOREIGN KEY (`partner_id`)
		REFERENCES `partner` (`id`),
	CONSTRAINT `sphinx_log_FK_2`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`)
)Type=MyISAM;

CREATE TABLE `sphinx_log_server`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`server` VARCHAR(63),
	`dc` INTEGER,
	`last_log_id` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `sphinx_log_server_FI_1` (`last_log_id`),
	CONSTRAINT `sphinx_log_server_FK_1`
		FOREIGN KEY (`last_log_id`)
		REFERENCES `sphinx_log` (`id`)
)Type=MyISAM;

-- This is a fix for InnoDB in MySQL >= 4.1.x
-- It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-----------------------------------------------------------------------------
-- metadata_profile
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata_profile`;

CREATE TABLE `metadata_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`views_version` INTEGER,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`status` TINYINT,
	`object_type` INTEGER,
	PRIMARY KEY (`id`)
)Type=MyISAM;

-----------------------------------------------------------------------------
-- metadata_profile_field
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata_profile_field`;

CREATE TABLE `metadata_profile_field`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`metadata_profile_id` INTEGER,
	`metadata_profile_version` INTEGER,
	`partner_id` INTEGER,
	`label` VARCHAR(127),
	`key` VARCHAR(127),
	`type` VARCHAR(127),
	`xpath` VARCHAR(255),
	`status` TINYINT,
	PRIMARY KEY (`id`)
)Type=MyISAM;

-----------------------------------------------------------------------------
-- metadata
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata`;

CREATE TABLE `metadata`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`metadata_profile_id` INTEGER,
	`metadata_profile_version` INTEGER,
	`partner_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` INTEGER,
	`status` TINYINT,
	PRIMARY KEY (`id`)
)Type=MyISAM;

-- This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE  `upload_token` ADD  `dc` INT NULL;

ALTER TABLE  `admin_kuser`
ADD  `login_blocked_until` DATETIME NULL AFTER `partner_id`,
ADD  `custom_data` TEXT NULL AFTER `login_blocked_until`;

CREATE TABLE `audit_trail`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`parsed_at` DATETIME,
	`status` TINYINT,
	`object_type` VARCHAR(31),
	`object_id` VARCHAR(31),
	`related_object_id` VARCHAR(31),
	`related_object_type` VARCHAR(31),
	`entry_id` VARCHAR(31),
	`master_partner_id` INTEGER,
	`partner_id` INTEGER,
	`request_id` VARCHAR(31),
	`kuser_id` INTEGER,
	`action` VARCHAR(31),
	`data` TEXT,
	`ks` VARCHAR(511),
	`context` TINYINT,
	`entry_point` VARCHAR(127),
	`server_name` VARCHAR(63),
	`ip_address` VARCHAR(15),
	`user_agent` VARCHAR(127),
	`description` VARCHAR(1023),
	`error_description` VARCHAR(1023),
	PRIMARY KEY (`id`),
	KEY `object_index`(`object_type`, `object_id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `kuser_index`(`kuser_id`),
	KEY `status_index`(`status`)
)Type=MyISAM;

CREATE TABLE `audit_trail_data`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`audit_trail_id` INTEGER,
	`created_at` DATETIME,
	`object_type` VARCHAR(31),
	`object_id` VARCHAR(31),
	`partner_id` INTEGER,
	`action` VARCHAR(31),
	`descriptor` VARCHAR(127),
	`old_value` VARCHAR(511),
	`new_value` VARCHAR(511),
	PRIMARY KEY (`id`),
	KEY `object_index`(`object_type`, `object_id`),
	KEY `partner_index`(`partner_id`),
	KEY `audit_trail_index`(`audit_trail_id`)
)Type=MyISAM;

CREATE TABLE `audit_trail_config`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`object_type` VARCHAR(31),
	`descriptors` VARCHAR(1023),
	`actions` VARCHAR(1023),
	PRIMARY KEY (`id`),
	KEY `partner_object_index`(`partner_id`, `object_type`)
)Type=MyISAM;

DROP TABLE IF EXISTS `invalid_session`;

CREATE TABLE `invalid_session`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`ks` VARCHAR(300),
	`ks_valid_until` DATETIME,
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `ks_index`(`ks`),
	KEY `ks_valid_until_index`(`ks_valid_until`)
)Type=MyISAM;
