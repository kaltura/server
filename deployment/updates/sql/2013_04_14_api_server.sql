
CREATE TABLE `api_server`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`hostname` VARCHAR(256),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`)
)Type=InnoDB;

