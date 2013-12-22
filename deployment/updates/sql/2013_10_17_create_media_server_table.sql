CREATE TABLE `media_server`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`hostname` VARCHAR(255),
	`dc` INTEGER,
	`custom_data` TEXT,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
