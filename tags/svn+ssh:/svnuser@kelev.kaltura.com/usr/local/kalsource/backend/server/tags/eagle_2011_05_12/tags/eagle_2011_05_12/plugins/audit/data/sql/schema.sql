
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- audit_trail
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `audit_trail`;


CREATE TABLE `audit_trail`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`parsed_at` DATETIME,
	`status` TINYINT,
	`object_type` VARCHAR(31),
	`object_id` VARCHAR(31),
	`related_object_id` VARCHAR(31),
	`related_object_type` VARCHAR(31),
	`entry_id` VARCHAR(31),
	`master_partner_id` INTEGER,
	`partner_id` INTEGER,
	`request_id` VARCHAR(31),
	`kuser_id` INTEGER,
	`action` VARCHAR(31),
	`data` TEXT,
	`ks` VARCHAR(511),
	`context` TINYINT,
	`entry_point` VARCHAR(127),
	`server_name` VARCHAR(63),
	`ip_address` VARCHAR(15),
	`user_agent` VARCHAR(127),
	`client_tag` VARCHAR(127),
	`description` VARCHAR(1023),
	`error_description` VARCHAR(1023),
	PRIMARY KEY (`id`),
	KEY `object_index`(`object_type`, `object_id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `kuser_index`(`kuser_id`),
	KEY `status_index`(`status`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- audit_trail_data
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `audit_trail_data`;


CREATE TABLE `audit_trail_data`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`audit_trail_id` INTEGER,
	`created_at` DATETIME,
	`object_type` VARCHAR(31),
	`object_id` VARCHAR(31),
	`partner_id` INTEGER,
	`action` VARCHAR(31),
	`descriptor` VARCHAR(127),
	`old_value` VARCHAR(511),
	`new_value` VARCHAR(511),
	PRIMARY KEY (`id`),
	KEY `object_index`(`object_type`, `object_id`),
	KEY `partner_index`(`partner_id`),
	KEY `audit_trail_index`(`audit_trail_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- audit_trail_config
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `audit_trail_config`;


CREATE TABLE `audit_trail_config`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`object_type` VARCHAR(31),
	`descriptors` VARCHAR(1023),
	`actions` VARCHAR(1023),
	PRIMARY KEY (`id`),
	KEY `partner_object_index`(`partner_id`, `object_type`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
