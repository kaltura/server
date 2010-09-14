
DROP TABLE IF EXISTS `media_info`;


CREATE TABLE `media_info`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`flavor_asset_id` INTEGER,
	`row_data` VARCHAR(1023),
	`container_format` VARCHAR(6),
	`container_duration` INTEGER,
	`container_bit_rate` INTEGER,
	`file_size` INTEGER default null,
	`video_format` VARCHAR(6),
	`video_duration` INTEGER,
	`video_bit_rate` INTEGER,
	`width` INTEGER default null,
	`height` INTEGER default null,
	`dar_width` INTEGER default null,
	`dar_height` INTEGER default null,
	`frame_rate` INTEGER default null,
	`audio_format` VARCHAR(6),
	`audio_duration` INTEGER,
	`audio_bit_rate` INTEGER,
	`channels` TINYINT default null,
	`sampling_rate` INTEGER default null,
	`resulution_width` INTEGER default null,
	`resulution_height` INTEGER default null,
	`format_profile` VARCHAR(127) default 'null',
	`codec_id` VARCHAR(127) default 'null',
	`codec_info` VARCHAR(127) default 'null',
	`codec_hint` VARCHAR(127) default 'null',
	`writing_lib` VARCHAR(127) default 'null',
	`format_id` VARCHAR(127) default 'null',
	`bit_rate_mode` VARCHAR(127) default 'null',
	`description` VARCHAR(127) default 'null',
	PRIMARY KEY (`id`),
	KEY `flavor_asset_id_index`(`flavor_asset_id`)
)Type=MyISAM;
