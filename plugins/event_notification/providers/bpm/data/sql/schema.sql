
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- business_process_server
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `business_process_server`;


CREATE TABLE `business_process_server`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`system_name` VARCHAR(127),
	`description` VARCHAR(255),
	`status` TINYINT,
	`type` INTEGER,
	`custom_data` TEXT,
	`dc` INTEGER,
	PRIMARY KEY (`id`)
)ENGINE=InnoDB;

#-----------------------------------------------------------------------------
#-- business_process_case
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `business_process_case`;


CREATE TABLE `business_process_case`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`case_id` VARCHAR(64),
	`process_id` VARCHAR(255),
	`template_id` INTEGER,
	`server_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` INTEGER,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`),
	KEY `object_id_and_type`(`object_type`, `object_id`)
)ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
