
CREATE TABLE `live_channel_segment`
(
	`id` BIGINT  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`name` VARCHAR(255),
	`description` TEXT,
	`tags` TEXT,
	`type` INTEGER,
	`status` INTEGER,
	`channel_id` VARCHAR(20),
	`entry_id` VARCHAR(20),
	`trigger_type` INTEGER,
	`trigger_segment_id` BIGINT,
	`start_time` FLOAT,
	`duration` FLOAT,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_channel_status_index`(`partner_id`, `channel_id`, `status`),
	KEY `partner_entry_status_index`(`partner_id`, `entry_id`, `status`),
	INDEX `live_channel_segment_FI_1` (`trigger_segment_id`),
	CONSTRAINT `live_channel_segment_FK_1`
		FOREIGN KEY (`trigger_segment_id`)
		REFERENCES `live_channel_segment` (`id`),
	CONSTRAINT `live_channel_segment_FK_2`
		FOREIGN KEY (`partner_id`)
		REFERENCES `partner` (`id`),
	INDEX `live_channel_segment_FI_3` (`channel_id`),
	CONSTRAINT `live_channel_segment_FK_3`
		FOREIGN KEY (`channel_id`)
		REFERENCES `entry` (`id`),
	INDEX `live_channel_segment_FI_4` (`entry_id`),
	CONSTRAINT `live_channel_segment_FK_4`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`)
)Type=InnoDB DEFAULT CHARSET=utf8;
