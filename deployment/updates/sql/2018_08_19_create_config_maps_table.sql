create table if not exists conf_maps
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`map_name` VARCHAR(256),
	`host_name` VARCHAR(256),
	`status` INTEGER,
	`version` INTEGER NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`remarks` TEXT,
	`content` TEXT,
	PRIMARY KEY (`id`),
	KEY `configuration_map_source` (`map_name`,`host_name`,`version`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;
