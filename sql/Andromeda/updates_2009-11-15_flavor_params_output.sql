ALTER TABLE flavor_params ADD version INT NOT NULL AFTER id;

CREATE TABLE `flavor_params_output`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`flavor_params_id` INTEGER  NOT NULL,
	`flavor_params_version` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`entry_id` VARCHAR(20)  NOT NULL,
	`flavor_asset_id` VARCHAR(20)  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`tags` TEXT,
	`description` VARCHAR(1024) default '' NOT NULL,
	`ready_behavior` TINYINT  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
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
	PRIMARY KEY (`id`),
	INDEX `flavor_params_output_FI_1` (`flavor_params_id`),
	CONSTRAINT `flavor_params_output_FK_1`
		FOREIGN KEY (`flavor_params_id`)
		REFERENCES `flavor_params` (`id`),
	INDEX `flavor_params_output_FI_2` (`entry_id`),
	CONSTRAINT `flavor_params_output_FK_2`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `flavor_params_output_FI_3` (`flavor_asset_id`),
	CONSTRAINT `flavor_params_output_FK_3`
		FOREIGN KEY (`flavor_asset_id`)
		REFERENCES `flavor_asset` (`id`)
)Type=MyISAM;
