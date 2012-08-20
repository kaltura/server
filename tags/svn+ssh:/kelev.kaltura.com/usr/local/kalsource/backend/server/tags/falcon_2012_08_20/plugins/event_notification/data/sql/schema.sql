
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- event_notification_template
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `event_notification_template`;


CREATE TABLE `event_notification_template`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(127)  NOT NULL,
	`system_name` VARCHAR(127),
	`description` VARCHAR(255),
	`type` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	`event_type` INTEGER,
	`object_type` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
