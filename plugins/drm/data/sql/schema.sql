
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- drm_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `drm_profile`;


CREATE TABLE `drm_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` TEXT  NOT NULL,
	`description` TEXT,
	`provider` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`license_server_url` TEXT,
	`default_policy` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `drm_profile_unique` (`partner_id`, `provider`, `status`),
	KEY `partner_id_index`(`partner_id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
