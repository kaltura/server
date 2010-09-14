
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
