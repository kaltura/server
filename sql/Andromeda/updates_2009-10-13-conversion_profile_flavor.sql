CREATE TABLE `flavor_params`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`tags` TEXT,
	`description` VARCHAR(1024) default '' NOT NULL,
	`ready_behavior` TINYINT  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default null,
	`is_default` TINYINT default 0 NOT NULL,
	`format` VARCHAR(20)  NOT NULL,
	`video_codec` VARCHAR(20)  NOT NULL,
	`video_bitrate` INTEGER default 0 NOT NULL,
	`audio_codec` VARCHAR(20)  NOT NULL,
	`audio_bitrate` INTEGER default 0 NOT NULL,
	`audio_channels` TINYINT default 0 NOT NULL,
	`audio_sample_rate` INTEGER default 0,
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`frame_rate` INTEGER default 0 NOT NULL,
	`gop_size` INTEGER default 0 NOT NULL,
	`conversion_engine` VARCHAR(1024),
	`conversion_engine_extra_params` VARCHAR(1024),
	`custom_data` TEXT,
	PRIMARY KEY (`id`)
)Type=MyISAM;

CREATE TABLE `flavor_asset`
(
	`id` VARCHAR(20)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default null,
	`entry_id` VARCHAR(20)  NOT NULL,
	`flavor_params_id` INTEGER  NOT NULL,
	`status` TINYINT,
	`version` VARCHAR(20),
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`bitrate` INTEGER default 0 NOT NULL,
	`frame_rate` INTEGER default 0 NOT NULL,
	`size` INTEGER default 0 NOT NULL,
	PRIMARY KEY (`int_id`),
	INDEX `flavor_asset_FI_1` (`entry_id`),
	CONSTRAINT `flavor_asset_FK_1`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `flavor_asset_FI_2` (`flavor_params_id`),
	CONSTRAINT `flavor_asset_FK_2`
		FOREIGN KEY (`flavor_params_id`)
		REFERENCES `flavor_params` (`id`)
)Type=MyISAM;

CREATE TABLE `conversion_profile_2`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default null,
	`description` VARCHAR(1024) default '' NOT NULL,
	`crop_left` INTEGER default -1 NOT NULL,
	`crop_top` INTEGER default -1 NOT NULL,
	`crop_width` INTEGER default -1 NOT NULL,
	`crop_height` INTEGER default -1 NOT NULL,
	`clip_start` INTEGER default -1 NOT NULL,
	`clip_duration` INTEGER default -1 NOT NULL,
	PRIMARY KEY (`id`)
)Type=MyISAM;

CREATE TABLE `flavor_params_conversion_profile`
(
	`conversion_profile_id` INTEGER  NOT NULL,
	`flavor_params_id` INTEGER  NOT NULL,
	`ready_behavior` TINYINT  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `flavor_params_conversion_profile_FI_1` (`conversion_profile_id`),
	CONSTRAINT `flavor_params_conversion_profile_FK_1`
		FOREIGN KEY (`conversion_profile_id`)
		REFERENCES `conversion_profile_2` (`id`),
	INDEX `flavor_params_conversion_profile_FI_2` (`flavor_params_id`),
	CONSTRAINT `flavor_params_conversion_profile_FK_2`
		FOREIGN KEY (`flavor_params_id`)
		REFERENCES `flavor_params` (`id`),
	INDEX `updated_at_FI_3` (`updated_at`)
)Type=MyISAM;

ALTER TABLE `entry` ADD	(
	`conversion_profile_id` INTEGER,
	INDEX `entry_FI_3` (`access_control_id`),
	CONSTRAINT `entry_FK_3`
		FOREIGN KEY (`access_control_id`)
		REFERENCES `access_control` (`id`),
	INDEX `entry_FI_4` (`entry_schedule_id`),
	CONSTRAINT `entry_FK_4`
		FOREIGN KEY (`entry_schedule_id`)
		REFERENCES `entry_schedule` (`id`),
	INDEX `entry_FI_5` (`conversion_profile_id`),
	CONSTRAINT `entry_FK_5`
		FOREIGN KEY (`conversion_profile_id`)
		REFERENCES `conversion_profile_2` (`id`)
);