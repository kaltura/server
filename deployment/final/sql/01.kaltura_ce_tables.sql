
DROP TABLE IF EXISTS `access_control`;

CREATE TABLE `access_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(1024) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_restrict_type` tinyint(4) DEFAULT NULL,
  `site_restrict_list` varchar(1024) DEFAULT NULL,
  `country_restrict_type` tinyint(4) DEFAULT NULL,
  `country_restrict_list` varchar(1024) DEFAULT NULL,
  `ks_restrict_privilege` varchar(20) DEFAULT NULL,
  `prv_restrict_privilege` varchar(20) DEFAULT NULL,
  `prv_restrict_length` int(11) DEFAULT NULL,
  `kdir_restrict_type` tinyint(4) DEFAULT NULL,
  `custom_data` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `admin_permission`;

CREATE TABLE `admin_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groups` varchar(512) DEFAULT NULL,
  `admin_kuser_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_permission_FI_1` (`admin_kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `alert`;

CREATE TABLE `alert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kuser_id` int(11) DEFAULT NULL,
  `alert_type` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `rule_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subject_index` (`alert_type`,`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `annotation`;

CREATE TABLE `annotation` (
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(255) NOT NULL,
  `parent_id` varchar(255) DEFAULT NULL,
  `entry_id` varchar(31) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` text,
  `tags` varchar(255) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `kuser_id` int(11) DEFAULT NULL,
  `custom_data` text,
  `partner_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_entry_index` (`partner_id`,`entry_id`),
  KEY `int_id_index` (`int_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `audit_trail`;

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `parsed_at` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `object_type` varchar(31) DEFAULT NULL,
  `object_id` varchar(31) DEFAULT NULL,
  `related_object_id` varchar(31) DEFAULT NULL,
  `related_object_type` varchar(31) DEFAULT NULL,
  `entry_id` varchar(31) DEFAULT NULL,
  `master_partner_id` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `request_id` varchar(31) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `action` varchar(31) DEFAULT NULL,
  `data` text,
  `ks` varchar(511) DEFAULT NULL,
  `context` tinyint(4) DEFAULT NULL,
  `entry_point` varchar(127) DEFAULT NULL,
  `server_name` varchar(63) DEFAULT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `user_agent` varchar(127) DEFAULT NULL,
  `client_tag` VARCHAR(127),
  `description` varchar(1023) DEFAULT NULL,
  `error_description` varchar(1023) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_index` (`object_type`,`object_id`),
  KEY `partner_entry_index` (`partner_id`,`entry_id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `status_index` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `audit_trail_config`;

CREATE TABLE `audit_trail_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `object_type` varchar(31) DEFAULT NULL,
  `descriptors` varchar(1023) DEFAULT NULL,
  `actions` varchar(1023) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_object_index` (`partner_id`,`object_type`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `audit_trail_data`;

CREATE TABLE `audit_trail_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `audit_trail_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `object_type` varchar(31) DEFAULT NULL,
  `object_id` varchar(31) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `action` varchar(31) DEFAULT NULL,
  `descriptor` varchar(127) DEFAULT NULL,
  `old_value` varchar(511) DEFAULT NULL,
  `new_value` varchar(511) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_index` (`object_type`,`object_id`),
  KEY `partner_index` (`partner_id`),
  KEY `audit_trail_index` (`audit_trail_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `batch_job`;

CREATE TABLE `batch_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_type` smallint(6) DEFAULT NULL,
  `job_sub_type` smallint(6) DEFAULT NULL,
  `data` varchar(8192) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `duplication_key` varchar(41) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `abort` tinyint(4) DEFAULT NULL,
  `check_again_timeout` int(11) DEFAULT NULL,
  `progress` tinyint(4) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `updates_count` smallint(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) NOT NULL,
  `work_group_id` int(11) NOT NULL,
  `queue_time` datetime DEFAULT NULL,
  `finish_time` datetime DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `subp_id` int(11) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `last_scheduler_id` int(11) DEFAULT NULL,
  `last_worker_id` int(11) DEFAULT NULL,
  `last_worker_remote` int(11) DEFAULT '0',
  `processor_name` varchar(64) DEFAULT NULL,
  `processor_expiration` datetime DEFAULT NULL,
  `parent_job_id` int(11) DEFAULT NULL,
  `processor_location` varchar(64) DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `lock_version` int(11) DEFAULT NULL,
  `twin_job_id` int(11) DEFAULT NULL,
  `bulk_job_id` int(11) DEFAULT NULL,
  `root_job_id` int(11) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `err_type` int(11) NOT NULL DEFAULT '0',
  `err_number` int(11) NOT NULL DEFAULT '0',
  `on_stress_divert_to` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entry_id_index_id` (`entry_id`,`id`),
  KEY `status_job_type_index` (`status`,`job_type`),
  KEY `created_at_job_type_status_index` (`created_at`,`job_type`,`status`),
  KEY `partner_type_index` (`partner_id`,`job_type`),
  KEY `partner_id_index` (`partner_id`),
  KEY `work_group_id_index_priority` (`work_group_id`,`priority`),
  KEY `twin_job_id_index` (`twin_job_id`),
  KEY `bulk_job_id_index` (`bulk_job_id`),
  KEY `root_job_id_index` (`root_job_id`),
  KEY `parent_job_id_index` (`parent_job_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bb_forum`;

CREATE TABLE `bb_forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `post_count` int(11) DEFAULT '0',
  `thread_count` int(11) DEFAULT '0',
  `last_post` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_live` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `bb_forum_FI_1` (`last_post`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bb_post`;

CREATE TABLE `bb_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `forum_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `node_level` int(11) DEFAULT NULL,
  `node_id` varchar(64) DEFAULT NULL,
  `num_childern` int(11) DEFAULT '0',
  `last_child` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bb_post_FI_1` (`kuser_id`),
  KEY `bb_post_FI_2` (`forum_id`),
  KEY `bb_post_FI_3` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `blocked_email`;

CREATE TABLE `blocked_email` (
  `email` varchar(40) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bulk_upload_result`;

CREATE TABLE `bulk_upload_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `bulk_upload_job_id` int(11) DEFAULT NULL,
  `line_index` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `entry_status` int(11) NOT NULL,
  `row_data` varchar(1023) DEFAULT NULL,
  `title` varchar(127) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `content_type` varchar(31) DEFAULT NULL,
  `conversion_profile_id` int(11) NOT NULL,
  `access_control_profile_id` int(11) NOT NULL,
  `category` varchar(128) NOT NULL,
  `schedule_start_date` datetime DEFAULT NULL,
  `schedule_end_date` datetime DEFAULT NULL,
  `thumbnail_url` varchar(255) NOT NULL,
  `thumbnail_saved` int(11) NOT NULL,
  `partner_data` varchar(4096) NOT NULL,
  `error_description` varchar(255) DEFAULT NULL,
  `plugins_data` varchar(9182) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id_index_id` (`entry_id`,`id`),
  KEY `job_id_line_index` (`bulk_upload_job_id`,`line_index`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `depth` tinyint(4) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `full_name` varchar(490) NOT NULL DEFAULT '',
  `entries_count` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `full_name_index` (`full_name`(333))
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kuser_id` int(11) DEFAULT NULL,
  `comment_type` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `base_date` date DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `comment` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_created_index` (`comment_type`,`subject_id`,`base_date`,`reply_to`,`created_at`),
  KEY `comment_FI_1` (`kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `control_panel_command`;

CREATE TABLE `control_panel_command` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(127) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `worker_configured_id` int(11) DEFAULT NULL,
  `worker_name` varchar(127) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `target_type` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `cause` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `error_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `conversion`;

CREATE TABLE `conversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` varchar(20) DEFAULT NULL,
  `in_file_name` varchar(128) DEFAULT NULL,
  `in_file_ext` varchar(16) DEFAULT NULL,
  `in_file_size` int(11) DEFAULT NULL,
  `source` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `conversion_params` varchar(512) DEFAULT NULL,
  `out_file_name` varchar(128) DEFAULT NULL,
  `out_file_size` int(11) DEFAULT NULL,
  `out_file_name_2` varchar(128) DEFAULT NULL,
  `out_file_size_2` int(11) DEFAULT NULL,
  `conversion_time` int(11) DEFAULT NULL,
  `total_process_time` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id_index` (`entry_id`),
  KEY `id_status_index` (`id`,`status`),
  KEY `created_at_status_index` (`created_at`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `conversion_params`;

CREATE TABLE `conversion_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `enabled` tinyint(4) DEFAULT '1',
  `name` varchar(128) DEFAULT NULL,
  `profile_type` varchar(128) DEFAULT NULL,
  `profile_type_index` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `aspect_ratio` varchar(6) DEFAULT NULL,
  `gop_size` int(11) DEFAULT NULL,
  `bitrate` int(11) DEFAULT NULL,
  `qscale` int(11) DEFAULT NULL,
  `file_suffix` varchar(64) DEFAULT NULL,
  `custom_data` varchar(4096) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `conversion_profile`;

CREATE TABLE `conversion_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT '0',
  `enabled` tinyint(4) DEFAULT '1',
  `name` varchar(128) DEFAULT NULL,
  `profile_type` varchar(128) DEFAULT NULL,
  `commercial_transcoder` tinyint(4) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `aspect_ratio` varchar(6) DEFAULT NULL,
  `bypass_flv` tinyint(4) DEFAULT NULL,
  `use_with_bulk` tinyint(4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `profile_type_suffix` varchar(32) DEFAULT NULL,
  `bypass_by_extension` varchar(32) DEFAULT NULL,
  `conversion_profile_2_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `conversion_profile_2`;

CREATE TABLE `conversion_profile_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(1024) NOT NULL DEFAULT '',
  `crop_left` int(11) NOT NULL DEFAULT '-1',
  `crop_top` int(11) NOT NULL DEFAULT '-1',
  `crop_width` int(11) NOT NULL DEFAULT '-1',
  `crop_height` int(11) NOT NULL DEFAULT '-1',
  `clip_start` int(11) NOT NULL DEFAULT '-1',
  `clip_duration` int(11) NOT NULL DEFAULT '-1',
  `input_tags_map` varchar(1023) DEFAULT NULL,
  `creation_mode` smallint(6) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `distribution_profile`;

CREATE TABLE `distribution_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `provider_type` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `submit_enabled` tinyint(4) DEFAULT NULL,
  `update_enabled` tinyint(4) DEFAULT NULL,
  `delete_enabled` tinyint(4) DEFAULT NULL,
  `report_enabled` tinyint(4) DEFAULT NULL,
  `auto_create_flavors` varchar(255) DEFAULT NULL,
  `auto_create_thumb` varchar(255) DEFAULT NULL,
  `optional_flavor_params_ids` varchar(127) DEFAULT NULL,
  `required_flavor_params_ids` varchar(127) DEFAULT NULL,
  `optional_thumb_dimensions` varchar(2048) DEFAULT NULL,
  `required_thumb_dimensions` varchar(2048) DEFAULT NULL,
  `report_interval` int(11) NOT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`),
  KEY `partner_status` (`partner_id`,`status`),
  KEY `partner_status_provider` (`partner_id`,`status`,`provider_type`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dynamic_enum`;

CREATE TABLE `dynamic_enum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enum_name` varchar(255) NOT NULL,
  `value_name` varchar(255) NOT NULL,
  `plugin_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enum_unique` (`enum_name`,`value_name`,`plugin_name`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `email_campaign`;

CREATE TABLE `email_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `criteria_id` smallint(6) DEFAULT NULL,
  `criteria_str` varchar(1024) DEFAULT NULL,
  `criteria_params` varchar(1024) DEFAULT NULL,
  `template_path` varchar(256) DEFAULT NULL,
  `campaign_mgr_kuser_id` int(11) DEFAULT NULL,
  `send_count` int(11) DEFAULT NULL,
  `open_count` int(11) DEFAULT NULL,
  `click_count` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_mgr_kuser_id_index` (`campaign_mgr_kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `email_ingestion_profile`;

CREATE TABLE `email_ingestion_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` text,
  `email_address` varchar(50) NOT NULL,
  `mailbox_id` varchar(50) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `conversion_profile_2_id` int(11) DEFAULT NULL,
  `moderation_status` tinyint(4) DEFAULT NULL,
  `custom_data` text,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_ingestion_profile_email_address_unique` (`email_address`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `entry`;

CREATE TABLE `entry` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `kshow_id` varchar(20) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `media_type` smallint(6) DEFAULT NULL,
  `data` varchar(48) DEFAULT NULL,
  `thumbnail` varchar(48) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `votes` int(11) DEFAULT '0',
  `comments` int(11) DEFAULT '0',
  `favorites` int(11) DEFAULT '0',
  `total_rank` int(11) DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `tags` text,
  `anonymous` tinyint(4) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `source` smallint(6) DEFAULT NULL,
  `source_id` varchar(48) DEFAULT NULL,
  `source_link` varchar(1024) DEFAULT NULL,
  `license_type` smallint(6) DEFAULT NULL,
  `credit` varchar(1024) DEFAULT NULL,
  `length_in_msecs` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `display_in_search` tinyint(4) DEFAULT '1',
  `subp_id` int(11) DEFAULT '0',
  `custom_data` text,
  `search_text` varchar(4096) DEFAULT NULL,
  `screen_name` varchar(20) DEFAULT NULL,
  `site_url` varchar(256) DEFAULT NULL,
  `permissions` int(11) DEFAULT NULL,
  `group_id` varchar(64) DEFAULT NULL,
  `plays` int(11) DEFAULT '0',
  `partner_data` varchar(4096) DEFAULT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `indexed_custom_data_1` int(11) DEFAULT NULL,
  `description` text,
  `media_date` datetime DEFAULT NULL,
  `admin_tags` text,
  `moderation_status` int(11) DEFAULT '2',
  `moderation_count` int(11) DEFAULT '0',
  `modified_at` datetime DEFAULT NULL,
  `puser_id` varchar(64) DEFAULT NULL,
  `access_control_id` int(11) DEFAULT NULL,
  `conversion_profile_id` int(11) DEFAULT NULL,
  `categories` varchar(4096) DEFAULT NULL,
  `categories_ids` varchar(1024) DEFAULT NULL,
  `search_text_discrete` varchar(4096) DEFAULT NULL,
  `flavor_params_ids` varchar(512) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `available_from` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kshow_created_index` (`kshow_id`,`created_at`),
  KEY `int_id_index` (`int_id`),
  KEY `entry_FI_2` (`kuser_id`),
  KEY `kshow_index` (`partner_id`,`kshow_id`,`subp_id`),
  KEY `kshow_index_2` (`partner_id`,`kshow_id`,`status`,`subp_id`),
  KEY `partner_created_at_status_type_index` (`partner_id`,`created_at`,`status`,`type`),
  KEY `status_created_index` (`status`,`created_at`),
  KEY `type_status_created_index` (`type`,`status`,`created_at`),
  KEY `created_at_index` (`created_at`),
  KEY `partner_group_index` (`partner_id`,`group_id`),
  KEY `partner_kuser_indexed_custom_data_index` (`partner_id`,`kuser_id`,`indexed_custom_data_1`),
  KEY `partner_status_index` (`partner_id`,`status`),
  KEY `partner_moderation_status` (`partner_id`,`moderation_status`),
  KEY `partner_modified_at_index` (`partner_id`,`modified_at`),
  KEY `partner_status_media_type_index` (`partner_id`,`status`,`media_type`),
  KEY `modified_at_index` (`modified_at`),
  KEY `updated_at_index` (`updated_at`),
  KEY `entry_FI_3` (`access_control_id`),
  KEY `entry_FI_5` (`conversion_profile_id`),
  FULLTEXT KEY `search_text_index` (`search_text`),
  FULLTEXT KEY `search_text_discrete_index` (`search_text_discrete`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `entry_distribution`;

CREATE TABLE `entry_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `distribution_profile_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `dirty_status` tinyint(4) DEFAULT NULL,
  `thumb_asset_ids` varchar(255) DEFAULT NULL,
  `flavor_asset_ids` varchar(255) DEFAULT NULL,
  `sunrise` datetime DEFAULT NULL,
  `sunset` datetime DEFAULT NULL,
  `remote_id` varchar(31) DEFAULT NULL,
  `plays` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `validation_errors` text,
  `error_type` int(11) DEFAULT NULL,
  `error_number` int(11) DEFAULT NULL,
  `error_description` varchar(255) DEFAULT NULL,
  `last_report` datetime NOT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_entry_profile` (`partner_id`,`entry_id`,`distribution_profile_id`),
  KEY `partner_profile_status` (`partner_id`,`distribution_profile_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `facebook_invite`;

CREATE TABLE `facebook_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `puser_id` varchar(64) DEFAULT NULL,
  `invited_puser_id` varchar(64) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `puser_id_index` (`puser_id`),
  KEY `invited_puser_id_index` (`invited_puser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `favorite`;

CREATE TABLE `favorite` (
  `kuser_id` int(11) DEFAULT NULL,
  `subject_type` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `privacy` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subject_index` (`subject_type`,`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `file_sync`;

CREATE TABLE `file_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `object_type` int(4) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `object_sub_type` tinyint(4) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `original` tinyint(4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ready_at` datetime DEFAULT NULL,
  `sync_time` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `file_type` tinyint(4) DEFAULT NULL,
  `linked_id` int(11) DEFAULT NULL,
  `link_count` int(11) DEFAULT NULL,
  `file_root` varchar(64) DEFAULT NULL,
  `file_path` varchar(128) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`object_type`,`object_id`,`version`,`object_sub_type`,`dc`),
  KEY `object_id_object_type_version_subtype_index` (`object_id`,`object_type`,`version`,`object_sub_type`),
  KEY `partner_id_object_id_object_type_index` (`partner_id`,`object_id`,`object_type`),
  KEY `dc_status_index` (`dc`,`status`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `flag`;

CREATE TABLE `flag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kuser_id` int(11) DEFAULT NULL,
  `subject_type` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `flag_type` int(11) DEFAULT NULL,
  `other` varchar(60) DEFAULT NULL,
  `comment` varchar(2048) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_created_index` (`subject_type`,`subject_id`,`created_at`),
  KEY `flag_FI_1` (`kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `flavor_asset`;

CREATE TABLE `flavor_asset` (
  `id` varchar(20) NOT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `tags` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `entry_id` varchar(20) NOT NULL,
  `flavor_params_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `bitrate` int(11) NOT NULL DEFAULT '0',
  `frame_rate` float NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `is_original` int(11) NOT NULL DEFAULT '0',
  `file_ext` varchar(4) DEFAULT NULL,
  `container_format` varchar(127) DEFAULT NULL,
  `video_codec_id` varchar(127) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `custom_data` text,
  PRIMARY KEY (`int_id`),
  KEY `partner_id_entry_id` (`partner_id`,`entry_id`),
  KEY `flavor_asset_FI_1` (`entry_id`),
  KEY `flavor_asset_FI_2` (`flavor_params_id`),
  KEY `id_indx` (`id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `flavor_params`;

CREATE TABLE `flavor_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL DEFAULT '0',
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `tags` text,
  `description` varchar(1024) NOT NULL DEFAULT '',
  `ready_behavior` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  `format` varchar(20) NOT NULL,
  `video_codec` varchar(20) NOT NULL,
  `video_bitrate` int(11) NOT NULL DEFAULT '0',
  `audio_codec` varchar(20) NOT NULL,
  `audio_bitrate` int(11) NOT NULL DEFAULT '0',
  `audio_channels` tinyint(4) NOT NULL DEFAULT '0',
  `audio_sample_rate` int(11) DEFAULT '0',
  `audio_resolution` int(11) DEFAULT '0',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `frame_rate` float NOT NULL DEFAULT '0',
  `gop_size` int(11) NOT NULL DEFAULT '0',
  `two_pass` int(11) NOT NULL DEFAULT '0',
  `conversion_engines` varchar(1024) DEFAULT NULL,
  `conversion_engines_extra_params` varchar(1024) DEFAULT NULL,
  `custom_data` text,
  `view_order` int(11) DEFAULT '0',
  `bypass_by_extension` varchar(32) DEFAULT NULL,
  `creation_mode` smallint(6) DEFAULT '1',
  `deinterlice` int(11) NOT NULL,
  `rotate` int(11) NOT NULL,
  `operators` text,
  `engine_version` smallint(6) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `flavor_params_conversion_profile`;

CREATE TABLE `flavor_params_conversion_profile` (
  `conversion_profile_id` int(11) NOT NULL,
  `flavor_params_id` int(11) NOT NULL,
  `ready_behavior` tinyint(4) NOT NULL,
  `force_none_complied` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `flavor_params_conversion_profile_FI_1` (`conversion_profile_id`),
  KEY `flavor_params_conversion_profile_FI_2` (`flavor_params_id`),
  KEY `updated_at_FI_3` (`updated_at`)
) ENGINE=MyISAM ;


DROP TABLE IF EXISTS `flavor_params_output`;

CREATE TABLE `flavor_params_output` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flavor_params_id` int(11) NOT NULL,
  `flavor_params_version` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `entry_id` varchar(20) NOT NULL,
  `flavor_asset_id` varchar(20) NOT NULL,
  `flavor_asset_version` varchar(20) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `tags` text,
  `description` varchar(1024) NOT NULL DEFAULT '',
  `ready_behavior` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  `format` varchar(20) NOT NULL,
  `video_codec` varchar(20) NOT NULL,
  `video_bitrate` int(11) NOT NULL DEFAULT '0',
  `audio_codec` varchar(20) DEFAULT NULL,
  `audio_bitrate` int(11) DEFAULT NULL,
  `audio_channels` tinyint(4) DEFAULT NULL,
  `audio_sample_rate` int(11) DEFAULT NULL,
  `audio_resolution` int(11) DEFAULT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `frame_rate` float DEFAULT NULL,
  `gop_size` int(11) NOT NULL DEFAULT '0',
  `two_pass` int(11) NOT NULL DEFAULT '0',
  `conversion_engines` varchar(1024) DEFAULT NULL,
  `conversion_engines_extra_params` varchar(1024) DEFAULT NULL,
  `custom_data` text,
  `command_lines` varchar(2047) DEFAULT NULL,
  `file_ext` varchar(4) DEFAULT NULL,
  `deinterlice` int(11) NOT NULL,
  `rotate` int(11) NOT NULL,
  `operators` text,
  `engine_version` smallint(6) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `flavor_params_output_FI_1` (`flavor_params_id`),
  KEY `flavor_params_output_FI_2` (`entry_id`),
  KEY `flavor_params_output_FI_3` (`flavor_asset_id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `flickr_token`;

CREATE TABLE `flickr_token` (
  `kalt_token` varchar(256) NOT NULL,
  `frob` varchar(64) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `nsid` varchar(64) DEFAULT NULL,
  `response` varchar(512) DEFAULT NULL,
  `is_valid` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`kalt_token`),
  KEY `is_valid_index` (`is_valid`,`kalt_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `generic_distribution_provider`;

CREATE TABLE `generic_distribution_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `is_default` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `name` varchar(127) DEFAULT NULL,
  `optional_flavor_params_ids` varchar(127) DEFAULT NULL,
  `required_flavor_params_ids` varchar(127) DEFAULT NULL,
  `optional_thumb_dimensions` varchar(2048) DEFAULT NULL,
  `required_thumb_dimensions` varchar(2048) DEFAULT NULL,
  `editable_fields` varchar(255) DEFAULT NULL,
  `mandatory_fields` varchar(255) DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_and_defaults` (`partner_id`,`is_default`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `generic_distribution_provider_action`;

CREATE TABLE `generic_distribution_provider_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `generic_distribution_provider_id` int(11) DEFAULT NULL,
  `action` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `results_parser` tinyint(4) DEFAULT NULL,
  `protocol` int(11) DEFAULT NULL,
  `server_address` varchar(255) DEFAULT NULL,
  `remote_path` varchar(255) DEFAULT NULL,
  `remote_username` varchar(127) DEFAULT NULL,
  `remote_password` varchar(127) DEFAULT NULL,
  `editable_fields` varchar(255) DEFAULT NULL,
  `mandatory_fields` varchar(255) DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `generic_distribution_provider_id` (`generic_distribution_provider_id`),
  KEY `generic_distribution_provider_status` (`generic_distribution_provider_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `invalid_session`;

CREATE TABLE `invalid_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ks` varchar(300) DEFAULT NULL,
  `ks_valid_until` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ks_index` (`ks`),
  KEY `ks_valid_until_index` (`ks_valid_until`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `kce_installation_error`;

CREATE TABLE `kce_installation_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `server_ip` varchar(20) DEFAULT NULL,
  `server_os` varchar(100) DEFAULT NULL,
  `php_version` varchar(20) DEFAULT NULL,
  `ce_admin_email` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `server_os_index` (`server_os`),
  KEY `php_version_index` (`php_version`),
  KEY `type_index` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `keyword`;

CREATE TABLE `keyword` (
  `word` varchar(30) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_type` int(11) DEFAULT NULL,
  `entity_columns` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`word`),
  KEY `word_index` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `kshow`;

CREATE TABLE `kshow` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `producer_id` int(11) DEFAULT NULL,
  `episode_id` varchar(20) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `subdomain` varchar(30) DEFAULT NULL,
  `description` text,
  `status` int(11) DEFAULT '0',
  `type` int(11) DEFAULT NULL,
  `media_type` int(11) DEFAULT NULL,
  `format_type` int(11) DEFAULT NULL,
  `language` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `skin` text,
  `thumbnail` varchar(48) DEFAULT NULL,
  `show_entry_id` varchar(20) DEFAULT NULL,
  `intro_id` varchar(10) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `votes` int(11) DEFAULT '0',
  `comments` int(11) DEFAULT '0',
  `favorites` int(11) DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `entries` int(11) DEFAULT '0',
  `contributors` int(11) DEFAULT '0',
  `subscribers` int(11) DEFAULT '0',
  `number_of_updates` int(11) DEFAULT '0',
  `tags` text,
  `custom_data` text,
  `indexed_custom_data_1` int(11) DEFAULT NULL,
  `indexed_custom_data_2` int(11) DEFAULT NULL,
  `indexed_custom_data_3` varchar(256) DEFAULT NULL,
  `reoccurence` int(11) DEFAULT NULL,
  `license_type` int(11) DEFAULT NULL,
  `length_in_msecs` int(11) DEFAULT '0',
  `view_permissions` int(11) DEFAULT NULL,
  `view_password` varchar(40) DEFAULT NULL,
  `contrib_permissions` int(11) DEFAULT NULL,
  `contrib_password` varchar(40) DEFAULT NULL,
  `edit_permissions` int(11) DEFAULT NULL,
  `edit_password` varchar(40) DEFAULT NULL,
  `salt` varchar(32) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `display_in_search` tinyint(4) DEFAULT '1',
  `subp_id` int(11) DEFAULT '0',
  `search_text` varchar(4096) DEFAULT NULL,
  `permissions` varchar(1024) DEFAULT NULL,
  `group_id` varchar(64) DEFAULT NULL,
  `plays` int(11) DEFAULT '0',
  `partner_data` varchar(4096) DEFAULT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `views_index` (`views`),
  KEY `votes_index` (`votes`),
  KEY `created_at_index` (`created_at`),
  KEY `type_index` (`type`),
  KEY `kshow_FI_1` (`producer_id`),
  KEY `indexed_custom_data_1_index` (`indexed_custom_data_1`),
  KEY `indexed_custom_data_2_index` (`indexed_custom_data_2`),
  KEY `indexed_custom_data_3_index` (`indexed_custom_data_3`),
  KEY `partner_id_subp_index` (`partner_id`,`id`,`subp_id`),
  KEY `partner_created_at_indes` (`partner_id`,`created_at`),
  KEY `created_index` (`created_at`),
  KEY `producer_updated_index` (`producer_id`,`updated_at`),
  KEY `producer_updated_id_index` (`producer_id`,`updated_at`,`id`),
  KEY `partner_subp_entries_index` (`partner_id`,`subp_id`,`entries`),
  KEY `partner_group_index` (`partner_id`,`group_id`),
  KEY `int_id_index` (`int_id`),
  FULLTEXT KEY `search_text_index` (`search_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `kshow_kuser`;

CREATE TABLE `kshow_kuser` (
  `kshow_id` varchar(20) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `subscription_type` int(11) DEFAULT NULL,
  `alert_type` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `kshow_index` (`kshow_id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subscription_index` (`kshow_id`,`subscription_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `kuser`;

CREATE TABLE `kuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_data_id` int(11) DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT NULL,
  `screen_name` varchar(127) CHARACTER SET latin1 DEFAULT NULL,
  `first_name` varchar(40) DEFAULT NULL,
  `last_name` varchar(40) DEFAULT NULL,
  `full_name` varchar(40) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sha1_password` varchar(40) DEFAULT NULL,
  `salt` varchar(32) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `state` varchar(16) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `url_list` varchar(256) DEFAULT NULL,
  `picture` varchar(48) DEFAULT NULL,
  `icon` tinyint(4) DEFAULT NULL,
  `about_me` varchar(4096) DEFAULT NULL,
  `tags` text,
  `tagline` varchar(256) DEFAULT NULL,
  `network_highschool` varchar(30) DEFAULT NULL,
  `network_college` varchar(30) DEFAULT NULL,
  `network_other` varchar(30) DEFAULT NULL,
  `mobile_num` varchar(16) DEFAULT NULL,
  `mature_content` tinyint(4) DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `registration_ip` int(11) DEFAULT NULL,
  `registration_cookie` varchar(256) DEFAULT NULL,
  `im_list` varchar(256) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `fans` int(11) DEFAULT '0',
  `entries` int(11) DEFAULT '0',
  `produced_kshows` int(11) DEFAULT '0',
  `status` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `display_in_search` tinyint(4) DEFAULT '1',
  `search_text` varchar(4096) DEFAULT NULL,
  `partner_data` varchar(4096) DEFAULT NULL,
  `storage_size` int(11) DEFAULT '0',
  `puser_id` varchar(100) DEFAULT NULL,
  `admin_tags` text,
  `indexed_partner_data_int` int(11) DEFAULT NULL,
  `indexed_partner_data_string` varchar(64) DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `screen_name_index` (`screen_name`),
  KEY `full_name_index` (`full_name`),
  KEY `network_college_index` (`network_college`),
  KEY `network_highschool_index` (`network_highschool`),
  KEY `entries_index` (`entries`),
  KEY `views_index` (`views`),
  KEY `partner_id_index` (`partner_id`,`id`),
  KEY `partner_created_at_indes` (`partner_id`,`created_at`),
  KEY `created_index` (`created_at`),
  KEY `partner_indexed_partner_data_int` (`partner_id`,`indexed_partner_data_int`),
  KEY `partner_indexed_partner_data_string` (`partner_id`,`indexed_partner_data_string`),
  KEY `partner_puser_id` (`partner_id`,`puser_id`),
  KEY `updated_at` (`updated_at`),
  KEY `login_data_id_index` (`login_data_id`),
  KEY `is_admin_index` (`is_admin`),
  FULLTEXT KEY `search_text_index` (`search_text`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `kuser_to_user_role`;

CREATE TABLE `kuser_to_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kuser_id` int(11) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kuser_to_user_role_FI_1` (`kuser_id`),
  KEY `kuser_to_user_role_FI_2` (`user_role_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `kvote`;

CREATE TABLE `kvote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kshow_id` varchar(20) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kshow_index` (`kshow_id`),
  KEY `entry_user_index` (`entry_id`),
  KEY `kvote_FI_3` (`kuser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `kwidget_log`;

CREATE TABLE `kwidget_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_id` varchar(24) DEFAULT NULL,
  `source_widget_id` varchar(24) DEFAULT NULL,
  `root_widget_id` varchar(24) DEFAULT NULL,
  `kshow_id` varchar(20) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `ui_conf_id` int(11) DEFAULT NULL,
  `referer` varchar(1024) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `ip1` int(11) DEFAULT NULL,
  `ip1_count` int(11) DEFAULT '0',
  `ip2` int(11) DEFAULT NULL,
  `ip2_count` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `plays` int(11) DEFAULT '0',
  `partner_id` int(11) DEFAULT '0',
  `subp_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `referer_index` (`referer`(333)),
  KEY `entry_id_kshow_id_index` (`entry_id`,`kshow_id`),
  KEY `partner_id_subp_id_index` (`partner_id`,`subp_id`),
  KEY `kwidget_log_FI_1` (`widget_id`),
  KEY `kwidget_log_FI_2` (`kshow_id`),
  KEY `kwidget_log_FI_4` (`ui_conf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `mail_job`;

CREATE TABLE `mail_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_type` smallint(6) DEFAULT NULL,
  `mail_priority` smallint(6) DEFAULT NULL,
  `recipient_name` varchar(64) DEFAULT NULL,
  `recipient_email` varchar(64) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `from_name` varchar(64) DEFAULT NULL,
  `from_email` varchar(64) DEFAULT NULL,
  `body_params` varchar(2048) DEFAULT NULL,
  `subject_params` varchar(512) DEFAULT NULL,
  `template_path` varchar(512) DEFAULT NULL,
  `culture` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `min_send_date` datetime DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `processor_name` varchar(64) DEFAULT NULL,
  `processor_location` varchar(64) DEFAULT NULL,
  `processor_expiration` datetime DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `lock_version` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `dc` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id_index` (`campaign_id`),
  KEY `STATUS_PRIORITY_INDEX` (`status`,`mail_priority`),
  KEY `recipient_id_index` (`recipient_id`),
  KEY `partner_id_index` (`partner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `media_info`;

CREATE TABLE `media_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `flavor_asset_id` varchar(20) DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `container_format` varchar(127) CHARACTER SET latin1 DEFAULT NULL,
  `container_id` varchar(127) DEFAULT NULL,
  `container_profile` varchar(127) DEFAULT NULL,
  `container_duration` int(11) DEFAULT NULL,
  `container_bit_rate` int(11) DEFAULT NULL,
  `video_format` varchar(127) CHARACTER SET latin1 DEFAULT NULL,
  `video_codec_id` varchar(127) DEFAULT NULL,
  `video_duration` int(11) DEFAULT NULL,
  `video_bit_rate` int(11) DEFAULT NULL,
  `video_bit_rate_mode` tinyint(4) DEFAULT NULL,
  `video_width` int(11) NOT NULL,
  `video_height` int(11) NOT NULL,
  `video_frame_rate` float DEFAULT NULL,
  `video_dar` float DEFAULT NULL,
  `video_rotation` int(11) NOT NULL,
  `audio_format` varchar(127) CHARACTER SET latin1 DEFAULT NULL,
  `audio_codec_id` varchar(127) DEFAULT NULL,
  `audio_duration` int(11) DEFAULT NULL,
  `audio_bit_rate` int(11) DEFAULT NULL,
  `audio_bit_rate_mode` tinyint(4) DEFAULT NULL,
  `audio_channels` tinyint(4) DEFAULT NULL,
  `audio_sampling_rate` int(11) DEFAULT NULL,
  `audio_resolution` int(11) DEFAULT NULL,
  `writing_lib` varchar(127) DEFAULT NULL,
  `custom_data` text,
  `raw_data` text,
  `multi_stream_info` varchar(1023) DEFAULT NULL,
  `flavor_asset_version` varchar(20) NOT NULL,
  `scan_type` int(11) NOT NULL,
  `multi_stream` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `flavor_asset_id_index` (`flavor_asset_id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `metadata`;

CREATE TABLE `metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `metadata_profile_id` int(11) DEFAULT NULL,
  `metadata_profile_version` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `object_type` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `metadata_profile`;

CREATE TABLE `metadata_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `views_version` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `object_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `metadata_profile_field`;

CREATE TABLE `metadata_profile_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `metadata_profile_id` int(11) DEFAULT NULL,
  `metadata_profile_version` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `label` varchar(127) DEFAULT NULL,
  `key` varchar(127) DEFAULT NULL,
  `type` varchar(127) DEFAULT NULL,
  `xpath` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `moderation`;

CREATE TABLE `moderation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `subp_id` int(11) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `object_type` smallint(6) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `puser_id` varchar(64) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `comments` varchar(1024) DEFAULT NULL,
  `group_id` varchar(64) DEFAULT NULL,
  `report_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_status_index` (`partner_id`,`status`),
  KEY `partner_id_group_id_status_index` (`partner_id`,`group_id`,`status`),
  KEY `object_index` (`partner_id`,`status`,`object_id`,`object_type`),
  KEY `moderation_FI_1` (`kuser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `moderation_flag`;

CREATE TABLE `moderation_flag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `object_type` smallint(6) DEFAULT NULL,
  `flagged_entry_id` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `flagged_kuser_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `comments` varchar(1024) COLLATE latin1_general_ci DEFAULT NULL,
  `flag_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_status_index` (`partner_id`,`status`),
  KEY `entry_object_index` (`partner_id`,`status`,`object_type`,`flagged_kuser_id`),
  KEY `moderation_flag_FI_1` (`kuser_id`),
  KEY `moderation_flag_FI_2` (`flagged_entry_id`),
  KEY `moderation_flag_FI_3` (`flagged_kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


DROP TABLE IF EXISTS `notification`;

CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `puser_id` varchar(64) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `notification_data` varchar(4096) DEFAULT NULL,
  `number_of_attempts` smallint(6) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `notification_result` varchar(256) DEFAULT NULL,
  `object_type` smallint(6) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `processor_name` varchar(64) DEFAULT NULL,
  `processor_location` varchar(64) DEFAULT NULL,
  `processor_expiration` datetime DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `lock_version` int(11) DEFAULT NULL,
  `dc` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_partner_id_index` (`status`,`partner_id`),
  KEY `object_type_object_id` (`object_type`,`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `partner`;

CREATE TABLE `partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_name` varchar(256) DEFAULT NULL,
  `url1` varchar(1024) DEFAULT NULL,
  `url2` varchar(1024) DEFAULT NULL,
  `secret` varchar(50) DEFAULT NULL,
  `admin_secret` varchar(50) DEFAULT NULL,
  `max_number_of_hits_per_day` int(11) DEFAULT '-1',
  `appear_in_search` int(11) DEFAULT '2',
  `debug_level` int(11) DEFAULT '0',
  `invalid_login_count` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_alias` varchar(64) DEFAULT NULL,
  `ANONYMOUS_KUSER_ID` int(11) DEFAULT NULL,
  `ks_max_expiry_in_seconds` int(11) DEFAULT NULL,
  `create_user_on_demand` tinyint(4) DEFAULT '1',
  `prefix` varchar(32) DEFAULT NULL,
  `admin_name` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `admin_email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `description` varchar(1024) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `commercial_use` tinyint(4) DEFAULT '0',
  `moderate_content` tinyint(4) DEFAULT '0',
  `notify` tinyint(4) DEFAULT '0',
  `custom_data` text,
  `service_config_id` varchar(64) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `content_categories` varchar(1024) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1',
  `phone` varchar(64) DEFAULT NULL,
  `describe_yourself` varchar(64) DEFAULT NULL,
  `adult_content` tinyint(4) DEFAULT '0',
  `partner_package` tinyint(4) DEFAULT '1',
  `usage_percent` int(11) DEFAULT '0',
  `storage_usage` int(11) DEFAULT '0',
  `eighty_percent_warning` int(11) DEFAULT NULL,
  `usage_limit_warning` int(11) DEFAULT NULL,
  `monitor_usage` int(11) DEFAULT '1',
  `priority_group_id` int(11) DEFAULT NULL,
  `work_group_id` int(11) DEFAULT NULL,
  `partner_group_type` smallint(6) DEFAULT '1',
  `partner_parent_id` int(11) DEFAULT NULL,
  `kmc_version` varchar(15) DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `partner_alias_index` (`partner_alias`),
  KEY `updated_at` (`updated_at`),
  KEY `partner_parent_index` (`partner_parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `partner_activity`;

CREATE TABLE `partner_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `activity_date` date DEFAULT NULL,
  `activity` int(11) DEFAULT NULL,
  `sub_activity` int(11) DEFAULT NULL,
  `amount` bigint(20) DEFAULT NULL,
  `amount1` bigint(20) DEFAULT NULL,
  `amount2` bigint(20) DEFAULT NULL,
  `amount3` int(11) DEFAULT '0',
  `amount4` int(11) DEFAULT '0',
  `amount5` int(11) DEFAULT '0',
  `amount6` int(11) DEFAULT '0',
  `amount7` int(11) DEFAULT '0',
  `amount8` int(11) DEFAULT '0',
  `amount9` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `partner_id` (`partner_id`,`activity_date`,`activity`,`sub_activity`),
  KEY `partner_id_index` (`partner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `partner_stats`;

CREATE TABLE `partner_stats` (
  `partner_id` int(11) NOT NULL,
  `views` int(11) DEFAULT NULL,
  `plays` int(11) DEFAULT NULL,
  `videos` int(11) DEFAULT NULL,
  `audios` int(11) DEFAULT NULL,
  `images` int(11) DEFAULT NULL,
  `entries` int(11) DEFAULT NULL,
  `users_1` int(11) DEFAULT NULL,
  `users_2` int(11) DEFAULT NULL,
  `rc_1` int(11) DEFAULT NULL,
  `rc_2` int(11) DEFAULT NULL,
  `kshows_1` int(11) DEFAULT NULL,
  `kshows_2` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  `widgets` int(11) DEFAULT NULL,
  PRIMARY KEY (`partner_id`),
  KEY `partner_id_index` (`partner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `partner_transactions`;

CREATE TABLE `partner_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `currency` varchar(6) DEFAULT NULL,
  `transaction_id` varchar(17) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `correlation_id` varchar(12) DEFAULT NULL,
  `ack` varchar(20) DEFAULT NULL,
  `transaction_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `partnership`;

CREATE TABLE `partnership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partnership_order` int(11) DEFAULT NULL,
  `image_path` varchar(256) DEFAULT NULL,
  `href` varchar(1024) DEFAULT NULL,
  `text` varchar(1024) DEFAULT NULL,
  `alt` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partnership_date` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partnership_order_index` (`partnership_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `friendly_name` varchar(100) DEFAULT NULL,
  `description` text,
  `partner_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `depends_on_permission_names` text,
  `tags` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `name_index` (`name`),
  KEY `name_partner_id_index` (`name`,`partner_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `permission_item`;

CREATE TABLE `permission_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `param_1` varchar(100) NOT NULL,
  `param_2` varchar(100) NOT NULL,
  `param_3` varchar(100) NOT NULL,
  `param_4` varchar(100) NOT NULL,
  `param_5` varchar(100) NOT NULL,
  `tags` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM; 			


DROP TABLE IF EXISTS `permission_to_permission_item`;

CREATE TABLE `permission_to_permission_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `permission_item_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_to_permission_item_FI_1` (`permission_id`),
  KEY `permission_to_permission_item_FI_2` (`permission_item_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `pr_news`;

CREATE TABLE `pr_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_order` int(11) DEFAULT NULL,
  `image_path` varchar(256) DEFAULT NULL,
  `href` varchar(1024) DEFAULT NULL,
  `text` varchar(1024) DEFAULT NULL,
  `alt` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `press_date` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `priority_group`;

CREATE TABLE `priority_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `priority` tinyint(4) DEFAULT NULL,
  `bulk_priority` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `puser_kuser`;

CREATE TABLE `puser_kuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `puser_id` varchar(64) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `puser_name` varchar(64) DEFAULT NULL,
  `custom_data` varchar(1024) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `context` varchar(1024) DEFAULT '',
  `subp_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `partner_puser_index` (`partner_id`,`puser_id`),
  KEY `kuser_id_index` (`kuser_id`),
  KEY `I_referenced_puser_role_FK_3_1` (`puser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `puser_role`;

CREATE TABLE `puser_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kshow_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `puser_id` varchar(64) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `subp_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `partner_puser_index` (`partner_id`,`puser_id`),
  KEY `kshow_id_index` (`kshow_id`),
  KEY `puser_role_FI_3` (`puser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `roughcut_entry`;

CREATE TABLE `roughcut_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roughcut_id` varchar(20) DEFAULT NULL,
  `roughcut_version` int(11) DEFAULT NULL,
  `roughcut_kshow_id` varchar(20) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `op_type` smallint(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `entry_id_index` (`entry_id`),
  KEY `roughcut_id_index` (`roughcut_id`),
  KEY `roughcut_kshow_id_index` (`roughcut_kshow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `scheduler`;

CREATE TABLE `scheduler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(127) DEFAULT NULL,
  `configured_id` int(11) DEFAULT NULL,
  `name` varchar(127) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `statuses` varchar(1023) DEFAULT '',
  `last_status` datetime DEFAULT NULL,
  `host` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `scheduler_config`;

CREATE TABLE `scheduler_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(127) DEFAULT NULL,
  `command_id` int(11) DEFAULT NULL,
  `command_status` tinyint(4) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) DEFAULT NULL,
  `scheduler_name` varchar(127) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `worker_configured_id` int(11) DEFAULT NULL,
  `worker_name` varchar(127) DEFAULT NULL,
  `variable` varchar(127) DEFAULT NULL,
  `variable_part` varchar(127) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_variable_index` (`variable`,`variable_part`),
  KEY `status_created_at_index` (`created_at`),
  KEY `scheduler_id_index` (`scheduler_id`),
  KEY `worker_id_index_type` (`worker_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `scheduler_status`;

CREATE TABLE `scheduler_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(127) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `worker_configured_id` int(11) DEFAULT NULL,
  `worker_type` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_type_index` (`type`),
  KEY `status_created_at_index` (`created_at`),
  KEY `scheduler_id_index` (`scheduler_id`),
  KEY `worker_id_index_type` (`worker_id`,`worker_type`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `scheduler_worker`;

CREATE TABLE `scheduler_worker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(127) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(127) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) DEFAULT NULL,
  `configured_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `name` varchar(127) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `statuses` varchar(1023) DEFAULT '',
  `last_status` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `short_link`;

CREATE TABLE `short_link` (
  `id` varchar(5) NOT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `name` varchar(63) DEFAULT NULL,
  `system_name` varchar(63) DEFAULT NULL,
  `full_url` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id` (`int_id`),
  KEY `partner_id` (`partner_id`),
  KEY `kuser_partner_name` (`partner_id`,`kuser_id`,`system_name`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sphinx_log`;

CREATE TABLE `sphinx_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `dc` int(11) DEFAULT NULL,
  `sql` longtext,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `creatd_at` (`created_at`),
  KEY `sphinx_log_FI_1` (`partner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sphinx_log_server`;

CREATE TABLE `sphinx_log_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server` varchar(63) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `last_log_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sphinx_log_server_FI_1` (`last_log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `storage_profile`;

CREATE TABLE `storage_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `desciption` varchar(127) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `protocol` tinyint(4) DEFAULT NULL,
  `storage_url` varchar(127) DEFAULT NULL,
  `storage_base_dir` varchar(127) DEFAULT NULL,
  `storage_username` varchar(31) DEFAULT NULL,
  `storage_password` varchar(31) DEFAULT NULL,
  `storage_ftp_passive_mode` tinyint(4) DEFAULT NULL,
  `delivery_http_base_url` varchar(127) DEFAULT NULL,
  `delivery_rmp_base_url` varchar(127) DEFAULT NULL,
  `delivery_iis_base_url` varchar(127) DEFAULT NULL,
  `min_file_size` int(11) DEFAULT NULL,
  `max_file_size` int(11) DEFAULT NULL,
  `flavor_params_ids` varchar(127) DEFAULT NULL,
  `max_concurrent_connections` int(11) DEFAULT NULL,
  `custom_data` text,
  `path_manager_class` varchar(127) DEFAULT NULL,
  `url_manager_class` varchar(127) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `syndication_feed`;

CREATE TABLE `syndication_feed` (
  `id` varchar(20) NOT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `playlist_id` varchar(20) DEFAULT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(4) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `landing_page` varchar(512) NOT NULL DEFAULT '',
  `flavor_param_id` int(11) DEFAULT NULL,
  `player_uiconf_id` int(11) DEFAULT NULL,
  `allow_embed` int(11) DEFAULT '1',
  `adult_content` varchar(10) DEFAULT NULL,
  `transcode_existing_content` int(11) DEFAULT '0',
  `add_to_default_conversion_profile` int(11) DEFAULT '0',
  `categories` varchar(1024) DEFAULT NULL,
  `feed_description` varchar(1024) DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  `feed_landing_page` varchar(512) DEFAULT NULL,
  `owner_name` varchar(50) DEFAULT NULL,
  `owner_email` varchar(128) DEFAULT NULL,
  `feed_image_url` varchar(512) DEFAULT NULL,
  `feed_author` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id_index` (`int_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tagword_count`;

CREATE TABLE `tagword_count` (
  `tag` varchar(30) NOT NULL,
  `tag_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`tag`),
  KEY `count_index` (`tag_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `temp_entry_update`;

CREATE TABLE `temp_entry_update` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `views` int(11) DEFAULT '0',
  `plays` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `temp_updated_kusers_storage_usage`;

CREATE TABLE `temp_updated_kusers_storage_usage` (
  `kuser_id` INT(11) NOT NULL,
  `storage_kb` INT(11)
) ENGINE=MYISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tmp`;

CREATE TABLE `tmp` (
  `id` int(11) NOT NULL DEFAULT '0',
  `kshow_id` int(11) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `media_type` smallint(6) DEFAULT NULL,
  `data` varchar(48) DEFAULT NULL,
  `thumbnail` varchar(48) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `votes` int(11) DEFAULT '0',
  `comments` int(11) DEFAULT '0',
  `favorites` int(11) DEFAULT '0',
  `total_rank` int(11) DEFAULT '0',
  `rank` int(11) DEFAULT '0',
  `tags` text,
  `anonymous` tinyint(4) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `source` smallint(6) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `source_link` varchar(1024) DEFAULT NULL,
  `license_type` smallint(6) DEFAULT NULL,
  `credit` varchar(1024) DEFAULT NULL,
  `length_in_msecs` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `display_in_search` tinyint(4) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `track_entry`;

CREATE TABLE `track_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `track_event_type_id` smallint(6) DEFAULT NULL,
  `ps_version` varchar(10) DEFAULT NULL,
  `context` varchar(511) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `host_name` varchar(20) DEFAULT NULL,
  `uid` varchar(63) DEFAULT NULL,
  `track_event_status_id` smallint(6) DEFAULT NULL,
  `changed_properties` varchar(1023) DEFAULT NULL,
  `param_1_str` varchar(255) DEFAULT NULL,
  `param_2_str` varchar(511) DEFAULT NULL,
  `param_3_str` varchar(511) DEFAULT NULL,
  `ks` varchar(511) DEFAULT NULL,
  `description` varchar(127) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `user_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_event_type_indx` (`partner_id`,`track_event_type_id`),
  KEY `entry_id_indx` (`entry_id`),
  KEY `track_event_type_id_indx` (`track_event_type_id`),
  KEY `param_1_indx` (`param_1_str`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ui_conf`;

CREATE TABLE `ui_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_type` smallint(6) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `subp_id` int(11) DEFAULT '0',
  `conf_file_path` varchar(128) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `width` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `html_params` varchar(256) DEFAULT NULL,
  `swf_url` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `conf_vars` varchar(4096) DEFAULT NULL,
  `use_cdn` tinyint(4) DEFAULT '1',
  `tags` text,
  `custom_data` text,
  `status` int(11) DEFAULT '2',
  `description` varchar(4096) DEFAULT NULL,
  `display_in_search` tinyint(4) DEFAULT '0',
  `creation_mode` tinyint(4) DEFAULT '1',
  `version` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `partner_id_creation_mode_index` (`partner_id`,`creation_mode`),
  KEY `updated_at` (`updated_at`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `upload_token`;

CREATE TABLE `upload_token` (
  `id` varchar(35) NOT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT '0',
  `kuser_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `file_name` varchar(256) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `uploaded_file_size` bigint(20) DEFAULT NULL,
  `upload_temp_path` varchar(256) DEFAULT NULL,
  `user_ip` varchar(39) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id` (`int_id`),
  KEY `partner_id_status` (`partner_id`,`status`),
  KEY `partner_id_created_at` (`partner_id`,`created_at`),
  KEY `status_created_at` (`status`,`created_at`),
  KEY `created_at` (`created_at`),
  KEY `upload_token_FI_1` (`kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_login_data`;

CREATE TABLE `user_login_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_email` varchar(100) NOT NULL,
  `first_name` varchar(40) DEFAULT NULL,
  `last_name` varchar(40) DEFAULT NULL,
  `sha1_password` varchar(40) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `config_partner_id` int(11) NOT NULL,
  `login_blocked_until` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `login_email_index` (`login_email`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `str_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `partner_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `permission_names` text,
  `tags` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `virus_scan_profile`;

CREATE TABLE `virus_scan_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `engine_type` int(11) DEFAULT NULL,
  `entry_filter` text,
  `action_if_infected` int(11) DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `widget`;

CREATE TABLE `widget` (
  `id` varchar(32) NOT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_widget_id` varchar(32) DEFAULT NULL,
  `root_widget_id` varchar(32) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `subp_id` int(11) DEFAULT NULL,
  `kshow_id` varchar(20) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `ui_conf_id` int(11) DEFAULT NULL,
  `custom_data` varchar(1024) DEFAULT NULL,
  `security_type` smallint(6) DEFAULT NULL,
  `security_policy` smallint(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_data` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id_index` (`int_id`),
  KEY `widget_FI_1` (`kshow_id`),
  KEY `widget_FI_2` (`entry_id`),
  KEY `widget_FI_3` (`ui_conf_id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `created_at_index` (`created_at`),
  KEY `updated_at` (`updated_at`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `widget_log`;

CREATE TABLE `widget_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kshow_id` varchar(20) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `kmedia_type` int(11) DEFAULT NULL,
  `widget_type` varchar(32) DEFAULT NULL,
  `referer` varchar(1024) DEFAULT NULL,
  `views` int(11) DEFAULT '0',
  `ip1` int(11) DEFAULT NULL,
  `ip1_count` int(11) DEFAULT '0',
  `ip2` int(11) DEFAULT NULL,
  `ip2_count` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `plays` int(11) DEFAULT '0',
  `partner_id` int(11) DEFAULT '0',
  `subp_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `referer_index` (`referer`(333)),
  KEY `entry_id_kshow_id_index` (`entry_id`,`kshow_id`),
  KEY `views_index` (`views`),
  KEY `plays_index` (`plays`),
  KEY `partner_id_subp_id_index` (`partner_id`,`subp_id`),
  KEY `created_at` (`created_at`),
  KEY `widget_index` (`widget_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `work_group`;

CREATE TABLE `work_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
