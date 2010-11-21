
CREATE TABLE `dynamic_enum`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`enum_name` VARCHAR(255)  NOT NULL,
	`value_name` VARCHAR(255)  NOT NULL,
	`plugin_name` VARCHAR(255),
	PRIMARY KEY (`id`)
)Type=MyISAM;
ALTER TABLE `dynamic_enum` AUTO_INCREMENT = 10001;
