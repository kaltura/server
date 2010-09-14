DROP TABLE IF EXISTS `file_sync`;

CREATE TABLE `file_sync`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`object_type` TINYINT,
	`object_id` VARCHAR(20),
	`version` VARCHAR(20),
	`object_sub_type` TINYINT,
	`dc` VARCHAR(2),
	`original` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`ready_at` DATETIME,
	`sync_time` INTEGER,
	`status` TINYINT,
	`file_type` TINYINT,
	`linked_id` INTEGER,
	`link_count` INTEGER,
	`file_root` VARCHAR(64),
	`file_path` VARCHAR(128),
	`file_size` BIGINT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unique_key` (`object_type`,`object_id`,`version`,`object_sub_type`,`dc`),
	KEY `object_id_object_type_version_subtype_index`(`object_id`, `object_type`, `version`, `object_sub_type`),
	KEY `partner_id_object_id_object_type_index`(`partner_id`, `object_id`, `object_type`),
	KEY `dc_status_index`(`dc`, `status`)
)ENGINE=MyISAM;
