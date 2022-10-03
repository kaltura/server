CREATE TABLE IF NOT EXISTS `resource_user`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`resource_tag` TEXT  NOT NULL,
	`kuser_id` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`creator_kuser_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_kuser_id`(`partner_id`, `kuser_id`, `status`),
	KEY `partner_id_resource_tag`(`partner_id`, `resource_tag`, `status`),
	KEY `resource_id_kuser_id_status`(`kuser_id`, `resource_tag`, `status`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;