CREATE TABLE `business_process_case`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`case_id` INTEGER,
	`process_id` VARCHAR(255),
	`template_id` INTEGER,
	`server_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`),
	KEY `object_id_and_type`(`object_type`, `object_id`, `partner_id`),
	KEY `server_id_and_process_id`(`server_id`, `process_id`, `object_type`, `object_id`, `partner_id`)
)Type=InnoDB DEFAULT CHARSET=utf8;