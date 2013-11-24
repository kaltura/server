


CREATE TABLE `file_asset`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`partner_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` INTEGER,
	`status` TINYINT,
	`name` VARCHAR(255),
	`system_name` VARCHAR(255),
	`file_ext` VARCHAR(4),
	`size` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_object_status`(`partner_id`, `object_id`, `object_type`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

