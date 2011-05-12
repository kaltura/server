
CREATE TABLE `virus_scan_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`status` INTEGER,
	`engine_type` INTEGER,
	`entry_filter` TEXT,
	`action_if_infected` INTEGER,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`)
)Type=MyISAM;

