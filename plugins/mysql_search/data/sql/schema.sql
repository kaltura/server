
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- search_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `search_entry`;


CREATE TABLE `search_entry`
(
	`entry_id` VARCHAR(20)  NOT NULL,
	`kuser_id` INTEGER,
	`name` VARCHAR(60),
	`type` SMALLINT,
	`media_type` SMALLINT,
	`views` INTEGER default 0,
	`rank` INTEGER default 0,
	`tags` TEXT,
	`status` INTEGER,
	`source_link` VARCHAR(1024),
	`duration` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER default 0,
	`display_in_search` TINYINT,
	`group_id` VARCHAR(64),
	`plays` INTEGER default 0,
	`description` TEXT,
	`media_date` DATETIME,
	`admin_tags` TEXT,
	`moderation_status` INTEGER,
	`moderation_count` INTEGER,
	`modified_at` DATETIME,
	`access_control_id` INTEGER,
	`categories` VARCHAR(4096),
	`start_date` DATETIME,
	`end_date` DATETIME,
	`flavor_params` VARCHAR(512),
	`available_from` DATETIME,
	`plugin_data` TEXT,
	PRIMARY KEY (`entry_id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
