
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- short_link
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `short_link`;


CREATE TABLE `short_link`
(
	`id` VARCHAR(20)  NOT NULL,
	`int_id` BIGINT NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`expires_at` DATETIME,
	`partner_id` INTEGER,
	`kuser_id` INTEGER,
	`name` VARCHAR(63),
	`system_name` VARCHAR(63),
	`full_url` VARCHAR(255),
	`status` INTEGER,
	`unique_id` VARCHAR(63),
	`custom_data` text,
	PRIMARY KEY (`id`),
	KEY `int_id`(`int_id`),
	KEY `expires_at`(`expires_at`),
	KEY `kuser_partner_name`(`partner_id`, `kuser_id`, `system_name`),
	KEY `partner_unique_id`(`partner_id`, `unique_id`),
	KEY `partner_kuser_status`(`partner_id`, `kuser_id`, `status`),
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
