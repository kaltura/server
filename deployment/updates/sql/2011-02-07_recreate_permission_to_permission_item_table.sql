DROP TABLE IF EXISTS `permission_to_permission_item`;


CREATE TABLE `permission_to_permission_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`permission_id` INTEGER  NOT NULL,
	`permission_item_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `permission_to_permission_item_FI_1` (`permission_id`),
	CONSTRAINT `permission_to_permission_item_FK_1`
		FOREIGN KEY (`permission_id`)
		REFERENCES `permission` (`id`),
	INDEX `permission_to_permission_item_FI_2` (`permission_item_id`),
	CONSTRAINT `permission_to_permission_item_FK_2`
		FOREIGN KEY (`permission_item_id`)
		REFERENCES `permission_item` (`id`)
)Type=MyISAM;