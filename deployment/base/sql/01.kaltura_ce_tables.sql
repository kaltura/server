
/*Table structure for table `access_control` */

CREATE TABLE IF NOT EXISTS `access_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `system_name` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(1024) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `site_restrict_type` tinyint(4) DEFAULT NULL,
  `site_restrict_list` text,
  `country_restrict_type` tinyint(4) DEFAULT NULL,
  `country_restrict_list` text,
  `ks_restrict_privilege` varchar(20) DEFAULT NULL,
  `prv_restrict_privilege` varchar(20) DEFAULT NULL,
  `prv_restrict_length` int(11) DEFAULT NULL,
  `kdir_restrict_type` tinyint(4) DEFAULT NULL,
  `custom_data` text,
  `rules` text,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`,`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `audit_trail` */

CREATE TABLE IF NOT EXISTS `audit_trail` (
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
  `client_tag` varchar(127) DEFAULT NULL,
  `description` varchar(1023) DEFAULT NULL,
  `error_description` varchar(1023) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_index` (`object_type`,`object_id`),
  KEY `partner_entry_index` (`partner_id`,`entry_id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `audit_trail_config` */

CREATE TABLE IF NOT EXISTS `audit_trail_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) DEFAULT NULL,
  `object_type` varchar(31) DEFAULT NULL,
  `descriptors` varchar(1023) DEFAULT NULL,
  `actions` varchar(1023) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_object_index` (`partner_id`,`object_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `audit_trail_data` */

CREATE TABLE IF NOT EXISTS `audit_trail_data` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `batch_job_lock` */

CREATE TABLE IF NOT EXISTS `batch_job_lock` (
  `id` bigint(20) NOT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `object_type` int(6) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `job_type` int(6) DEFAULT NULL,
  `job_sub_type` int(6) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `priority` tinyint(4) NOT NULL,
  `urgency` tinyint(4) NOT NULL,
  `estimated_effort` bigint(20) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `batch_job_id` bigint(20) NOT NULL,
  `custom_data` text,
  `batch_version` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lock_index` (`scheduler_id`,`worker_id`,`batch_index`),
  KEY `partner_urgency_type_index` (`partner_id`,`job_type`,`worker_id`,`urgency`),
  KEY `partner+type_status_index` (`job_type`,`status`,`dc`,`partner_id`),
  KEY `urgency_type_status_index` (`job_type`,`status`,`dc`,`urgency`),
  KEY `execution_attempts_index` (`job_type`,`execution_attempts`,`dc`),
  KEY `processor_expiration_index` (`job_type`,`execution_attempts`,`expiration`),
  KEY `dc_job_type_status_job_sub_type` (`dc`,`job_type`,`status`,`job_sub_type`),
  KEY `dc_job_type_status_attempts_job_sub_type` (`dc`,`job_type`,`status`,`execution_attempts`,`job_sub_type`),
  KEY `dc_job_type_status_attempts_created` (`dc`,`job_type`,`status`,`execution_attempts`,`created_at`),
  KEY `dc_status` (`dc`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `batch_job_lock_suspend` */

CREATE TABLE IF NOT EXISTS `batch_job_lock_suspend` (
  `id` bigint(20) NOT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `object_type` int(6) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `job_type` int(6) DEFAULT NULL,
  `job_sub_type` int(6) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `priority` tinyint(4) NOT NULL,
  `urgency` tinyint(4) NOT NULL,
  `estimated_effort` bigint(20) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `batch_job_id` bigint(20) NOT NULL,
  `custom_data` text,
  `batch_version` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dc_partner_job_type`(`dc`, `partner_id`, `job_type`, `job_sub_type`),
  INDEX `batch_job_lock_suspend_FI_1` (`batch_job_id`),
  CONSTRAINT `batch_job_lock_suspend_FK_1`
  FOREIGN KEY (`batch_job_id`)
  REFERENCES `batch_job_sep` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*Table structure for table `batch_job_log` */

CREATE TABLE IF NOT EXISTS `batch_job_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `job_id` bigint(20) DEFAULT NULL,
  `job_type` smallint(6) DEFAULT NULL,
  `job_sub_type` smallint(6) DEFAULT NULL,
  `data` text,
  `file_size` int(11) DEFAULT NULL,
  `duplication_key` varchar(2047) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `log_status` int(11) DEFAULT NULL,
  `abort` tinyint(4) DEFAULT NULL,
  `check_again_timeout` int(11) DEFAULT NULL,
  `progress` tinyint(4) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `updates_count` smallint(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT NULL,
  `work_group_id` int(11) DEFAULT NULL,
  `queue_time` datetime DEFAULT NULL,
  `finish_time` datetime DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT '',
  `partner_id` int(11) DEFAULT '0',
  `subp_id` int(11) DEFAULT '0',
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `last_scheduler_id` int(11) DEFAULT NULL,
  `last_worker_id` int(11) DEFAULT NULL,
  `last_worker_remote` tinyint(4) DEFAULT NULL,
  `processor_expiration` datetime DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `lock_version` int(11) DEFAULT NULL,
  `twin_job_id` bigint(20) DEFAULT NULL,
  `bulk_job_id` bigint(20) DEFAULT NULL,
  `root_job_id` bigint(20) DEFAULT NULL,
  `parent_job_id` bigint(20) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `err_type` int(11) DEFAULT NULL,
  `err_number` int(11) DEFAULT NULL,
  `on_stress_divert_to` int(11) DEFAULT NULL,
  `param_1` int(11) DEFAULT NULL,
  `param_2` varchar(255) DEFAULT NULL,
  `param_3` varchar(255) DEFAULT NULL,
  `param_4` int(11) DEFAULT NULL,
  `param_5` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `partner_id` (`partner_id`,`job_type`,`abort`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `batch_job_sep` */

CREATE TABLE IF NOT EXISTS `batch_job_sep` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `entry_id` varchar(20) DEFAULT NULL,
  `object_type` int(6) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `parent_job_id` bigint(20) DEFAULT NULL,
  `bulk_job_id` bigint(20) DEFAULT NULL,
  `root_job_id` bigint(20) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `job_type` int(6) DEFAULT NULL,
  `job_sub_type` int(6) NOT NULL DEFAULT 0,
  `priority` tinyint(4) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `data` text,
  `description` varchar(1024) DEFAULT NULL,
  `err_type` int(11) NOT NULL DEFAULT '0',
  `err_number` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `queue_time` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `finish_time` datetime DEFAULT NULL,
  `last_scheduler_id` int(11) DEFAULT NULL,
  `last_worker_id` int(11) DEFAULT NULL,
  `execution_status` int(4) DEFAULT NULL,
  `batch_job_lock_id` bigint(20) DEFAULT NULL,
  `history` text,
  `lock_info` text,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `entry_id_index` (`entry_id`),
  KEY `object_index` (`object_id`,`object_type`),
  KEY `bulk_job_id_index` (`bulk_job_id`),
  KEY `root_job_id_index` (`root_job_id`),
  KEY `parent_job_id_index` (`parent_job_id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_type_status_index` (`partner_id`,`job_type`,`status`),
  KEY `status_job_type_job_sub_type_created_at` (`status`,`job_type`,`job_sub_type`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `blocked_email` */

CREATE TABLE IF NOT EXISTS `blocked_email` (
  `email` varchar(40) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `bulk_upload_result` */

CREATE TABLE IF NOT EXISTS `bulk_upload_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `bulk_upload_job_id` int(11) DEFAULT NULL,
  `line_index` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `object_type` int(11) DEFAULT '1',
  `action` int(11) DEFAULT '1',
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
  `error_description` text,
  `plugins_data` varchar(9182) NOT NULL,
  `custom_data` text,
  `status` int(11) DEFAULT NULL,
  `object_status` int(11) DEFAULT NULL,
  `object_error_description` varchar(255) DEFAULT NULL,
  `error_code` int(11) DEFAULT NULL,
  `error_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id_index_id` (`object_id`,`id`),
  KEY `job_id_line_index` (`bulk_upload_job_id`,`line_index`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `caption_asset_item` */

CREATE TABLE IF NOT EXISTS `caption_asset_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `caption_asset_id` varchar(20) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caption_asset` (`caption_asset_id`),
  KEY `partner_caption_asset` (`partner_id`,`caption_asset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `category` */

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `depth` tinyint(4) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `full_name` text,
  `entries_count` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `direct_entries_count` int(11) DEFAULT '0',
  `members_count` int(11) DEFAULT '0',
  `pending_members_count` int(11) DEFAULT '0',
  `description` text,
  `tags` text,
  `display_in_search` tinyint(4) DEFAULT '1',
  `privacy` tinyint(4) DEFAULT '1',
  `inheritance_type` tinyint(4) DEFAULT '2',
  `user_join_policy` tinyint(4) DEFAULT '3',
  `default_permission_level` tinyint(4) DEFAULT '3',
  `kuser_id` int(11) DEFAULT NULL,
  `puser_id` varchar(100) NOT NULL,
  `reference_id` varchar(512) DEFAULT NULL,
  `contribution_policy` tinyint(4) DEFAULT '2',
  `custom_data` text,
  `privacy_context` varchar(255) DEFAULT NULL,
  `privacy_contexts` varchar(255) DEFAULT NULL,
  `inherited_parent_id` int(11) DEFAULT NULL,
  `full_ids` text NOT NULL,
  `direct_sub_categories_count` int(11) DEFAULT '0',
  `moderation` tinyint(4) DEFAULT '0',
  `pending_entries_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `full_name_index` (`full_name`(255)),
  KEY `partner_id` (`partner_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `category_entry` */

CREATE TABLE IF NOT EXISTS `category_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `custom_data` text,
  `updated_at` datetime DEFAULT NULL,
  `category_full_ids` text NOT NULL,
  `status` int(11) DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `category_id_index` (`category_id`),
  KEY `entry_id_index` (`entry_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `category_kuser` */

CREATE TABLE IF NOT EXISTS `category_kuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `kuser_id` int(11) NOT NULL,
  `puser_id` varchar(100) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `permission_level` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `inherit_from_category` int(11) DEFAULT NULL,
  `update_method` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  `category_full_ids` text,
  `screen_name` varchar(100) NOT NULL,
  `permission_names` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `category_index` (`category_id`,`status`),
  KEY `kuser_index` (`kuser_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `comment` */

CREATE TABLE IF NOT EXISTS `comment` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `control_panel_command` */

CREATE TABLE IF NOT EXISTS `control_panel_command` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) NOT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `worker_configured_id` int(11) NOT NULL,
  `worker_name` varchar(50) DEFAULT 'null',
  `batch_index` int(11) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `target_type` smallint(6) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `cause` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `error_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `conversion` */

CREATE TABLE IF NOT EXISTS `conversion` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `conversion_params` */

CREATE TABLE IF NOT EXISTS `conversion_params` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `conversion_profile` */

CREATE TABLE IF NOT EXISTS `conversion_profile` (
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
  KEY `partner_id_index` (`partner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `conversion_profile_2` */

CREATE TABLE IF NOT EXISTS `conversion_profile_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `description` varchar(1024) NOT NULL DEFAULT '',
  `system_name` varchar(128) NOT NULL DEFAULT '',
  `tags` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '2',
  `default_entry_id` varchar(20) DEFAULT NULL,
  `crop_left` int(11) NOT NULL DEFAULT '-1',
  `crop_top` int(11) NOT NULL DEFAULT '-1',
  `crop_width` int(11) NOT NULL DEFAULT '-1',
  `crop_height` int(11) NOT NULL DEFAULT '-1',
  `clip_start` int(11) NOT NULL DEFAULT '-1',
  `clip_duration` int(11) NOT NULL DEFAULT '-1',
  `input_tags_map` varchar(1023) DEFAULT NULL,
  `creation_mode` smallint(6) DEFAULT '1',
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_id_status` (`partner_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `cue_point` */

CREATE TABLE IF NOT EXISTS `cue_point` (
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(20) NOT NULL DEFAULT '',
  `parent_id` varchar(255) DEFAULT NULL,
  `entry_id` varchar(31) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `system_name` varchar(127) DEFAULT NULL,
  `text` text,
  `tags` varchar(255) DEFAULT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `sub_type` int(11) NOT NULL,
  `kuser_id` int(11) NOT NULL,
  `custom_data` text,
  `partner_data` text,
  `partner_sort_value` int(11) DEFAULT NULL,
  `thumb_offset` int(11) DEFAULT NULL,
  `depth` int(11) NOT NULL DEFAULT '0',
  `children_count` int(11) NOT NULL DEFAULT '0',
  `direct_children_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `int_id_index` (`int_id`),
  KEY `entry_id` (`entry_id`),
  KEY `parent_id` (`parent_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `distribution_profile` */

CREATE TABLE IF NOT EXISTS `distribution_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `provider_type` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `system_name` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(4) DEFAULT NULL,
  `submit_enabled` tinyint(4) DEFAULT NULL,
  `update_enabled` tinyint(4) DEFAULT NULL,
  `delete_enabled` tinyint(4) DEFAULT NULL,
  `report_enabled` tinyint(4) DEFAULT NULL,
  `auto_create_flavors` varchar(255) DEFAULT NULL,
  `auto_create_thumb` varchar(255) DEFAULT NULL,
  `optional_flavor_params_ids` text,
  `required_flavor_params_ids` text,
  `optional_thumb_dimensions` varchar(2048) DEFAULT NULL,
  `required_thumb_dimensions` varchar(2048) DEFAULT NULL,
  `report_interval` int(11) NOT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_status_provider` (`partner_id`,`status`,`provider_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `drop_folder` */

CREATE TABLE IF NOT EXISTS `drop_folder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `dc` int(11) NOT NULL,
  `path` text NOT NULL,
  `conversion_profile_id` int(11) DEFAULT NULL,
  `file_delete_policy` int(11) DEFAULT NULL,
  `file_handler_type` int(11) DEFAULT NULL,
  `file_name_patterns` text NOT NULL,
  `file_handler_config` text NOT NULL,
  `tags` text,
  `error_code` int(11) DEFAULT NULL,
  `error_description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `dc_status` (`dc`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `drop_folder_file` */

CREATE TABLE IF NOT EXISTS `drop_folder_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `drop_folder_id` int(11) NOT NULL,
  `file_name` varchar(500) NOT NULL,
  `type`  int(11),
  `status` int(11) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_size_last_set_at` datetime DEFAULT NULL,
  `error_code` int(11) DEFAULT NULL,
  `error_description` text,
  `parsed_slug` varchar(500) DEFAULT NULL,
  `parsed_flavor` varchar(500) DEFAULT NULL,
  `lead_drop_folder_file_id` int(11) DEFAULT NULL,
  `deleted_drop_folder_file_id` int(11) NOT NULL DEFAULT '0',
  `md5_file_name` varchar(32) NOT NULL DEFAULT 0,
  `entry_id` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `upload_start_detected_at` datetime DEFAULT NULL,
  `upload_end_detected_at` datetime DEFAULT NULL,
  `import_started_at` datetime DEFAULT NULL,
  `import_ended_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file_name_in_drop_folder_unique` (`md5_file_name`,`drop_folder_id`,`deleted_drop_folder_file_id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `drop_folder_id` (`drop_folder_id`,`status`),
  KEY `drop_folder_id_file_name` (`drop_folder_id`,`file_name`(255))
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `dynamic_enum` */

CREATE TABLE IF NOT EXISTS `dynamic_enum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enum_name` varchar(255) NOT NULL,
  `value_name` varchar(255) NOT NULL,
  `plugin_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8;

/*Table structure for table `email_ingestion_profile` */

CREATE TABLE IF NOT EXISTS `email_ingestion_profile` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `entry` */

CREATE TABLE IF NOT EXISTS `entry` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `kshow_id` varchar(20) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
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
  `flavor_params_ids` varchar(512) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `available_from` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kshow_created_index` (`kshow_id`,`created_at`),
  KEY `int_id_index` (`int_id`),
  KEY `entry_FI_2` (`kuser_id`),
  KEY `status_created_index` (`status`,`created_at`),
  KEY `partner_status_index` (`partner_id`,`status`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_created_at` (`partner_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `entry_distribution` */

CREATE TABLE IF NOT EXISTS `entry_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `distribution_profile_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `dirty_status` tinyint(4) DEFAULT NULL,
  `thumb_asset_ids` text,
  `flavor_asset_ids` text,
  `asset_ids` text,
  `sunrise` datetime DEFAULT NULL,
  `sunset` datetime DEFAULT NULL,
  `remote_id` varchar(255) DEFAULT NULL,
  `plays` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `validation_errors` text,
  `error_type` int(11) DEFAULT NULL,
  `error_number` int(11) DEFAULT NULL,
  `error_description` varchar(255) DEFAULT NULL,
  `last_report` datetime NOT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_profile_status` (`partner_id`,`distribution_profile_id`,`status`),
  KEY `entry_profile` (`entry_id`,`distribution_profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `event_notification_template` */

CREATE TABLE IF NOT EXISTS `event_notification_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(127) NOT NULL,
  `system_name` varchar(127) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  `event_type` int(11) DEFAULT NULL,
  `object_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_status_index` (`partner_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `favorite` */

CREATE TABLE IF NOT EXISTS `favorite` (
  `kuser_id` int(11) DEFAULT NULL,
  `subject_type` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `privacy` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subject_index` (`subject_type`,`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `file_sync` */

CREATE TABLE IF NOT EXISTS `file_sync` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `linked_id` bigint(20) DEFAULT NULL,
  `link_count` int(11) DEFAULT NULL,
  `file_root` varchar(64) DEFAULT NULL,
  `file_path` varchar(512) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `custom_data` text,
  `deleted_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`object_id`,`object_type`,`version`,`object_sub_type`,`dc`,`deleted_id`),
  KEY `linked_id_indx` (`linked_id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_id_dc_status` (`partner_id`,`dc`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `flag` */

CREATE TABLE IF NOT EXISTS `flag` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `flavor_asset` */

CREATE TABLE IF NOT EXISTS `flavor_asset` (
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
  `type` int(11) NOT NULL DEFAULT '1',
  `custom_data` text,
  PRIMARY KEY (`int_id`),
  KEY `partner_id_entry_id` (`partner_id`,`entry_id`),
  KEY `flavor_asset_FI_1` (`entry_id`),
  KEY `flavor_asset_FI_2` (`flavor_params_id`),
  KEY `id_indx` (`id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `flavor_params` */

CREATE TABLE IF NOT EXISTS `flavor_params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` int(11) NOT NULL DEFAULT '0',
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `system_name` varchar(128) NOT NULL DEFAULT '',
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
  `type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_id` (`partner_id`,`deleted_at`,`is_default`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `flavor_params_conversion_profile` */

CREATE TABLE IF NOT EXISTS `flavor_params_conversion_profile` (
  `conversion_profile_id` int(11) NOT NULL,
  `flavor_params_id` int(11) NOT NULL,
  `system_name` varchar(128) NOT NULL DEFAULT '',
  `origin` tinyint(4) NOT NULL DEFAULT '0',
  `ready_behavior` tinyint(4) NOT NULL,
  `force_none_complied` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` tinyint(4) DEFAULT '0',
  `custom_data` text,
  `delete_policy` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `conversion_profile_id_flavor_params` (`conversion_profile_id`,`flavor_params_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `flavor_params_output` */

CREATE TABLE IF NOT EXISTS `flavor_params_output` (
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
  `conversion_engines` text,
  `conversion_engines_extra_params` text,
  `custom_data` text,
  `command_lines` text,
  `file_ext` varchar(4) DEFAULT NULL,
  `deinterlice` int(11) NOT NULL,
  `rotate` int(11) NOT NULL,
  `operators` text,
  `engine_version` smallint(6) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `flavor_params_output_FI_2` (`entry_id`),
  KEY `flavor_params_output_FI_3` (`flavor_asset_id`),
  KEY `updated_at_index` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `flickr_token` */

CREATE TABLE IF NOT EXISTS `flickr_token` (
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

/*Table structure for table `generic_distribution_provider` */

CREATE TABLE IF NOT EXISTS `generic_distribution_provider` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `generic_distribution_provider_action` */

CREATE TABLE IF NOT EXISTS `generic_distribution_provider_action` (
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
  KEY `generic_distribution_provider_status` (`generic_distribution_provider_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `invalid_session` */

CREATE TABLE IF NOT EXISTS `invalid_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ks` varchar(300) DEFAULT NULL,
  `ks_valid_until` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `actions_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ks_index` (`ks`(255)),
  KEY `ks_valid_until_index` (`ks_valid_until`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `kce_installation_error` */

CREATE TABLE IF NOT EXISTS `kce_installation_error` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `keyword` */

CREATE TABLE IF NOT EXISTS `keyword` (
  `word` varchar(30) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity_type` int(11) DEFAULT NULL,
  `entity_columns` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`word`),
  KEY `word_index` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `kshow` */

CREATE TABLE IF NOT EXISTS `kshow` (
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
  `permissions` varchar(1024) DEFAULT NULL,
  `group_id` varchar(64) DEFAULT NULL,
  `plays` int(11) DEFAULT '0',
  `partner_data` varchar(4096) DEFAULT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `partner_group_index` (`partner_id`,`group_id`),
  KEY `int_id_index` (`int_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `kshow_kuser` */

CREATE TABLE IF NOT EXISTS `kshow_kuser` (
  `kshow_id` varchar(20) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `subscription_type` int(11) DEFAULT NULL,
  `alert_type` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `kshow_index` (`kshow_id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subscription_index` (`kshow_id`,`subscription_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `kuser` */

CREATE TABLE IF NOT EXISTS `kuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_data_id` int(11) DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT NULL,
  `screen_name` varchar(127) DEFAULT NULL,
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
  `partner_data` varchar(4096) DEFAULT NULL,
  `storage_size` int(11) DEFAULT '0',
  `puser_id` varchar(100) DEFAULT NULL,
  `admin_tags` text,
  `indexed_partner_data_int` int(11) DEFAULT NULL,
  `indexed_partner_data_string` varchar(64) DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_created_at_indes` (`partner_id`,`created_at`),
  KEY `partner_puser_id` (`partner_id`,`puser_id`),
  KEY `updated_at` (`updated_at`),
  KEY `login_data_id_index` (`login_data_id`),
  KEY `partner_id` (`partner_id`,`status`,`is_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `kuser_to_user_role` */

CREATE TABLE IF NOT EXISTS `kuser_to_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kuser_id` int(11) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kuser_to_user_role_FI_1` (`kuser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `kvote` */

CREATE TABLE IF NOT EXISTS `kvote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kshow_id` varchar(20) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `kvote_type` int(11) DEFAULT '1',
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `kshow_index` (`kshow_id`),
  KEY `entry_user_index` (`entry_id`),
  KEY `kvote_FI_3` (`kuser_id`),
  KEY `entry_user_status_index` (`entry_id`,`kuser_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `kwidget_log` */

CREATE TABLE IF NOT EXISTS `kwidget_log` (
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
  KEY `referer_index` (`referer`(255)),
  KEY `entry_id_kshow_id_index` (`entry_id`,`kshow_id`),
  KEY `partner_id_subp_id_index` (`partner_id`,`subp_id`),
  KEY `kwidget_log_FI_1` (`widget_id`),
  KEY `kwidget_log_FI_2` (`kshow_id`),
  KEY `kwidget_log_FI_4` (`ui_conf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `media_info` */

CREATE TABLE IF NOT EXISTS `media_info` (
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
  PRIMARY KEY (`id`) KEY_BLOCK_SIZE=8,
  KEY `flavor_asset_id_index` (`flavor_asset_id`) KEY_BLOCK_SIZE=8,
  KEY `updated_at_index` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=4;

/*Table structure for table `metadata` */

CREATE TABLE IF NOT EXISTS `metadata` (
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
  PRIMARY KEY (`id`),
  KEY `profile_id_and_version` (`metadata_profile_id`,`metadata_profile_version`),
  KEY `object_id_type_status` (`object_id`,`object_type`,`status`),
  KEY `partner_id_status` (`partner_id`,`status`,`object_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `metadata_profile` */

CREATE TABLE IF NOT EXISTS `metadata_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `views_version` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `system_name` varchar(127) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) DEFAULT NULL,
  `object_type` int(11) DEFAULT NULL,
  `create_mode` int(11) NOT NULL DEFAULT '1',
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `metadata_profile_field` */

CREATE TABLE IF NOT EXISTS `metadata_profile_field` (
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
  PRIMARY KEY (`id`),
  KEY `partner_id` (`partner_id`),
  KEY `profile_id_and_version` (`metadata_profile_id`,`metadata_profile_version`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `moderation` */

CREATE TABLE IF NOT EXISTS `moderation` (
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
  KEY `partner_id_group_id_status_index` (`partner_id`,`group_id`,`status`),
  KEY `object_index` (`partner_id`,`status`,`object_id`,`object_type`),
  KEY `moderation_FI_1` (`kuser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `moderation_flag` */

CREATE TABLE IF NOT EXISTS `moderation_flag` (
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
  KEY `entry_object_index` (`partner_id`,`status`,`object_type`,`flagged_kuser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Table structure for table `partner` */

CREATE TABLE IF NOT EXISTS `partner` (
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
  `kmc_version` varchar(15) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `partner_alias_index` (`partner_alias`),
  KEY `updated_at` (`updated_at`),
  KEY `partner_parent_index` (`partner_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

/*Table structure for table `partner_activity` */

CREATE TABLE IF NOT EXISTS `partner_activity` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `partner_load` */

CREATE TABLE IF NOT EXISTS `partner_load` (
  `partner_id` int(11) NOT NULL,
  `job_type` int(6) NOT NULL,
  `job_sub_type` int(6) NOT NULL DEFAULT '0',
  `partner_load` int(11) DEFAULT NULL,
  `weighted_partner_load` int(11) DEFAULT '0',
  `custom_data` text,
  `dc` int(11) NOT NULL DEFAULT '0',
  `quota` int(11) DEFAULT NULL,
  PRIMARY KEY (`partner_id`,`job_type`,`job_sub_type`,`dc`),
  KEY `weight_index` (`weighted_partner_load`),
  KEY `load_index` (`partner_load`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `partner_stats` */

CREATE TABLE IF NOT EXISTS `partner_stats` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `partner_transactions` */

CREATE TABLE IF NOT EXISTS `partner_transactions` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `permission` */

CREATE TABLE IF NOT EXISTS `permission` (
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
  KEY `name_partner_id_index` (`name`,`partner_id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_id_status_type` (`partner_id`,`status`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `permission_item` */

CREATE TABLE IF NOT EXISTS `permission_item` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `permission_to_permission_item` */

CREATE TABLE IF NOT EXISTS `permission_to_permission_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `permission_item_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_to_permission_item_FI_2` (`permission_item_id`),
  KEY `permission_to_permission_item_FI_1` (`permission_id`,`permission_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `priority_group` */

CREATE TABLE IF NOT EXISTS `priority_group` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `puser_kuser` */

CREATE TABLE IF NOT EXISTS `puser_kuser` (
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
  KEY `kuser_id_index` (`kuser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `puser_role` */

CREATE TABLE IF NOT EXISTS `puser_role` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `report` */

CREATE TABLE IF NOT EXISTS `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `system_name` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(1024) NOT NULL DEFAULT '',
  `query` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `roughcut_entry` */

CREATE TABLE IF NOT EXISTS `roughcut_entry` (
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
  KEY `entry_id_index` (`entry_id`),
  KEY `roughcut_id_index` (`roughcut_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `scheduler` */

CREATE TABLE IF NOT EXISTS `scheduler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `configured_id` int(11) NOT NULL,
  `name` varchar(20) DEFAULT '',
  `description` varchar(20) DEFAULT '',
  `statuses` varchar(255) NOT NULL,
  `last_status` datetime NOT NULL,
  `host` varchar(63) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `configured_id` (`configured_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `scheduler_config` */

CREATE TABLE IF NOT EXISTS `scheduler_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `command_id` int(11) DEFAULT NULL,
  `command_status` tinyint(4) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) NOT NULL,
  `scheduler_name` varchar(20) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `worker_configured_id` int(11) NOT NULL,
  `worker_name` varchar(50) DEFAULT 'null',
  `variable` varchar(100) DEFAULT NULL,
  `variable_part` varchar(100) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_variable_index` (`variable`,`variable_part`),
  KEY `status_created_at_index` (`created_at`),
  KEY `worker_id_index_type` (`worker_id`),
  KEY `scheduler_id_index` (`scheduler_id`,`command_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `scheduler_status` */

CREATE TABLE IF NOT EXISTS `scheduler_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) NOT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `worker_configured_id` int(11) NOT NULL,
  `worker_type` smallint(6) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_type_index` (`type`),
  KEY `scheduler_id_index` (`scheduler_id`),
  KEY `worker_id_index_type` (`worker_id`,`worker_type`),
  KEY `status_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `scheduler_worker` */

CREATE TABLE IF NOT EXISTS `scheduler_worker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `scheduler_configured_id` int(11) NOT NULL,
  `configured_id` int(11) NOT NULL,
  `type` smallint(6) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` varchar(128) DEFAULT NULL,
  `statuses` varchar(255) NOT NULL,
  `last_status` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `configured_id` (`configured_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `short_link` */

CREATE TABLE IF NOT EXISTS `short_link` (
  `id` varchar(5) NOT NULL,
  `int_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `kuser_id` int(11) DEFAULT NULL,
  `name` varchar(63) DEFAULT NULL,
  `system_name` varchar(63) DEFAULT NULL,
  `full_url` text DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id` (`int_id`),
  KEY `kuser_partner_name` (`partner_id`,`kuser_id`,`system_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;

/*Table structure for table `storage_profile` */

CREATE TABLE IF NOT EXISTS `storage_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `name` varchar(31) DEFAULT NULL,
  `system_name` varchar(128) NOT NULL DEFAULT '',
  `desciption` varchar(127) DEFAULT NULL,
  `status` integer DEFAULT NULL,
  `protocol` integer DEFAULT NULL,
  `storage_url` varchar(127) DEFAULT NULL,
  `storage_base_dir` varchar(127) DEFAULT NULL,
  `storage_username` varchar(31) DEFAULT NULL,
  `storage_password` varchar(64) DEFAULT NULL,
  `storage_ftp_passive_mode` integer DEFAULT NULL,
  `delivery_http_base_url` varchar(127) DEFAULT NULL,
  `delivery_rmp_base_url` varchar(127) DEFAULT NULL,
  `delivery_iis_base_url` varchar(127) DEFAULT NULL,
  `min_file_size` int(11) DEFAULT NULL,
  `max_file_size` int(11) DEFAULT NULL,
  `flavor_params_ids` text,
  `max_concurrent_connections` int(11) DEFAULT NULL,
  `custom_data` text,
  `path_manager_class` varchar(127) DEFAULT NULL,
  `url_manager_class` varchar(127) NOT NULL,
  `delivery_priority` int(11) DEFAULT '1',
  `delivery_status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `syndication_feed` */

CREATE TABLE IF NOT EXISTS `syndication_feed` (
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
  `custom_data` text,
  `display_in_search` tinyint(4) DEFAULT '1',
  `privacy_context` varchar(255) DEFAULT NULL,
  `enforce_entitlement` tinyint(4) DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id_index` (`int_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `tag` */

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `object_type` int(11) NOT NULL,
  `instance_count` int(11) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `privacy_context` varchar(255) DEFAULT 'NO_PC',
  `custom_data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_tag` (`partner_id`),
  KEY `partner_object_tag` (`partner_id`,`object_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `temp_entry_update` */

CREATE TABLE IF NOT EXISTS `temp_entry_update` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `views` decimal(32,0) DEFAULT NULL,
  `plays` decimal(32,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `temp_updated_kusers_storage_usage` */

CREATE TABLE IF NOT EXISTS `temp_updated_kusers_storage_usage` (
  `kuser_id` int(11) NOT NULL,
  `storage_kb` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tmp` */

CREATE TABLE IF NOT EXISTS `tmp` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `track_entry` */

CREATE TABLE IF NOT EXISTS `track_entry` (
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
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `entry_id_indx` (`entry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;

/*Table structure for table `ui_conf` */

CREATE TABLE IF NOT EXISTS `ui_conf` (
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
  `html5_url` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partner_id_creation_mode_index` (`partner_id`,`creation_mode`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

/*Table structure for table `upload_token` */

CREATE TABLE IF NOT EXISTS `upload_token` (
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
  `object_type` varchar(127) DEFAULT NULL,
  `object_id` varchar(31) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `int_id` (`int_id`),
  KEY `partner_id_status` (`partner_id`,`status`),
  KEY `status_created_at` (`status`,`created_at`),
  KEY `upload_token_FI_1` (`kuser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `user_login_data` */

CREATE TABLE IF NOT EXISTS `user_login_data` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `user_role` */

CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `str_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `system_name` varchar(128) DEFAULT NULL,
  `description` text,
  `partner_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `permission_names` text,
  `tags` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `custom_data` text,
  PRIMARY KEY (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `str_id` (`str_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `virus_scan_profile` */

CREATE TABLE IF NOT EXISTS `virus_scan_profile` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `widget` */

CREATE TABLE IF NOT EXISTS `widget` (
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
  KEY `widget_FI_2` (`entry_id`),
  KEY `widget_FI_3` (`ui_conf_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `widget_log` */

CREATE TABLE IF NOT EXISTS `widget_log` (
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
  KEY `referer_index` (`referer`(255)),
  KEY `entry_id_kshow_id_index` (`entry_id`,`kshow_id`),
  KEY `views_index` (`views`),
  KEY `plays_index` (`plays`),
  KEY `partner_id_subp_id_index` (`partner_id`,`subp_id`),
  KEY `created_at` (`created_at`),
  KEY `widget_index` (`widget_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*Table structure for table `work_group` */

CREATE TABLE IF NOT EXISTS `work_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(20) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `api_server`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`hostname` VARCHAR(256),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `drm_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` TEXT  NOT NULL,
	`description` TEXT,
	`provider` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`license_server_url` TEXT,
	`default_policy` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
