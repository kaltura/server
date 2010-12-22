
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- annotation
#-----------------------------------------------------------------------------

CREATE TABLE `annotation`
(
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`id` VARCHAR(255)  NOT NULL,
	`parent_id` VARCHAR(255),
	`entry_id` VARCHAR(31)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`text` TEXT,
	`tags` VARCHAR(255),
	`start_time` INTEGER,
	`end_time` INTEGER,
	`status` TINYINT  NOT NULL,
	`kuser_id` INTEGER,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `int_id_index`(`int_id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
