-- TODO: add a FK if needed and line index
CREATE TABLE `xml_upload_results`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	`xml_upload_job_id` INTEGER  NOT NULL,
	`item_index` INTEGER  NOT NULL,
	`entry_id` INTEGER  NOT NULL,
	`entry_status` INTEGER NOT NULL,
	`raw_data` VARCHAR(255),
	`error_description` VARCHAR(255),
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `xml_bulk_upload_id_index`(`xml_upload_id`)
)ENGINE=MyISAM;

