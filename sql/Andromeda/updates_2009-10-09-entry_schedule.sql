# ignore on server update
CREATE TABLE `entry_schedule`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default null,
	`start_date` DATETIME default null,
	`end_date` DATETIME default null,
	PRIMARY KEY (`id`)
)Type=MyISAM;


ALTER TABLE entry ADD COLUMN `entry_schedule_id` INTEGER;