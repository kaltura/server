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
