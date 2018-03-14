CREATE TABLE IF NOT EXISTS `reach_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER  NOT NULL,
	`type` TINYINT  NOT NULL,
	`status` TINYINT  NOT NULL,
	`used_credit` INTEGER,
	`rules` TEXT,
	`dictionary` TEXT,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `partner_id_type_index`(`partner_id`, `type`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;