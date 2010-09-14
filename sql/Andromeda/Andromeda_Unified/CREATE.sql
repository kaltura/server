use kaltura;

#updates_2009-08-04_file_sync.sql
DROP TABLE IF EXISTS `file_sync`;
CREATE TABLE `file_sync`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`object_type` TINYINT,
	`object_id` VARCHAR(20),
	`version` VARCHAR(20),
	`object_sub_type` TINYINT,
	`dc` VARCHAR(2),
	`original` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`ready_at` DATETIME,
	`sync_time` INTEGER,
	`status` TINYINT,
	`file_type` TINYINT,
	`linked_id` INTEGER,
	`link_count` INTEGER,
	`file_root` VARCHAR(64),
	`file_path` VARCHAR(128),
	`file_size` BIGINT,
	PRIMARY KEY (`id`),
	UNIQUE KEY `unique_key` (`object_type`,`object_id`,`version`,`object_sub_type`,`dc`),
	KEY `object_id_object_type_version_subtype_index`(`object_id`, `object_type`, `version`, `object_sub_type`),
	KEY `partner_id_object_id_object_type_index`(`partner_id`, `object_id`, `object_type`),
	KEY `dc_status_index`(`dc`, `status`)
)ENGINE=MyISAM;

#updates_2009-09-29_access_control.sql
/*
#updates_2009-10-09-access_control_change_status_to_deleted_at.sql
ALTER TABLE kaltura.access_control DROP COLUMN `status`;
ALTER TABLE kaltura.access_control ADD COLUMN `deleted_at` DATETIME default null AFTER `updated_at`;

#updates_2009-10-09-access_control_remove_scheduling.sql
ALTER TABLE kaltura.access_control DROP COLUMN `schd_restrict_start_date`;
ALTER TABLE kaltura.access_control DROP COLUMN `schd_restrict_end_date`;
*/
DROP TABLE IF EXISTS `access_control`;
CREATE TABLE `access_control`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
#	`status` TINYINT  NOT NULL,
	`description` VARCHAR(1024) default '' NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
  `deleted_at` DATETIME default null,
	`site_restrict_type` TINYINT,
	`site_restrict_list` VARCHAR(1024),
	`country_restrict_type` TINYINT,
	`country_restrict_list` VARCHAR(1024),
#	`schd_restrict_start_date` DATETIME,
#	`schd_restrict_end_date` DATETIME,
	`ks_restrict_privilege` VARCHAR(20),
	`prv_restrict_privilege` VARCHAR(20),
	`prv_restrict_length` INTEGER,
	`kdir_restrict_type` TINYINT,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

#updates_2009-10-09-entry_schedule.sql
#ignore on server update
#updates_2009-11-05-remove_entry_schedule.sql
#DROP TABLE IF EXISTS `entry_schedule`;
/*CREATE TABLE `entry_schedule`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default null,
	`start_date` DATETIME default null,
	`end_date` DATETIME default null,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;
*/

#updates_2009-10-13-conversion_profile_flavor.sql
DROP TABLE IF EXISTS `flavor_params`;
CREATE TABLE `flavor_params`
/*
ALTER TABLE `conversion_profile` ADD `bypass_by_extension` VARCHAR(32);
ALTER TABLE flavor_params ADD `audio_resolution` INTEGER default 0 AFTER `audio_sample_rate`;
ALTER TABLE flavor_params ADD version INT NOT NULL AFTER id;
ALTER TABLE flavor_params DROP conversion_engine;
ALTER TABLE flavor_params DROP conversion_engine_extra_params;
ALTER TABLE flavor_params ADD conversion_engines VARCHAR(1024) AFTER gop_size;
ALTER TABLE flavor_params ADD conversion_engines_extra_params VARCHAR(1024) AFTER conversion_engines;
ALTER TABLE flavor_params CHANGE frame_rate frame_rate FLOAT default 0 NOT NULL; 
ALTER TABLE `flavor_params` ADD `two_pass` INTEGER default 0 NOT NULL AFTER `gop_size`;
ALTER TABLE  `flavor_params` ADD  `view_order` INT DEFAULT 0 AFTER  `custom_data`;
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
  `version` INT default 0 NOT NULL,
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
  `audio_resolution` INTEGER default 0,
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`frame_rate` FLOAT default 0 NOT NULL,
	`gop_size` INTEGER default 0 NOT NULL,
  `two_pass` INTEGER default 0 NOT NULL,
  `conversion_engines` VARCHAR(1024),
  `conversion_engines_extra_params` VARCHAR(1024),
  `custom_data` TEXT,
  `view_order` INT DEFAULT 0,
  `bypass_by_extension` VARCHAR(32),
	`creation_mode` smallint default 1,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `flavor_asset`;
CREATE TABLE `flavor_asset`
/*
ALTER TABLE flavor_asset ADD KEY partner_id_entry_id (partner_id, entry_id);
ALTER TABLE `flavor_asset` ADD `description` VARCHAR( 255 ) NOT NULL AFTER `version`;
ALTER TABLE `flavor_asset` ADD `is_original` INT NOT NULL DEFAULT '0' AFTER `size`;
ALTER TABLE flavor_asset CHANGE frame_rate frame_rate FLOAT default 0 NOT NULL; 
ALTER TABLE  `flavor_asset` ADD  `file_ext` VARCHAR( 4 ) NULL DEFAULT NULL AFTER  `is_original`;
*/
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
  `description` VARCHAR( 255 ) NOT NULL,
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`bitrate` INTEGER default 0 NOT NULL,
	`frame_rate` FLOAT default 0 NOT NULL,
	`size` INTEGER default 0 NOT NULL,
  `is_original` INT NOT NULL DEFAULT '0',
  `file_ext` VARCHAR( 4 ) NULL DEFAULT NULL,
  `container_format` VARCHAR(127) NULL DEFAULT  NULL,
  `video_codec_id` VARCHAR(127) NULL DEFAULT  NULL,
	PRIMARY KEY (`int_id`),
  KEY partner_id_entry_id (partner_id, entry_id),
	INDEX `flavor_asset_FI_1` (`entry_id`),
	CONSTRAINT `flavor_asset_FK_1`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `flavor_asset_FI_2` (`flavor_params_id`),
	CONSTRAINT `flavor_asset_FK_2`
		FOREIGN KEY (`flavor_params_id`)
		REFERENCES `flavor_params` (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `conversion_profile_2`;
CREATE TABLE `conversion_profile_2`
#ALTER TABLE `conversion_profile_2` ADD `input_tags_map` VARCHAR(1023) default NULL AFTER `clip_duration`;
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
  `input_tags_map` VARCHAR(1023) default NULL,
	`creation_mode` smallint default 1,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `flavor_params_conversion_profile`;
CREATE TABLE `flavor_params_conversion_profile`
/*
ALTER TABLE `flavor_params_conversion_profile` ADD `force` INTEGER default 0 AFTER `ready_behavior`;
ALTER TABLE `flavor_params_conversion_profile` ADD `created_at` DATETIME AFTER `force`;
ALTER TABLE `flavor_params_conversion_profile` ADD `updated_at` DATETIME AFTER `created_at`;
ALTER TABLE  `flavor_params_conversion_profile` CHANGE  `force`  `force_none_complied` INT( 11 ) NULL DEFAULT  '0';
*/
(
	`conversion_profile_id` INTEGER  NOT NULL,
	`flavor_params_id` INTEGER  NOT NULL,
	`ready_behavior` TINYINT  NOT NULL,
  `force_none_complied` INT( 11 ) NULL DEFAULT  '0',
  `created_at` DATETIME,
  `updated_at` DATETIME,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
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
)ENGINE=MyISAM;

#updates_2009-10-18_category.sql
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER  NOT NULL,
	`depth` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`full_name` VARCHAR(490) default '' NOT NULL,
	`entries_count` INTEGER default 0 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default NULL,
	PRIMARY KEY (`id`)
#	KEY `partner_id_full_name_index`(`partner_id`, `full_name`)
)ENGINE=MyISAM;

#updates_2009-10-22_batch_tables.sql
#updates_2009-10-28_batch_tables.sql
#ALTER TABLE `scheduler` ADD `configured_id` INTEGER NOT NULL AFTER `updated_by` ;
DROP TABLE IF EXISTS `scheduler`;
CREATE TABLE `scheduler`
#ALTER TABLE scheduler ADD statuses VARCHAR( 255 ) NOT NULL;
#ALTER TABLE `scheduler` ADD `last_status` DATETIME NOT NULL AFTER `statuses`;
#ALTER TABLE `scheduler` ADD `host` VARCHAR(63) default '' NOT NULL AFTER `last_status`;
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
  `configured_id` INTEGER NOT NULL,
	`name` VARCHAR(20) default '',
	`description` VARCHAR(20) default '',
  `statuses` VARCHAR( 255 ) NOT NULL,
  `last_status` DATETIME NOT NULL,
  `host` VARCHAR(63) default '' NOT NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `scheduler_worker`;
CREATE TABLE `scheduler_worker`
/*
ALTER TABLE `scheduler_worker` 
ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ,
ADD `configured_id` INTEGER NOT NULL AFTER `scheduler_configured_id` ;
ALTER TABLE scheduler_worker ADD statuses VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `scheduler_worker` ADD `last_status` DATETIME NOT NULL AFTER `statuses`;
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`scheduler_id` INTEGER,
  `scheduler_configured_id` INTEGER NOT NULL,
  `configured_id` INTEGER NOT NULL,
	`type` SMALLINT,
	`name` VARCHAR(20) default '',
	`description` VARCHAR(20) default '',
  `statuses` VARCHAR( 255 ) NOT NULL,
  `last_status` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `scheduler_status`;
CREATE TABLE `scheduler_status`
/*
ALTER TABLE `scheduler_status` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ;
ALTER TABLE `scheduler_status` ADD `worker_configured_id` INTEGER NOT NULL AFTER `worker_id` ;
ALTER TABLE scheduler_status ADD INDEX status_created_at_index (created_at);
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`scheduler_id` INTEGER,
  `scheduler_configured_id` INTEGER NOT NULL,
	`worker_id` INTEGER default null,
  `worker_configured_id` INTEGER NOT NULL,
	`worker_type` SMALLINT default null,
	`type` SMALLINT,
	`value` INTEGER,
	PRIMARY KEY (`id`),
	KEY `status_type_index`(`type`),
	KEY `scheduler_id_index`(`scheduler_id`),
	KEY `worker_id_index_type`(`worker_id`, `worker_type`),
  INDEX status_created_at_index (created_at)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `scheduler_config`;
CREATE TABLE `scheduler_config`
/*
ALTER TABLE `scheduler_config` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ;
ALTER TABLE `scheduler_config` ADD `worker_configured_id` INTEGER NOT NULL AFTER `worker_id` ;
ALTER TABLE scheduler_config ADD INDEX status_variable_index (variable, variable_part);
ALTER TABLE scheduler_config ADD INDEX status_created_at_index (created_at);
ALTER TABLE scheduler_config ADD INDEX scheduler_id_index (scheduler_id);
ALTER TABLE scheduler_config ADD INDEX worker_id_index_type (worker_id);
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`command_id` INTEGER default null,
	`command_status` TINYINT,
	`scheduler_id` INTEGER,
  `scheduler_configured_id` INTEGER NOT NULL,
	`scheduler_name` VARCHAR(20),
	`worker_id` INTEGER default null,
  `worker_configured_id` INTEGER NOT NULL,
	`worker_name` VARCHAR(50) default 'null',
	`variable` VARCHAR(100),
	`variable_part` VARCHAR(100),
	`value` VARCHAR(255),
	PRIMARY KEY (`id`),
  INDEX status_variable_index (variable, variable_part),
  INDEX status_created_at_index (created_at),
  INDEX scheduler_id_index (scheduler_id),
  INDEX worker_id_index_type (worker_id)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `control_panel_command`;
CREATE TABLE `control_panel_command`
/*
ALTER TABLE `control_panel_command` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ;
ALTER TABLE `control_panel_command` ADD `worker_configured_id` INTEGER NOT NULL AFTER `worker_id` ;
ALTER TABLE `control_panel_command` ADD `batch_index` INT NULL DEFAULT NULL AFTER `worker_name`;
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`created_by_id` INTEGER,
	`scheduler_id` INTEGER default null,
  `scheduler_configured_id` INTEGER NOT NULL,
	`worker_id` INTEGER default null,
  `worker_configured_id` INTEGER NOT NULL,
	`worker_name` VARCHAR(50) default 'null',
  `batch_index` INT NULL DEFAULT NULL,
	`type` SMALLINT,
	`target_type` SMALLINT,
	`status` SMALLINT,
	`cause` VARCHAR(255),
	`description` VARCHAR(255),
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `priority_group`;
CREATE TABLE `priority_group`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`name` VARCHAR(20),
	`description` VARCHAR(100),
	`priority` TINYINT,
	`bulk_priority` TINYINT,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

DROP TABLE IF EXISTS `work_group`;
CREATE TABLE `work_group`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`name` VARCHAR(20),
	`description` VARCHAR(100),
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

#updates_2009-10-28_media_info.sql
/*
CREATE TABLE `media_info`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`flavor_asset_id` INTEGER,
	`format` VARCHAR(6),
	`duration` INTEGER,
	`bit_rate` INTEGER,
	`row_data` VARCHAR(1023),
	`file_size` INTEGER default null,
	`width` INTEGER default null,
	`height` INTEGER default null,
	`dar_width` INTEGER default null,
	`dar_height` INTEGER default null,
	`frame_rate` INTEGER default null,
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
)ENGINE=MyISAM;

#updates_2009-10-29_media_info.sql
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
)ENGINE=MyISAM;
*/

#updates_2009-11-15_media_info_new_table.sql
DROP TABLE IF EXISTS `media_info`;
CREATE TABLE `media_info`
/*
ALTER TABLE media_info CHANGE flavor_asset_id flavor_asset_id VARCHAR( 20 ) NULL DEFAULT NULL; 
ALTER TABLE media_info CHANGE video_frame_rate video_frame_rate FLOAT; 
ALTER TABLE media_info ADD multi_stream_info VARCHAR( 1023 ) NULL DEFAULT NULL; 
ALTER TABLE `media_info` CHANGE `raw_data` `raw_data` TEXT;
ALTER TABLE `media_info` 
CHANGE  `container_format`  `container_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE  `video_format`  `video_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE  `audio_format`  `audio_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
CHANGE  `flavor_asset_id`  `flavor_asset_id` VARCHAR( 20 ) NULL DEFAULT NULL;
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`flavor_asset_id` VARCHAR( 20 ) NULL DEFAULT NULL,
	`file_size` INTEGER  NOT NULL,
	`container_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
	`container_id` VARCHAR(127),
	`container_profile` VARCHAR(127),
	`container_duration` INTEGER,
	`container_bit_rate` INTEGER,
	`video_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
	`video_codec_id` VARCHAR(127),
	`video_duration` INTEGER,
	`video_bit_rate` INTEGER,
	`video_bit_rate_mode` TINYINT,
	`video_width` INTEGER  NOT NULL,
	`video_height` INTEGER  NOT NULL,
	`video_frame_rate` FLOAT,
	`video_dar` FLOAT,
  `video_rotation` INT NOT NULL,
	`audio_format` VARCHAR( 127 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
	`audio_codec_id` VARCHAR(127),
	`audio_duration` INTEGER,
	`audio_bit_rate` INTEGER,
	`audio_bit_rate_mode` TINYINT,
	`audio_channels` TINYINT,
	`audio_sampling_rate` INTEGER,
	`audio_resolution` INTEGER,
	`writing_lib` VARCHAR(127),
	`custom_data` TEXT,
	`raw_data` TEXT,
  `multi_stream_info` VARCHAR( 1023 ) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `flavor_asset_id_index`(`flavor_asset_id`)
)ENGINE=MyISAM;

#updates_2009-10-29_bulk_upload_result.sql
DROP TABLE IF EXISTS `bulk_upload_result`;
CREATE TABLE `bulk_upload_result`
/*
ALTER TABLE `bulk_upload_result` 
ADD `conversion_profile_id` INT NOT NULL AFTER `content_type` ,
ADD `access_control_profile_id` INT NOT NULL AFTER `conversion_profile_id` ,
ADD `category` VARCHAR( 128 ) NOT NULL AFTER `access_control_profile_id` ,
ADD `schedule_start_date` DATETIME NOT NULL AFTER `category` ,
ADD `schedule_end_date` DATETIME NOT NULL AFTER `schedule_start_date` ,
ADD `thumbnail_url` VARCHAR( 255 ) NOT NULL AFTER `schedule_end_date` ,
ADD `thumbnail_saved` INT NOT NULL AFTER `thumbnail_url`,
ADD `partner_data` VARCHAR( 4096 ) NOT NULL AFTER `thumbnail_saved`;
ALTER TABLE `bulk_upload_result` ADD `entry_status` INT NOT NULL AFTER `entry_id`;
ALTER TABLE `bulk_upload_result` CHANGE `entry_id` `entry_id` varchar(20);
ALTER TABLE `bulk_upload_result` CHANGE `schedule_start_date` `schedule_start_date` DATETIME DEFAULT NULL;
ALTER TABLE `bulk_upload_result` CHANGE `schedule_end_date` `schedule_end_date` DATETIME DEFAULT NULL;
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`bulk_upload_job_id` INTEGER,
	`line_index` INTEGER,
	`partner_id` INTEGER,
	`entry_id` varchar(20),
  `entry_status` INT NOT NULL,
	`row_data` VARCHAR(1023),
	`title` VARCHAR(127),
	`description` VARCHAR(255),
	`tags` VARCHAR(255),
	`url` VARCHAR(255),
	`content_type` VARCHAR(31),
  `conversion_profile_id` INT NOT NULL,
  `access_control_profile_id` INT NOT NULL,
  `category` VARCHAR( 128 ) NOT NULL,
  `schedule_start_date` DATETIME DEFAULT NULL,
  `schedule_end_date` DATETIME DEFAULT NULL,
  `thumbnail_url` VARCHAR( 255 ) NOT NULL,
  `thumbnail_saved` INT NOT NULL,
  `partner_data` VARCHAR( 4096 ) NOT NULL,
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `entry_id_index_id`(`entry_id`, `id`)
)ENGINE=MyISAM;

#updates_2009-11-05_syndicationFeed_table.sql
DROP TABLE IF EXISTS `syndication_feed`;
CREATE TABLE `syndication_feed`
#updates_2009-11-22_syndicationFeed_add_feedAuthor.sql
#alter table syndication_feed add column `feed_author` VARCHAR(50) after feed_image_url;
(
	`id` VARCHAR(20)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`playlist_id` VARCHAR(20),
	`name` VARCHAR(128) default '' NOT NULL,
	`status` TINYINT,
	`type` TINYINT,
	`landing_page` VARCHAR(512) default '' NOT NULL,
	`flavor_param_id` INTEGER,
	`player_uiconf_id` INTEGER,
	`allow_embed` INTEGER default 1,
	`adult_content` VARCHAR(10),
	`transcode_existing_content` INTEGER default 0,
	`add_to_default_conversion_profile` INTEGER default 0,
	`categories` VARCHAR(1024),
	`feed_description` VARCHAR(1024),
	`language` VARCHAR(5),
	`feed_landing_page` VARCHAR(512),
	`owner_name` VARCHAR(50),
	`owner_email` VARCHAR(128),
	`feed_image_url` VARCHAR(512),
  `feed_author` VARCHAR(50),
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `int_id_index`(`int_id`)
)ENGINE=MyISAM;

#updates_2009-11-15_flavor_params_output.sql
DROP TABLE IF EXISTS `flavor_params_output`;
CREATE TABLE `flavor_params_output`
/*
ALTER TABLE flavor_params_output DROP conversion_engine;
ALTER TABLE flavor_params_output DROP conversion_engine_extra_params;
ALTER TABLE flavor_params_output ADD conversion_engines VARCHAR(1024) AFTER gop_size;
ALTER TABLE flavor_params_output ADD conversion_engines_extra_params VARCHAR(1024) AFTER conversion_engines;
ALTER TABLE flavor_params_output ADD `audio_resolution` INTEGER default 0 AFTER `audio_sample_rate`;
ALTER TABLE flavor_params_output CHANGE frame_rate frame_rate FLOAT default 0 NOT NULL; 
ALTER TABLE `flavor_params_output` ADD `two_pass` INTEGER default 0 NOT NULL AFTER `gop_size`;
ALTER TABLE `flavor_params_output` CHANGE `frame_rate` `frame_rate` FLOAT NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_codec` `audio_codec` VARCHAR(20) NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_bitrate` `audio_bitrate` INTEGER NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_channels` `audio_channels` TINYINT NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_sample_rate` `audio_sample_rate` INTEGER NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_resolution` `audio_resolution` INTEGER NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` ADD `command_lines` VARCHAR(2047) default NULL AFTER `custom_data`;
ALTER TABLE  `flavor_params_output` ADD  `file_ext` VARCHAR( 4 ) NULL DEFAULT NULL AFTER  `command_lines`;
ALTER TABLE  `flavor_params_output` ADD  `flavor_asset_version` VARCHAR( 20 ) NOT NULL AFTER  `flavor_asset_id`;
*/
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`flavor_params_id` INTEGER  NOT NULL,
	`flavor_params_version` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`entry_id` VARCHAR(20)  NOT NULL,
	`flavor_asset_id` VARCHAR(20)  NOT NULL,
  `flavor_asset_version` VARCHAR( 20 ) NOT NULL,  
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
	`audio_codec` VARCHAR(20) NULL DEFAULT NULL,
	`audio_bitrate` INTEGER NULL DEFAULT NULL,
	`audio_channels` TINYINT NULL DEFAULT NULL,
	`audio_sample_rate` INTEGER NULL DEFAULT NULL,
  `audio_resolution` INTEGER NULL DEFAULT NULL,
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`frame_rate` FLOAT NULL DEFAULT NULL,
	`gop_size` INTEGER default 0 NOT NULL,
#	`conversion_engine` VARCHAR(1024),
#	`conversion_engine_extra_params` VARCHAR(1024),
  `two_pass` INTEGER default 0 NOT NULL,
  `conversion_engines` VARCHAR(1024),
  `conversion_engines_extra_params` VARCHAR(1024),
  `custom_data` TEXT,
  `command_lines` VARCHAR(2047) default NULL,
  `file_ext` VARCHAR( 4 ) NULL DEFAULT NULL,
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
)ENGINE=MyISAM;

/*
#updates_2009-12-28_flavor_params_and_asset.sql
CREATE TABLE `system_user`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(50)  NOT NULL,
	`first_name` VARCHAR(40)  NOT NULL,
	`last_name` VARCHAR(40)  NOT NULL,
	`sha1_password` VARCHAR(40)  NOT NULL,
	`salt` VARCHAR(32)  NOT NULL,
	`created_by` INTEGER,
	`status` TINYINT  NOT NULL,
	`status_updated_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;
*/
#updates_2009-12-10_system_user.sql
DROP TABLE IF EXISTS  `system_user`;
CREATE TABLE `system_user`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(50)  NOT NULL,
	`first_name` VARCHAR(40)  NOT NULL,
	`last_name` VARCHAR(40)  NOT NULL,
	`sha1_password` VARCHAR(40)  NOT NULL,
	`salt` VARCHAR(32)  NOT NULL,
	`created_by` INTEGER,
	`status` TINYINT  NOT NULL,
	`status_updated_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `system_user_email_unique` (`email`)
)ENGINE=MyISAM;



#-----------------------------------------------------------------------------
#-- track_entry
#-----------------------------------------------------------------------------
DROP TABLE IF EXISTS `track_entry`;
CREATE TABLE `track_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`track_event_type_id` SMALLINT,
	`ps_version` VARCHAR(10),
	`context` VARCHAR(511),
	`partner_id` INTEGER,
	`entry_id` VARCHAR(20),
	`host_name` VARCHAR(20),
	`uid` VARCHAR(63),
	`track_event_status_id` SMALLINT,
	`changed_properties` VARCHAR(1023),
	`param_1_str` VARCHAR(255),
	`param_2_str` VARCHAR(511),
	`param_3_str` VARCHAR(511),
	`ks` VARCHAR(511),
	`description` VARCHAR(127),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`user_ip` VARCHAR(20),
	PRIMARY KEY (`id`),
	KEY `partner_event_type_indx`(`partner_id`,`track_event_type_id`),
	KEY `entry_id_indx`(`entry_id`),
	KEY `track_event_type_id_indx`(`track_event_type_id`),
	KEY `param_1_indx`(`param_1_str`)
)Type=MyISAM;