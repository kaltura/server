
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- virtual_event
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `virtual_event`;


CREATE TABLE `virtual_event`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256),
	`description` TEXT,
	`partner_id` INTEGER,
	`status` INTEGER,
	`tags` TEXT,
	`custom_data` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deletion_due_date` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `name_index`(`name`, `partner_id`),
	KEY `status_index`(`status`, `partner_id`),
	KEY `deletion_index`(`deletion_due_date`)
)ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
