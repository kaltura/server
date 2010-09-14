CREATE TABLE `email_ingestion_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(60) default '' NOT NULL,
	`description` TEXT,
	`email_address` VARCHAR(50)  NOT NULL,
	`mailbox_id` VARCHAR(50)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`conversion_profile_2_id` INTEGER,
	`moderation_status` TINYINT,
	`custom_data` TEXT,
	`status` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email_ingestion_profile_email_address_unique` (`email_address`)
);