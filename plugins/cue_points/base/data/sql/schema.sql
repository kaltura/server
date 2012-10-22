
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- cue_point
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `cue_point`;


CREATE TABLE `cue_point`
(
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`id` VARCHAR(31)  NOT NULL,
	`parent_id` VARCHAR(31),
	`entry_id` VARCHAR(31)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`name` VARCHAR(255),
	`system_name` VARCHAR(127),
	`text` TEXT,
	`tags` VARCHAR(255),
	`start_time` INTEGER  NOT NULL,
	`end_time` INTEGER,
	`status` INTEGER  NOT NULL,
	`type` INTEGER  NOT NULL,
	`sub_type` INTEGER  NOT NULL,
	`kuser_id` INTEGER  NOT NULL,
	`custom_data` TEXT,
	`partner_data` TEXT,
	`partner_sort_value` INTEGER,
	`thumb_offset` INTEGER,
	`depth` INTEGER,
	`children_count` INTEGER,
	`direct_children_count` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `int_id_index`(`int_id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
