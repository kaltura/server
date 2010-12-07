
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- annotation
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `annotation`;


CREATE TABLE `annotation`
(
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`id` VARCHAR(255)  NOT NULL,
	`session_id` INTEGER,
	`entry_id` VARCHAR(31),
	`partner_id` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`data` TEXT,
	`tag` VARCHAR(255),
	`start_time` TIME,
	`end_time` TIME,
	`status` TINYINT,
	`kuser_id` INTEGER,
	`partner_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `session_entry_index`(`partner_id`, `session_id`, `entry_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- annotation_session
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `annotation_session`;


CREATE TABLE `annotation_session`
(
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`id` VARCHAR(255)  NOT NULL,
	`session_id` INTEGER,
	`entry_id` VARCHAR(31),
	`partner_id` INTEGER,
	`kuser_id` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`status` TINYINT,
	PRIMARY KEY (`id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `session_entry_index`(`partner_id`, `session_id`, `entry_id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
