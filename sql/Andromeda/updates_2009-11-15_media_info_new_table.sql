DROP TABLE IF EXISTS `media_info`;


CREATE TABLE `media_info`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`flavor_asset_id` INTEGER,
	`file_size` INTEGER  NOT NULL,
	`container_format` VARCHAR(6),
	`container_id` VARCHAR(127),
	`container_profile` VARCHAR(127),
	`container_duration` INTEGER,
	`container_bit_rate` INTEGER,
	`video_format` VARCHAR(6),
	`video_codec_id` VARCHAR(127),
	`video_duration` INTEGER,
	`video_bit_rate` INTEGER,
	`video_bit_rate_mode` TINYINT,
	`video_width` INTEGER  NOT NULL,
	`video_height` INTEGER  NOT NULL,
	`video_frame_rate` INTEGER,
	`video_dar` FLOAT,
	`audio_format` VARCHAR(6),
	`audio_codec_id` VARCHAR(127),
	`audio_duration` INTEGER,
	`audio_bit_rate` INTEGER,
	`audio_bit_rate_mode` TINYINT,
	`audio_channels` TINYINT,
	`audio_sampling_rate` INTEGER,
	`audio_resolution` INTEGER,
	`writing_lib` VARCHAR(127),
	`custom_data` TEXT,
	`raw_data` VARCHAR(1023),
	PRIMARY KEY (`id`),
	KEY `flavor_asset_id_index`(`flavor_asset_id`)
)Type=MyISAM;