CREATE TABLE `kuser_kgroup`
(
	`id` BIGINT  NOT NULL AUTO_INCREMENT,
	`kuser_id` INTEGER  NOT NULL,
	`puser_id` VARCHAR(100) NOT NULL,
	`kgroup_id` INTEGER  NOT NULL,
	`pgroup_id` VARCHAR(100) NOT NULL,
	`status` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_kuser_index`(`kuser_id`, `status`),
	KEY `partner_kgroup_index`(`kgroup_id`, `status`),
	KEY `partner_index`(`partner_id`, `status`),
	CONSTRAINT `kuser_kgroup_FK_1`
	FOREIGN KEY (`kgroup_id`)
	REFERENCES `kuser` (`id`),
	CONSTRAINT `kuser_kgroup_FK_2`
	FOREIGN KEY (`kuser_id`)
	REFERENCES `kuser` (`id`)
)Type=InnoDB;