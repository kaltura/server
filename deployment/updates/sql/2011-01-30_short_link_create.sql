
CREATE TABLE `short_link`
(
	`id` VARCHAR(5)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`expires_at` DATETIME,
	`partner_id` INTEGER,
	`kuser_id` INTEGER,
	`name` VARCHAR(63),
	`system_name` VARCHAR(63),
	`full_url` VARCHAR(255),
	`status` INTEGER,
	PRIMARY KEY (`id`),
	KEY `int_id`(`int_id`),
	KEY `partner_id`(`partner_id`),
	KEY `kuser_partner_name`(`partner_id`, `kuser_id`, `system_name`)
)Type=MyISAM;
