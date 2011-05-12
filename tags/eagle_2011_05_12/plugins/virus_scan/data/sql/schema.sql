
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- virus_scan_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `virus_scan_profile`;


CREATE TABLE `virus_scan_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`status` INTEGER,
	`engine_type` INTEGER,
	`entry_filter` TEXT,
	`action_if_infected` INTEGER,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
