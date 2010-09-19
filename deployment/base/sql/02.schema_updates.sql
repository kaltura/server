
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


CREATE TABLE `email_ingestion_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(60) default '' NOT NULL,
	`description` TEXT,
	`email_address` VARCHAR(50)  NOT NULL,
	`mailbox_id` VARCHAR(50)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`conversion_profile_2_id` INTEGER,
	`moderation_status` TINYINT,
	`custom_data` TEXT,
	`status` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email_ingestion_profile_email_address_unique` (`email_address`)
);


CREATE TABLE `upload_token`
(
	`id` VARCHAR(35)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER default 0,
	`kuser_id` INTEGER,
	`status` INTEGER,
	`file_name` VARCHAR(256),
	`file_size` BIGINT,
	`uploaded_file_size` BIGINT,
	`upload_temp_path` VARCHAR(256),
	`user_ip` VARCHAR(39)  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `int_id`(`int_id`),
	KEY `partner_id_status`(`partner_id`, `status`),
	KEY `partner_id_created_at`(`partner_id`, `created_at`),
	KEY `status_created_at`(`status`, `created_at`),
	KEY `created_at`(`created_at`),
	INDEX `upload_token_FI_1` (`kuser_id`),
	CONSTRAINT `upload_token_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

ALTER TABLE  `file_sync` CHANGE  `dc`  `dc` INT NULL DEFAULT NULL;
ALTER TABLE  `batch_job` CHANGE  `dc`  `dc` INT NULL DEFAULT NULL;

ALTER TABLE  `media_info` 
ADD  `flavor_asset_version` VARCHAR( 20 ) NOT NULL ,
ADD  `scan_type` INT NOT NULL ,
ADD  `multi_stream` VARCHAR( 255 ) NOT NULL;

ALTER TABLE  `flavor_params` 
ADD  `deinterlice` INT NOT NULL ,
ADD  `rotate` INT NOT NULL;

ALTER TABLE  `flavor_params_output` 
ADD  `deinterlice` INT NOT NULL ,
ADD  `rotate` INT NOT NULL;

ALTER TABLE  `storage_profile` ADD  `url_manager_class` VARCHAR( 127 ) NOT NULL;

ALTER TABLE `system_user` ADD COLUMN `role` VARCHAR(40) default 'admin' AFTER `deleted_at`;
UPDATE `system_user` SET role = 'admin';

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

/* This is a fix for InnoDB in MySQL >= 4.1.x
 It "suspends judgement" for fkey relationships until are tables are set. */
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
