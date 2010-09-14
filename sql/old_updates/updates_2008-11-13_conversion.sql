
DROP TABLE IF EXISTS `conversion_profile`;


CREATE TABLE `conversion_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER default 0,
	`enabled` TINYINT default 1,
	`name` VARCHAR(128),
	`profile_type` VARCHAR(128),
	`commercial_transcoder` TINYINT,
	`width` INTEGER,
	`height` INTEGER,
	`aspect_ratio` VARCHAR(6),
	`bypass_flv` TINYINT,
	`use_with_bulk` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- conversion_params
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `conversion_params`;


CREATE TABLE `conversion_params`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`enabled` TINYINT default 1,
	`name` VARCHAR(128),
	`profile_type` VARCHAR(128),
	`profile_type_index` INTEGER,
	`width` INTEGER,
	`height` INTEGER,
	`aspect_ratio` VARCHAR(6),
	`gop_size` INTEGER,
	`bitrate` INTEGER,
	`qscale` INTEGER,
	`file_suffix` VARCHAR(64),
	`custom_data` VARCHAR(4096),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;