
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- virtual_event
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `virtual_event`;


CREATE TABLE `virtual_event`
(
	`id` bigint(20)  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256),
	`description` varchar(1024),
	`partner_id` bigint(20),
	`status` tinyint(4),
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
    `custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `status_index`(`status`, `partner_id`)
	KEY `updated_at_index`(`updated_at`)
)ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
