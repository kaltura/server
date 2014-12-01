
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- scheduled_task_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduled_task_profile`;


CREATE TABLE `scheduled_task_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(127)  NOT NULL,
	`system_name` VARCHAR(127),
	`description` VARCHAR(255),
	`status` INTEGER  NOT NULL,
	`object_filter_engine_type` INTEGER  NOT NULL,
	`object_filter` TEXT  NOT NULL,
	`object_filter_api_type` VARCHAR(255)  NOT NULL,
	`object_tasks` TEXT  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`last_execution_started_at` DATETIME,
	`max_total_count_allowed` INTEGER  NOT NULL,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `system_name_partner_id`(`system_name`, `partner_id`),
	KEY `status_last_execution_started_at`(`status`, `last_execution_started_at`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
