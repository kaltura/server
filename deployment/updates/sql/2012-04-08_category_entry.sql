CREATE TABLE `category_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`entry_id` VARCHAR(20),
	`category_id` INTEGER,
	`created_at` DATETIME,
	`status` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id_category_id_index`(`partner_id`, `category_id`),
	KEY `partner_id_entry_id_index`(`partner_id`, `entry_id`)
)Type=MyISAM;