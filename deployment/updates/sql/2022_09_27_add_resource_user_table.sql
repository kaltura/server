CREATE TABLE IF NOT EXISTS `resource_user`
(
	`id` bigint(20)  NOT NULL AUTO_INCREMENT,
	`resource_tag` VARCHAR(256)  NOT NULL,
	`kuser_id` int(11)  NOT NULL,
	`status` tinyint(4)  NOT NULL,
	`partner_id` int(11)  NOT NULL,
	`creator_kuser_id` int(11)  NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` DATETIME NOT NULL,
	PRIMARY KEY (`id`),
	KEY `partner_id_kuser_id`(`partner_id`, `kuser_id`, `status`),
	KEY `partner_id_resource_tag`(`partner_id`, `resource_tag`, `status`),
	KEY `resource_id_kuser_id_status`(`kuser_id`, `resource_tag`, `status`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;