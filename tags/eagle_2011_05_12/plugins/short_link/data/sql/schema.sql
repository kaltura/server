
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- short_link
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `short_link`;


CREATE TABLE `short_link`
(
	`id` VARCHAR(5)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`expires_at` DATETIME,
	`partner_id` INTEGER,
	`kuser_id` INTEGER,
	`name` VARCHAR(63),
	`system_name` VARCHAR(63),
	`full_url` VARCHAR(255),
	`status` INTEGER,
	PRIMARY KEY (`id`),
	KEY `int_id`(`int_id`),
	KEY `partner_id`(`partner_id`),
	KEY `kuser_partner_name`(`partner_id`, `kuser_id`, `system_name`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
