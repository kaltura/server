CREATE TABLE IF NOT EXISTS `sso`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`application_type` varchar(64) NOT NULL,
	`partner_id` int default null,
	`domain` varchar(64) default null,
	`status` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` text,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`,`status`),
	KEY `domain_status_index`(`domain`,`status`)
) Engine=InnoDB DEFAULT CHARSET=utf8;