
CREATE TABLE `bulk_upload_result`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`bulk_upload_job_id` INTEGER,
	`line_index` INTEGER,
	`partner_id` INTEGER,
	`entry_id` INTEGER,
	`row_data` VARCHAR(1023),
	`title` VARCHAR(127),
	`description` VARCHAR(255),
	`tags` VARCHAR(255),
	`url` VARCHAR(255),
	`content_type` VARCHAR(31),
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `entry_id_index_id`(`entry_id`, `id`)
)Type=MyISAM;
