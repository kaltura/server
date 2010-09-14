DROP TABLE IF EXISTS  `system_user`;
CREATE TABLE `system_user`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(50)  NOT NULL,
	`first_name` VARCHAR(40)  NOT NULL,
	`last_name` VARCHAR(40)  NOT NULL,
	`sha1_password` VARCHAR(40)  NOT NULL,
	`salt` VARCHAR(32)  NOT NULL,
	`created_by` INTEGER,
	`status` TINYINT  NOT NULL,
	`status_updated_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `system_user_email_unique` (`email`)
)Type=MyISAM;
