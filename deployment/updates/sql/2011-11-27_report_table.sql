CREATE TABLE `report`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`system_name` VARCHAR(128) default '' NOT NULL,
	`description` VARCHAR(1024) default '' NOT NULL,
	`query` TEXT  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	PRIMARY KEY (`id`)
)Type=MyISAM;