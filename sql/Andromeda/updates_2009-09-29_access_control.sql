CREATE TABLE `access_control`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`status` TINYINT  NOT NULL,
	`description` VARCHAR(1024) default '' NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`site_restrict_type` TINYINT,
	`site_restrict_list` VARCHAR(1024),
	`country_restrict_type` TINYINT,
	`country_restrict_list` VARCHAR(1024),
	`schd_restrict_start_date` DATETIME,
	`schd_restrict_end_date` DATETIME,
	`ks_restrict_privilege` VARCHAR(20),
	`prv_restrict_privilege` VARCHAR(20),
	`prv_restrict_length` INTEGER,
	`kdir_restrict_type` TINYINT,
	PRIMARY KEY (`id`)
)Type=MyISAM;