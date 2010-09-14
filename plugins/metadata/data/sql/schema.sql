
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- metadata_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata_profile`;


CREATE TABLE `metadata_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`views_version` INTEGER,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`status` TINYINT,
	`object_type` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- metadata_profile_field
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata_profile_field`;


CREATE TABLE `metadata_profile_field`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`metadata_profile_id` INTEGER,
	`metadata_profile_version` INTEGER,
	`partner_id` INTEGER,
	`label` VARCHAR(127),
	`key` VARCHAR(127),
	`type` VARCHAR(127),
	`xpath` VARCHAR(255),
	`status` TINYINT,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`),
	KEY `profile_id_and_version`(`metadata_profile_id`, `metadata_profile_version`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- metadata
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata`;


CREATE TABLE `metadata`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`metadata_profile_id` INTEGER,
	`metadata_profile_version` INTEGER,
	`partner_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` INTEGER,
	`status` TINYINT,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`),
	KEY `profile_id_and_version`(`metadata_profile_id`, `metadata_profile_version`),
	KEY `object_id_and_type`(`object_id`, `object_type`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
