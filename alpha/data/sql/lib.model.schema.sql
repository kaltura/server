
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- kuser
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kuser`;


CREATE TABLE `kuser`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`screen_name` VARCHAR(20),
	`full_name` VARCHAR(40),
	`email` VARCHAR(50),
	`sha1_password` VARCHAR(40),
	`salt` VARCHAR(32),
	`date_of_birth` DATE,
	`country` VARCHAR(2),
	`state` VARCHAR(16),
	`city` VARCHAR(30),
	`zip` VARCHAR(10),
	`url_list` VARCHAR(256),
	`picture` VARCHAR(48),
	`icon` TINYINT,
	`about_me` VARCHAR(4096),
	`tags` TEXT,
	`tagline` VARCHAR(256),
	`network_highschool` VARCHAR(30),
	`network_college` VARCHAR(30),
	`network_other` VARCHAR(30),
	`mobile_num` VARCHAR(16),
	`mature_content` TINYINT,
	`gender` TINYINT,
	`registration_ip` INTEGER,
	`registration_cookie` VARCHAR(256),
	`im_list` VARCHAR(256),
	`views` INTEGER default 0,
	`fans` INTEGER default 0,
	`entries` INTEGER default 0,
	`storage_size` INTEGER default 0,
	`produced_kshows` INTEGER default 0,
	`status` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER default 0,
	`display_in_search` TINYINT,
	`search_text` VARCHAR(4096),
	`partner_data` VARCHAR(4096),
	`puser_id` VARCHAR(64),
	`admin_tags` TEXT,
	`indexed_partner_data_int` INTEGER,
	`indexed_partner_data_string` VARCHAR(64),
	PRIMARY KEY (`id`),
	KEY `screen_name_index`(`screen_name`),
	KEY `full_name_index`(`full_name`),
	KEY `network_college_index`(`network_college`),
	KEY `network_highschool_index`(`network_highschool`),
	KEY `entries_index`(`entries`),
	KEY `views_index`(`views`),
	KEY `display_in_search_index`(`display_in_search`),
	KEY `partner_indexed_partner_data_int`(`partner_id`, `indexed_partner_data_int`),
	KEY `partner_indexed_partner_data_string`(`partner_id`, `indexed_partner_data_string`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kshow
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kshow`;


CREATE TABLE `kshow`
(
	`id` VARCHAR(20)  NOT NULL,
	`producer_id` INTEGER,
	`episode_id` VARCHAR(20),
	`name` VARCHAR(60),
	`subdomain` VARCHAR(30),
	`description` TEXT,
	`status` INTEGER default 0,
	`type` INTEGER,
	`media_type` INTEGER,
	`format_type` INTEGER,
	`language` INTEGER,
	`start_date` DATE,
	`end_date` DATE,
	`skin` TEXT,
	`thumbnail` VARCHAR(48),
	`show_entry_id` VARCHAR(20),
	`intro_id` INTEGER,
	`views` INTEGER default 0,
	`votes` INTEGER default 0,
	`comments` INTEGER default 0,
	`favorites` INTEGER default 0,
	`rank` INTEGER default 0,
	`entries` INTEGER default 0,
	`contributors` INTEGER default 0,
	`subscribers` INTEGER default 0,
	`number_of_updates` INTEGER default 0,
	`tags` TEXT,
	`custom_data` TEXT,
	`indexed_custom_data_1` INTEGER,
	`indexed_custom_data_2` INTEGER,
	`indexed_custom_data_3` VARCHAR(256),
	`reoccurence` INTEGER,
	`license_type` INTEGER,
	`length_in_msecs` INTEGER default 0,
	`view_permissions` INTEGER,
	`view_password` VARCHAR(40),
	`contrib_permissions` INTEGER,
	`contrib_password` VARCHAR(40),
	`edit_permissions` INTEGER,
	`edit_password` VARCHAR(40),
	`salt` VARCHAR(32),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER default 0,
	`display_in_search` TINYINT,
	`subp_id` INTEGER default 0,
	`search_text` VARCHAR(4096),
	`permissions` VARCHAR(1024),
	`group_id` VARCHAR(64),
	`plays` INTEGER default 0,
	`partner_data` VARCHAR(4096),
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	KEY `views_index`(`views`),
	KEY `votes_index`(`votes`),
	KEY `created_at_index`(`created_at`),
	KEY `type_index`(`type`),
	KEY `indexed_custom_data_1_index`(`indexed_custom_data_1`),
	KEY `indexed_custom_data_2_index`(`indexed_custom_data_2`),
	KEY `indexed_custom_data_3_index`(`indexed_custom_data_3`),
	KEY `producer_id_index`(`producer_id`),
	KEY `display_in_search_index`(`display_in_search`),
	KEY `partner_group_index`(`partner_id`, `group_id`),
	CONSTRAINT `kshow_FK_1`
		FOREIGN KEY (`producer_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `entry`;


CREATE TABLE `entry`
(
	`id` VARCHAR(20)  NOT NULL,
	`kshow_id` VARCHAR(20),
	`kuser_id` INTEGER,
	`name` VARCHAR(60),
	`type` SMALLINT,
	`media_type` SMALLINT,
	`data` VARCHAR(48),
	`thumbnail` VARCHAR(48),
	`views` INTEGER default 0,
	`votes` INTEGER default 0,
	`comments` INTEGER default 0,
	`favorites` INTEGER default 0,
	`total_rank` INTEGER default 0,
	`rank` INTEGER default 0,
	`tags` TEXT,
	`anonymous` TINYINT,
	`status` INTEGER,
	`source` SMALLINT,
	`source_id` VARCHAR(48),
	`source_link` VARCHAR(1024),
	`license_type` SMALLINT,
	`credit` VARCHAR(1024),
	`length_in_msecs` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER default 0,
	`display_in_search` TINYINT,
	`subp_id` INTEGER default 0,
	`custom_data` TEXT,
	`search_text` VARCHAR(4096),
	`screen_name` VARCHAR(20),
	`site_url` VARCHAR(256),
	`permissions` INTEGER default 1,
	`group_id` VARCHAR(64),
	`plays` INTEGER default 0,
	`partner_data` VARCHAR(4096),
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`indexed_custom_data_1` INTEGER,
	`description` TEXT,
	`media_date` DATETIME,
	`admin_tags` TEXT,
	`moderation_status` INTEGER,
	`moderation_count` INTEGER,
	`modified_at` DATETIME,
	`puser_id` VARCHAR(64),
	`access_control_id` INTEGER,
	`conversion_profile_id` INTEGER,
	`categories` VARCHAR(4096),
	`categories_ids` VARCHAR(1024),
	`start_date` DATETIME,
	`end_date` DATETIME,
	`search_text_discrete` VARCHAR(4096),
	`flavor_params_ids` VARCHAR(512),
	`available_from` DATETIME,
	PRIMARY KEY (`id`),
	KEY `kshow_rank_index`(`kshow_id`, `rank`),
	KEY `kshow_views_index`(`kshow_id`, `views`),
	KEY `kshow_votes_index`(`kshow_id`, `votes`),
	KEY `kshow_created_index`(`kshow_id`, `created_at`),
	KEY `views_index`(`views`),
	KEY `votes_index`(`votes`),
	KEY `display_in_search_index`(`display_in_search`),
	KEY `partner_group_index`(`partner_id`, `group_id`),
	KEY `partner_kuser_indexed_custom_data_index`(`partner_id`, `kuser_id,indexed_custom_data_1`),
	KEY `partner_status_index`(`partner_id`, `status`),
	KEY `partner_moderation_status`(`partner_id`, `moderation_status`),
	KEY `partner_modified_at_index`(`partner_id`, `modified_at`),
	CONSTRAINT `entry_FK_1`
		FOREIGN KEY (`kshow_id`)
		REFERENCES `kshow` (`id`),
	INDEX `entry_FI_2` (`kuser_id`),
	CONSTRAINT `entry_FK_2`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`),
	INDEX `entry_FI_3` (`access_control_id`),
	CONSTRAINT `entry_FK_3`
		FOREIGN KEY (`access_control_id`)
		REFERENCES `access_control` (`id`),
	INDEX `entry_FI_4` (`conversion_profile_id`),
	CONSTRAINT `entry_FK_4`
		FOREIGN KEY (`conversion_profile_id`)
		REFERENCES `conversion_profile_2` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kvote
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kvote`;


CREATE TABLE `kvote`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kshow_id` VARCHAR(20),
	`entry_id` VARCHAR(20),
	`kuser_id` INTEGER,
	`rank` INTEGER,
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `kshow_index`(`kshow_id`),
	KEY `entry_user_index`(`entry_id`),
	CONSTRAINT `kvote_FK_1`
		FOREIGN KEY (`kshow_id`)
		REFERENCES `kshow` (`id`),
	CONSTRAINT `kvote_FK_2`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `kvote_FI_3` (`kuser_id`),
	CONSTRAINT `kvote_FK_3`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kshow` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- comment
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `comment`;


CREATE TABLE `comment`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kuser_id` INTEGER,
	`comment_type` INTEGER,
	`subject_id` INTEGER,
	`base_date` DATE,
	`reply_to` INTEGER,
	`comment` VARCHAR(256),
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `subject_created_index`(`comment_type`, `subject_id`, `base_date`, `reply_to`, `created_at`),
	INDEX `comment_FI_1` (`kuser_id`),
	CONSTRAINT `comment_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- flag
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `flag`;


CREATE TABLE `flag`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kuser_id` INTEGER,
	`subject_type` INTEGER,
	`subject_id` INTEGER,
	`flag_type` INTEGER,
	`other` VARCHAR(60),
	`comment` VARCHAR(2048),
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `subject_created_index`(`subject_type`, `subject_id`, `created_at`),
	INDEX `flag_FI_1` (`kuser_id`),
	CONSTRAINT `flag_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- alert
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `alert`;


CREATE TABLE `alert`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kuser_id` INTEGER,
	`alert_type` INTEGER,
	`subject_id` INTEGER,
	`rule_type` INTEGER,
	PRIMARY KEY (`id`),
	KEY `kuser_index`(`kuser_id`),
	KEY `subject_index`(`alert_type`, `subject_id`),
	CONSTRAINT `alert_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- favorite
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `favorite`;


CREATE TABLE `favorite`
(
	`kuser_id` INTEGER,
	`subject_type` INTEGER,
	`subject_id` INTEGER,
	`privacy` INTEGER,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	KEY `kuser_index`(`kuser_id`),
	KEY `subject_index`(`subject_type`, `subject_id`),
	CONSTRAINT `favorite_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kshow_kuser
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kshow_kuser`;


CREATE TABLE `kshow_kuser`
(
	`kshow_id` VARCHAR(20),
	`kuser_id` INTEGER,
	`subscription_type` INTEGER,
	`alert_type` INTEGER,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	KEY `kshow_index`(`kshow_id`),
	KEY `kuser_index`(`kuser_id`),
	KEY `subscription_index`(`kshow_id`, `subscription_type`),
	CONSTRAINT `kshow_kuser_FK_1`
		FOREIGN KEY (`kshow_id`)
		REFERENCES `kshow` (`id`),
	CONSTRAINT `kshow_kuser_FK_2`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- keyword
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `keyword`;


CREATE TABLE `keyword`
(
	`word` VARCHAR(30)  NOT NULL,
	`entity_id` INTEGER,
	`entity_type` INTEGER,
	`entity_columns` VARCHAR(30),
	PRIMARY KEY (`word`),
	KEY `word_index`(`word`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- tagword_count
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tagword_count`;


CREATE TABLE `tagword_count`
(
	`tag` VARCHAR(30)  NOT NULL,
	`tag_count` INTEGER,
	PRIMARY KEY (`tag`),
	KEY `count_index`(`tag_count`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- mail_job
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `mail_job`;


CREATE TABLE `mail_job`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`mail_type` SMALLINT,
	`mail_priority` SMALLINT,
	`recipient_name` VARCHAR(64),
	`recipient_email` VARCHAR(64),
	`recipient_id` INTEGER,
	`from_name` VARCHAR(64),
	`from_email` VARCHAR(64),
	`body_params` VARCHAR(2048),
	`subject_params` VARCHAR(512),
	`template_path` VARCHAR(512),
	`culture` TINYINT,
	`status` TINYINT,
	`created_at` DATETIME,
	`campaign_id` INTEGER,
	`min_send_date` DATETIME,
	`scheduler_id` INTEGER default null,
	`worker_id` INTEGER default null,
	`batch_index` INTEGER default null,
	`processor_expiration` DATETIME,
	`execution_attempts` TINYINT,
	`lock_version` INTEGER,
	`partner_id` INTEGER default 0,
	`updated_at` DATETIME,
	`dc` VARCHAR(2),
	PRIMARY KEY (`id`),
	KEY `mail_job_index`(`mail_priority`, `created_at`),
	KEY `recipient_id_index`(`recipient_id`),
	KEY `campaign_id_index`(`campaign_id`),
	KEY `partner_id_index`(`partner_id`),
	CONSTRAINT `mail_job_FK_1`
		FOREIGN KEY (`recipient_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler`;


CREATE TABLE `scheduler`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`configured_id` INTEGER,
	`name` VARCHAR(20) default '',
	`description` VARCHAR(20) default '',
	`statuses` VARCHAR(255) default '',
	`last_status` DATETIME,
	`host` VARCHAR(63) default '',
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler_worker
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler_worker`;


CREATE TABLE `scheduler_worker`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`configured_id` INTEGER,
	`type` SMALLINT,
	`name` VARCHAR(20) default '',
	`description` VARCHAR(20) default '',
	`statuses` VARCHAR(255) default '',
	`last_status` DATETIME,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler_status
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler_status`;


CREATE TABLE `scheduler_status`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`worker_id` INTEGER default null,
	`worker_configured_id` INTEGER default null,
	`worker_type` SMALLINT default null,
	`type` SMALLINT,
	`value` INTEGER,
	PRIMARY KEY (`id`),
	KEY `status_type_index`(`type`),
	KEY `status_created_at_index`(`created_at`),
	KEY `scheduler_id_index`(`scheduler_id`),
	KEY `worker_id_index_type`(`worker_id`, `worker_type`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler_config
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler_config`;


CREATE TABLE `scheduler_config`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`command_id` INTEGER default null,
	`command_status` TINYINT,
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`scheduler_name` VARCHAR(20),
	`worker_id` INTEGER default null,
	`worker_configured_id` INTEGER default null,
	`worker_name` VARCHAR(50) default 'null',
	`variable` VARCHAR(100),
	`variable_part` VARCHAR(100),
	`value` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `status_variable_index`(`variable`, `variable_part`),
	KEY `status_created_at_index`(`created_at`),
	KEY `scheduler_id_index`(`scheduler_id`),
	KEY `worker_id_index_type`(`worker_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- control_panel_command
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `control_panel_command`;


CREATE TABLE `control_panel_command`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`created_by_id` INTEGER,
	`scheduler_id` INTEGER default null,
	`scheduler_configured_id` INTEGER,
	`worker_id` INTEGER default null,
	`worker_configured_id` INTEGER default null,
	`worker_name` VARCHAR(50) default 'null',
	`batch_index` INTEGER default null,
	`type` SMALLINT,
	`target_type` SMALLINT,
	`status` SMALLINT,
	`cause` VARCHAR(255),
	`description` VARCHAR(255),
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- batch_job
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `batch_job`;


CREATE TABLE `batch_job`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`job_type` SMALLINT,
	`job_sub_type` SMALLINT,
	`data` VARCHAR(4096),
	`file_size` INTEGER default null,
	`duplication_key` VARCHAR(2047),
	`status` INTEGER,
	`abort` TINYINT,
	`check_again_timeout` INTEGER,
	`message` VARCHAR(1024),
	`description` VARCHAR(1024),
	`updates_count` SMALLINT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`deleted_at` DATETIME,
	`priority` TINYINT,
	`work_group_id` INTEGER,
	`queue_time` DATETIME,
	`finish_time` DATETIME,
	`entry_id` VARCHAR(20) default '',
	`partner_id` INTEGER default 0,
	`subp_id` INTEGER default 0,
	`scheduler_id` INTEGER,
	`worker_id` INTEGER,
	`batch_index` INTEGER,
	`last_scheduler_id` INTEGER,
	`last_worker_id` INTEGER,
	`last_worker_remote` INTEGER,
	`processor_expiration` DATETIME,
	`execution_attempts` TINYINT,
	`lock_version` INTEGER,
	`bulk_job_id` INTEGER default null,
	`root_job_id` INTEGER default null,
	`parent_job_id` INTEGER default null,
	`dc` INTEGER,
	`err_type` INTEGER,
	`err_number` INTEGER,
	PRIMARY KEY (`id`),
	KEY `status_job_type_index`(`status`, `job_type`),
	KEY `entry_id_index_id`(`entry_id`, `id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `work_group_id_index_priority`(`work_group_id`, `priority`),
	KEY `bulk_job_id_index`(`bulk_job_id`),
	KEY `root_job_id_index`(`root_job_id`),
	KEY `parent_job_id_index`(`parent_job_id`),
	KEY `execution_attempts_index`(`job_type`, `execution_attempts`),
	KEY `processor_expiration_index`(`job_type`, `processor_expiration`),
	KEY `lock_index`(`batch_index`, `scheduler_id`, `worker_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- priority_group
#-----------------------------------------------------------------------------

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
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- work_group
#-----------------------------------------------------------------------------

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
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- bulk_upload_result
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `bulk_upload_result`;


CREATE TABLE `bulk_upload_result`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`bulk_upload_job_id` INTEGER,
	`line_index` INTEGER,
	`partner_id` INTEGER,
	`entry_id` VARCHAR(20),
	`entry_status` INTEGER,
	`row_data` VARCHAR(1023),
	`title` VARCHAR(127),
	`description` VARCHAR(255),
	`tags` VARCHAR(255),
	`url` VARCHAR(255),
	`content_type` VARCHAR(31),
	`conversion_profile_id` INTEGER,
	`access_control_profile_id` INTEGER,
	`category` VARCHAR(128),
	`schedule_start_date` DATETIME,
	`schedule_end_date` DATETIME,
	`thumbnail_url` VARCHAR(255),
	`thumbnail_saved` INTEGER,
	`partner_data` VARCHAR(4096),
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `entry_id_index_id`(`entry_id`, `id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- blocked_email
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `blocked_email`;


CREATE TABLE `blocked_email`
(
	`email` VARCHAR(40)  NOT NULL,
	PRIMARY KEY (`email`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- conversion
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `conversion`;


CREATE TABLE `conversion`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`entry_id` VARCHAR(20),
	`in_file_name` VARCHAR(128),
	`in_file_ext` VARCHAR(16),
	`in_file_size` INTEGER,
	`source` INTEGER,
	`status` INTEGER,
	`conversion_params` VARCHAR(512),
	`out_file_name` VARCHAR(128),
	`out_file_size` INTEGER,
	`out_file_name_2` VARCHAR(128),
	`out_file_size_2` INTEGER,
	`conversion_time` INTEGER,
	`total_process_time` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `entry_id_index`(`entry_id`),
	CONSTRAINT `conversion_FK_1`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- flickr_token
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `flickr_token`;


CREATE TABLE `flickr_token`
(
	`kalt_token` VARCHAR(256)  NOT NULL,
	`frob` VARCHAR(64),
	`token` VARCHAR(64),
	`nsid` VARCHAR(64),
	`response` VARCHAR(512),
	`is_valid` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`kalt_token`),
	KEY `is_valid_index`(`is_valid`, `kalt_token`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- puser_kuser
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `puser_kuser`;


CREATE TABLE `puser_kuser`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`puser_id` VARCHAR(64),
	`kuser_id` INTEGER,
	`puser_name` VARCHAR(64),
	`custom_data` VARCHAR(1024),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`context` VARCHAR(1024),
	`subp_id` INTEGER default 0,
	PRIMARY KEY (`id`),
	KEY `partner_puser_index`(`partner_id`, `puser_id`),
	KEY `kuser_id_index`(`kuser_id`),
	INDEX `I_referenced_puser_role_FK_3_1` (`puser_id`),
	CONSTRAINT `puser_kuser_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- puser_role
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `puser_role`;


CREATE TABLE `puser_role`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kshow_id` VARCHAR(20),
	`partner_id` INTEGER,
	`puser_id` VARCHAR(64),
	`role` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`subp_id` INTEGER default 0,
	PRIMARY KEY (`id`),
	KEY `partner_puser_index`(`partner_id`, `puser_id`),
	KEY `kshow_id_index`(`kshow_id`),
	CONSTRAINT `puser_role_FK_1`
		FOREIGN KEY (`kshow_id`)
		REFERENCES `kshow` (`id`),
	CONSTRAINT `puser_role_FK_2`
		FOREIGN KEY (`partner_id`)
		REFERENCES `puser_kuser` (`partner_id`),
	INDEX `puser_role_FI_3` (`puser_id`),
	CONSTRAINT `puser_role_FK_3`
		FOREIGN KEY (`puser_id`)
		REFERENCES `puser_kuser` (`puser_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- facebook_invite
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `facebook_invite`;


CREATE TABLE `facebook_invite`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`puser_id` VARCHAR(64),
	`invited_puser_id` VARCHAR(64),
	`status` SMALLINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `puser_id_index`(`puser_id`),
	KEY `invited_puser_id_index`(`invited_puser_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- partner
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `partner`;


CREATE TABLE `partner`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_name` VARCHAR(256),
	`partner_alias` VARCHAR(64),
	`url1` VARCHAR(1024),
	`url2` VARCHAR(1024),
	`secret` VARCHAR(50),
	`admin_secret` VARCHAR(50),
	`max_number_of_hits_per_day` INTEGER default -1,
	`appear_in_search` INTEGER default 2,
	`debug_level` INTEGER default 0,
	`invalid_login_count` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`anonymous_kuser_id` INTEGER,
	`ks_max_expiry_in_seconds` INTEGER default 86400,
	`create_user_on_demand` TINYINT default 1,
	`prefix` VARCHAR(32),
	`admin_name` VARCHAR(50),
	`admin_email` VARCHAR(50),
	`description` VARCHAR(1024),
	`commercial_use` TINYINT default 0,
	`moderate_content` TINYINT default 0,
	`notify` TINYINT default 0,
	`custom_data` TEXT,
	`service_config_id` VARCHAR(64),
	`status` TINYINT default 1,
	`content_categories` VARCHAR(1024),
	`type` TINYINT default 1,
	`phone` VARCHAR(64),
	`describe_yourself` VARCHAR(64),
	`adult_content` TINYINT default 0,
	`partner_package` TINYINT default 1,
	`usage_percent` INTEGER default 0,
	`storage_usage` INTEGER default 0,
	`eighty_percent_warning` INTEGER,
	`usage_limit_warning` INTEGER,
	`monitor_usage` INTEGER default 1,
	`priority_group_id` INTEGER,
	`work_group_id` INTEGER,
	`partner_group_type` SMALLINT default 1,
	`partner_parent_id` INTEGER default null,
	`kmc_version` VARCHAR(15) default '1',
	PRIMARY KEY (`id`),
	KEY `partner_alias_index`(`partner_alias`),
	KEY `partner_parent_index`(`partner_parent_id`),
	INDEX `partner_FI_1` (`anonymous_kuser_id`),
	CONSTRAINT `partner_FK_1`
		FOREIGN KEY (`anonymous_kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- pr_news
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `pr_news`;


CREATE TABLE `pr_news`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`pr_order` INTEGER,
	`image_path` VARCHAR(256),
	`href` VARCHAR(1024),
	`text` VARCHAR(1024),
	`alt` VARCHAR(256),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`press_date` VARCHAR(128),
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- email_campaign
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `email_campaign`;


CREATE TABLE `email_campaign`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`criteria_id` SMALLINT,
	`criteria_str` VARCHAR(1024),
	`criteria_params` VARCHAR(1024),
	`template_path` VARCHAR(256),
	`campaign_mgr_kuser_id` INTEGER,
	`send_count` INTEGER,
	`open_count` INTEGER,
	`click_count` INTEGER,
	`status` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `campaign_mgr_kuser_id_index`(`campaign_mgr_kuser_id`),
	CONSTRAINT `email_campaign_FK_1`
		FOREIGN KEY (`campaign_mgr_kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- widget_log
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `widget_log`;


CREATE TABLE `widget_log`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kshow_id` VARCHAR(20),
	`entry_id` VARCHAR(20),
	`kmedia_type` INTEGER,
	`widget_type` VARCHAR(32),
	`referer` VARCHAR(1024),
	`views` INTEGER default 0,
	`ip1` INTEGER,
	`ip1_count` INTEGER default 0,
	`ip2` INTEGER,
	`ip2_count` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`plays` INTEGER default 0,
	`partner_id` INTEGER default 0,
	`subp_id` INTEGER default 0,
	PRIMARY KEY (`id`),
	KEY `referer_index`(`referer`),
	KEY `entry_id_kshow_id_index`(`entry_id`, `kshow_id`),
	KEY `partner_id_subp_id_index`(`partner_id`, `subp_id`),
	CONSTRAINT `widget_log_FK_1`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- partnership
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `partnership`;


CREATE TABLE `partnership`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partnership_order` INTEGER,
	`image_path` VARCHAR(256),
	`href` VARCHAR(1024),
	`text` VARCHAR(1024),
	`alt` VARCHAR(256),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partnership_date` VARCHAR(128),
	PRIMARY KEY (`id`),
	KEY `partnership_order_index`(`partnership_order`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- admin_kuser
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `admin_kuser`;


CREATE TABLE `admin_kuser`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`screen_name` VARCHAR(20),
	`full_name` VARCHAR(40),
	`email` VARCHAR(50),
	`sha1_password` VARCHAR(40),
	`salt` VARCHAR(32),
	`picture` VARCHAR(48),
	`icon` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	PRIMARY KEY (`id`),
	KEY `screen_name_index`(`screen_name`),
	INDEX `admin_kuser_FI_1` (`partner_id`),
	CONSTRAINT `admin_kuser_FK_1`
		FOREIGN KEY (`partner_id`)
		REFERENCES `partner` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- admin_permission
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `admin_permission`;


CREATE TABLE `admin_permission`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`groups` VARCHAR(512),
	`admin_kuser_id` INTEGER,
	PRIMARY KEY (`id`),
	INDEX `admin_permission_FI_1` (`admin_kuser_id`),
	CONSTRAINT `admin_permission_FK_1`
		FOREIGN KEY (`admin_kuser_id`)
		REFERENCES `admin_kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- notification
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `notification`;


CREATE TABLE `notification`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`puser_id` VARCHAR(64),
	`type` SMALLINT,
	`object_id` VARCHAR(20),
	`status` INTEGER,
	`notification_data` VARCHAR(4096),
	`number_of_attempts` SMALLINT default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`notification_result` VARCHAR(256),
	`object_type` SMALLINT,
	`scheduler_id` INTEGER default null,
	`worker_id` INTEGER default null,
	`batch_index` INTEGER default null,
	`processor_expiration` DATETIME,
	`execution_attempts` TINYINT,
	`lock_version` INTEGER,
	`dc` VARCHAR(2),
	PRIMARY KEY (`id`),
	KEY `status_partner_id_index`(`status`, `partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- moderation
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `moderation`;


CREATE TABLE `moderation`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` SMALLINT,
	`kuser_id` INTEGER,
	`puser_id` VARCHAR(64),
	`status` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`comments` VARCHAR(1024),
	`group_id` VARCHAR(64),
	`report_code` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `partner_id_group_id_status_index`(`partner_id`, `group_id`, `status`),
	KEY `object_index`(`partner_id`, `status`, `object_id`, `object_type`),
	INDEX `moderation_FI_1` (`kuser_id`),
	CONSTRAINT `moderation_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- moderation_flag
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `moderation_flag`;


CREATE TABLE `moderation_flag`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`kuser_id` INTEGER,
	`object_type` SMALLINT,
	`flagged_entry_id` VARCHAR(20),
	`flagged_kuser_id` INTEGER,
	`status` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`comments` VARCHAR(1024),
	`flag_type` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `entry_object_index`(`partner_id`, `status`, `object_type`, `flagged_kuser_id`),
	INDEX `moderation_flag_FI_1` (`kuser_id`),
	CONSTRAINT `moderation_flag_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`),
	INDEX `moderation_flag_FI_2` (`flagged_entry_id`),
	CONSTRAINT `moderation_flag_FK_2`
		FOREIGN KEY (`flagged_entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `moderation_flag_FI_3` (`flagged_kuser_id`),
	CONSTRAINT `moderation_flag_FK_3`
		FOREIGN KEY (`flagged_kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- roughcut_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `roughcut_entry`;


CREATE TABLE `roughcut_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`roughcut_id` VARCHAR(20),
	`roughcut_version` INTEGER,
	`roughcut_kshow_id` VARCHAR(20),
	`entry_id` VARCHAR(20),
	`partner_id` INTEGER,
	`op_type` SMALLINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `entry_id_index`(`entry_id`),
	KEY `roughcut_id_index`(`roughcut_id`),
	KEY `roughcut_kshow_id_index`(`roughcut_kshow_id`),
	CONSTRAINT `roughcut_entry_FK_1`
		FOREIGN KEY (`roughcut_id`)
		REFERENCES `entry` (`id`),
	CONSTRAINT `roughcut_entry_FK_2`
		FOREIGN KEY (`roughcut_kshow_id`)
		REFERENCES `kshow` (`id`),
	CONSTRAINT `roughcut_entry_FK_3`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- widget
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `widget`;


CREATE TABLE `widget`
(
	`id` VARCHAR(32)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`source_widget_id` VARCHAR(32),
	`root_widget_id` VARCHAR(32),
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`kshow_id` VARCHAR(20),
	`entry_id` VARCHAR(20),
	`ui_conf_id` INTEGER,
	`custom_data` VARCHAR(1024),
	`security_type` SMALLINT,
	`security_policy` SMALLINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_data` VARCHAR(4096),
	PRIMARY KEY (`id`),
	KEY `int_id_index`(`int_id`),
	INDEX `widget_FI_1` (`kshow_id`),
	CONSTRAINT `widget_FK_1`
		FOREIGN KEY (`kshow_id`)
		REFERENCES `kshow` (`id`),
	INDEX `widget_FI_2` (`entry_id`),
	CONSTRAINT `widget_FK_2`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `widget_FI_3` (`ui_conf_id`),
	CONSTRAINT `widget_FK_3`
		FOREIGN KEY (`ui_conf_id`)
		REFERENCES `ui_conf` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kwidget_log
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kwidget_log`;


CREATE TABLE `kwidget_log`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`widget_id` VARCHAR(32),
	`source_widget_id` VARCHAR(32),
	`root_widget_id` VARCHAR(32),
	`kshow_id` VARCHAR(20),
	`entry_id` VARCHAR(20),
	`ui_conf_id` INTEGER,
	`referer` VARCHAR(1024),
	`views` INTEGER default 0,
	`ip1` INTEGER,
	`ip1_count` INTEGER default 0,
	`ip2` INTEGER,
	`ip2_count` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`plays` INTEGER default 0,
	`partner_id` INTEGER default 0,
	`subp_id` INTEGER default 0,
	PRIMARY KEY (`id`),
	KEY `referer_index`(`referer`),
	KEY `entry_id_kshow_id_index`(`entry_id`, `kshow_id`),
	KEY `partner_id_subp_id_index`(`partner_id`, `subp_id`),
	INDEX `kwidget_log_FI_1` (`widget_id`),
	CONSTRAINT `kwidget_log_FK_1`
		FOREIGN KEY (`widget_id`)
		REFERENCES `widget` (`id`),
	INDEX `kwidget_log_FI_2` (`kshow_id`),
	CONSTRAINT `kwidget_log_FK_2`
		FOREIGN KEY (`kshow_id`)
		REFERENCES `kshow` (`id`),
	CONSTRAINT `kwidget_log_FK_3`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `kwidget_log_FI_4` (`ui_conf_id`),
	CONSTRAINT `kwidget_log_FK_4`
		FOREIGN KEY (`ui_conf_id`)
		REFERENCES `ui_conf` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- ui_conf
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ui_conf`;


CREATE TABLE `ui_conf`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`obj_type` SMALLINT,
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`conf_file_path` VARCHAR(128),
	`name` VARCHAR(128),
	`width` VARCHAR(10),
	`height` VARCHAR(10),
	`html_params` VARCHAR(256),
	`swf_url` VARCHAR(256),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`conf_vars` VARCHAR(4096),
	`use_cdn` TINYINT,
	`tags` TEXT,
	`custom_data` TEXT,
	`status` INTEGER,
	`description` VARCHAR(4096),
	`display_in_search` TINYINT,
	`creation_mode` TINYINT,
	`version` VARCHAR(10),
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `partner_id_creation_mode_index`(`partner_id,creation_mode`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- partner_stats
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `partner_stats`;


CREATE TABLE `partner_stats`
(
	`partner_id` INTEGER  NOT NULL,
	`views` INTEGER,
	`plays` INTEGER,
	`videos` INTEGER,
	`audios` INTEGER,
	`images` INTEGER,
	`entries` INTEGER,
	`users_1` INTEGER,
	`users_2` INTEGER,
	`rc_1` INTEGER,
	`rc_2` INTEGER,
	`kshows_1` INTEGER,
	`kshows_2` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	`widgets` INTEGER,
	PRIMARY KEY (`partner_id`),
	KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- partner_activity
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `partner_activity`;


CREATE TABLE `partner_activity`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`activity_date` DATE,
	`activity` INTEGER,
	`sub_activity` INTEGER,
	`amount` BIGINT,
	`amount1` BIGINT,
	`amount2` BIGINT,
	`amount3` INTEGER,
	`amount4` INTEGER,
	`amount5` INTEGER,
	`amount6` INTEGER,
	`amount7` INTEGER,
	`amount9` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- conversion_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `conversion_profile`;


CREATE TABLE `conversion_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER default 0,
	`enabled` TINYINT,
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
	`profile_type_suffix` VARCHAR(32),
	`conversion_profile_2_id` INTEGER,
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
	`enabled` TINYINT,
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

#-----------------------------------------------------------------------------
#-- partner_transactions
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `partner_transactions`;


CREATE TABLE `partner_transactions`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`created_at` DATETIME,
	`amount` FLOAT,
	`currency` VARCHAR(6),
	`transaction_id` VARCHAR(17),
	`timestamp` DATETIME,
	`correlation_id` VARCHAR(12),
	`ack` VARCHAR(20),
	`transaction_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kce_installation_error
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kce_installation_error`;


CREATE TABLE `kce_installation_error`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`browser` VARCHAR(100),
	`server_ip` VARCHAR(20),
	`server_os` VARCHAR(100),
	`php_version` VARCHAR(20),
	`ce_admin_email` VARCHAR(50),
	`type` VARCHAR(50),
	`description` VARCHAR(100),
	`data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `server_os_index`(`server_os`),
	KEY `php_version_index`(`php_version`),
	KEY `type_index`(`type`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- file_sync
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `file_sync`;


CREATE TABLE `file_sync`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`object_type` TINYINT,
	`object_id` VARCHAR(20),
	`version` VARCHAR(20),
	`object_sub_type` TINYINT,
	`dc` INTEGER,
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
	KEY `object_id_object_type_version_subtype_index`(`object_id`, `object_type`, `version`, `object_sub_type`),
	KEY `partner_id_object_id_object_type_index`(`partner_id`, `object_id`, `object_type`),
	KEY `dc_status_index`(`dc`, `status`),
	KEY `linked_index`(`linked_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- access_control
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `access_control`;


CREATE TABLE `access_control`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`description` VARCHAR(1024) default '' NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	`site_restrict_type` TINYINT,
	`site_restrict_list` VARCHAR(1024),
	`country_restrict_type` TINYINT,
	`country_restrict_list` VARCHAR(1024),
	`ks_restrict_privilege` VARCHAR(20),
	`prv_restrict_privilege` VARCHAR(20),
	`prv_restrict_length` INTEGER,
	`kdir_restrict_type` TINYINT,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- media_info
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `media_info`;


CREATE TABLE `media_info`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`flavor_asset_id` VARCHAR(20) default 'null',
	`file_size` INTEGER  NOT NULL,
	`container_format` VARCHAR(127),
	`container_id` VARCHAR(127),
	`container_profile` VARCHAR(127),
	`container_duration` INTEGER,
	`container_bit_rate` INTEGER,
	`video_format` VARCHAR(127),
	`video_codec_id` VARCHAR(127),
	`video_duration` INTEGER,
	`video_bit_rate` INTEGER,
	`video_bit_rate_mode` TINYINT,
	`video_width` INTEGER  NOT NULL,
	`video_height` INTEGER  NOT NULL,
	`video_frame_rate` FLOAT,
	`video_dar` FLOAT,
	`video_rotation` INTEGER,
	`audio_format` VARCHAR(127),
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
	`multi_stream_info` VARCHAR(1023),
	`flavor_asset_version` VARCHAR(20),
	`scan_type` INTEGER,
	`multi_stream` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `flavor_asset_id_index`(`flavor_asset_id`),
	CONSTRAINT `media_info_FK_1`
		FOREIGN KEY (`flavor_asset_id`)
		REFERENCES `flavor_asset` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- flavor_params
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `flavor_params`;


CREATE TABLE `flavor_params`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`version` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
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
	`audio_resolution` INTEGER default 0,
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`frame_rate` FLOAT default 0 NOT NULL,
	`gop_size` INTEGER default 0 NOT NULL,
	`two_pass` INTEGER default 0 NOT NULL,
	`conversion_engines` VARCHAR(1024),
	`conversion_engines_extra_params` VARCHAR(1024),
	`custom_data` TEXT,
	`view_order` INTEGER,
	`creation_mode` SMALLINT default 1,
	`deinterlice` INTEGER,
	`rotate` INTEGER,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- flavor_params_output
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `flavor_params_output`;


CREATE TABLE `flavor_params_output`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`flavor_params_id` INTEGER  NOT NULL,
	`flavor_params_version` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`entry_id` VARCHAR(20)  NOT NULL,
	`flavor_asset_id` VARCHAR(20)  NOT NULL,
	`flavor_asset_version` VARCHAR(20),
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
	`audio_codec` VARCHAR(20) default 'null',
	`audio_bitrate` INTEGER default null,
	`audio_channels` TINYINT default null,
	`audio_sample_rate` INTEGER default null,
	`audio_resolution` INTEGER default null,
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`frame_rate` FLOAT default null,
	`gop_size` INTEGER default 0 NOT NULL,
	`two_pass` INTEGER default 0 NOT NULL,
	`conversion_engines` VARCHAR(1024),
	`conversion_engines_extra_params` VARCHAR(1024),
	`custom_data` TEXT,
	`command_lines` VARCHAR(2047),
	`file_ext` VARCHAR(4),
	`deinterlice` INTEGER,
	`rotate` INTEGER,
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

#-----------------------------------------------------------------------------
#-- flavor_asset
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `flavor_asset`;


CREATE TABLE `flavor_asset`
(
	`id` VARCHAR(20)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	`entry_id` VARCHAR(20)  NOT NULL,
	`flavor_params_id` INTEGER  NOT NULL,
	`status` TINYINT,
	`version` VARCHAR(20),
	`description` VARCHAR(255),
	`width` INTEGER default 0 NOT NULL,
	`height` INTEGER default 0 NOT NULL,
	`bitrate` INTEGER default 0 NOT NULL,
	`frame_rate` FLOAT default 0 NOT NULL,
	`size` INTEGER default 0 NOT NULL,
	`is_original` INTEGER default 0,
	`file_ext` VARCHAR(4),
	`container_format` VARCHAR(127),
	`video_codec_id` VARCHAR(127),
	PRIMARY KEY (`int_id`),
	INDEX `I_referenced_media_info_FK_1_1` (`id`),
	INDEX `I_referenced_flavor_params_output_FK_3_2` (`id`),
	INDEX `flavor_asset_FI_1` (`entry_id`),
	CONSTRAINT `flavor_asset_FK_1`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`),
	INDEX `flavor_asset_FI_2` (`flavor_params_id`),
	CONSTRAINT `flavor_asset_FK_2`
		FOREIGN KEY (`flavor_params_id`)
		REFERENCES `flavor_params` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- conversion_profile_2
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `conversion_profile_2`;


CREATE TABLE `conversion_profile_2`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	`description` VARCHAR(1024) default '' NOT NULL,
	`crop_left` INTEGER default -1 NOT NULL,
	`crop_top` INTEGER default -1 NOT NULL,
	`crop_width` INTEGER default -1 NOT NULL,
	`crop_height` INTEGER default -1 NOT NULL,
	`clip_start` INTEGER default -1 NOT NULL,
	`clip_duration` INTEGER default -1 NOT NULL,
	`input_tags_map` VARCHAR(1023) default 'null',
	`creation_mode` SMALLINT default 1,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- flavor_params_conversion_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `flavor_params_conversion_profile`;


CREATE TABLE `flavor_params_conversion_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`conversion_profile_id` INTEGER  NOT NULL,
	`flavor_params_id` INTEGER  NOT NULL,
	`ready_behavior` TINYINT  NOT NULL,
	`force_none_complied` INTEGER,
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
		REFERENCES `flavor_params` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- category
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `category`;


CREATE TABLE `category`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER  NOT NULL,
	`depth` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`full_name` VARCHAR(512) default '' NOT NULL,
	`entries_count` INTEGER default 0 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_full_name_index`(`partner_id`, `full_name`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- syndication_feed
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `syndication_feed`;


CREATE TABLE `syndication_feed`
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
)Type=MyISAM;

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
	KEY `partner_event_type_indx`(`partner_id`, `track_event_type_id`),
	KEY `entry_id_indx`(`entry_id`),
	KEY `track_event_type_id_indx`(`track_event_type_id`),
	KEY `param_1_indx`(`param_1_str`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- system_user
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `system_user`;


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
	`is_primary` INTEGER default 0,
	`status_updated_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	`role` VARCHAR(40),
	PRIMARY KEY (`id`),
	UNIQUE KEY `system_user_email_unique` (`email`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- audit_trail
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `audit_trail`;


CREATE TABLE `audit_trail`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`object_name` VARCHAR(20)  NOT NULL,
	`object_type_id` VARCHAR(20)  NOT NULL,
	`object_id` VARCHAR(20)  NOT NULL,
	`partner_id` INTEGER,
	`uid` VARCHAR(63),
	`ks_partner_id` INTEGER,
	`ks_uid` VARCHAR(63),
	`before` VARCHAR(2047),
	`after` VARCHAR(2047),
	`context` VARCHAR(127),
	`host_name` VARCHAR(20),
	`action_id` TINYINT,
	`created_at` DATETIME,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- storage_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `storage_profile`;


CREATE TABLE `storage_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`desciption` VARCHAR(127),
	`status` TINYINT,
	`protocol` TINYINT,
	`storage_url` VARCHAR(127),
	`storage_base_dir` VARCHAR(127),
	`storage_username` VARCHAR(31),
	`storage_password` VARCHAR(31),
	`storage_ftp_passive_mode` TINYINT,
	`delivery_http_base_url` VARCHAR(127),
	`delivery_rmp_base_url` VARCHAR(127),
	`delivery_iis_base_url` VARCHAR(127),
	`min_file_size` INTEGER,
	`max_file_size` INTEGER,
	`flavor_params_ids` VARCHAR(127),
	`max_concurrent_connections` INTEGER,
	`custom_data` TEXT,
	`path_manager_class` VARCHAR(127),
	`url_manager_class` VARCHAR(127),
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- email_ingestion_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `email_ingestion_profile`;


CREATE TABLE `email_ingestion_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(60) default '' NOT NULL,
	`description` TEXT,
	`email_address` VARCHAR(50)  NOT NULL,
	`mailbox_id` VARCHAR(50)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`conversion_profile_2_id` INTEGER,
	`moderation_status` TINYINT,
	`custom_data` TEXT,
	`status` TINYINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `email_ingestion_profile_email_address_unique` (`email_address`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- upload_token
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `upload_token`;


CREATE TABLE `upload_token`
(
	`id` VARCHAR(35)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER default 0,
	`kuser_id` INTEGER,
	`status` INTEGER,
	`file_name` VARCHAR(256),
	`file_size` BIGINT,
	`uploaded_file_size` BIGINT,
	`upload_temp_path` VARCHAR(256),
	`user_ip` VARCHAR(39)  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `int_id`(`int_id`),
	KEY `partner_id_status`(`partner_id`, `status`),
	KEY `partner_id_created_at`(`partner_id`, `created_at`),
	KEY `status_created_at`(`status`, `created_at`),
	KEY `created_at`(`created_at`),
	INDEX `upload_token_FI_1` (`kuser_id`),
	CONSTRAINT `upload_token_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- metadata_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata_profile`;


CREATE TABLE `metadata_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`partner_id` INTEGER,
	`name` VARCHAR(31),
	`status` TINYINT,
	`object_type` INTEGER,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- metadata
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `metadata`;


CREATE TABLE `metadata`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`version` INTEGER,
	`metadata_profile_id` INTEGER,
	`metadata_profile_version` INTEGER,
	`partner_id` INTEGER,
	`object_id` VARCHAR(20),
	`object_type` INTEGER,
	`status` TINYINT,
	PRIMARY KEY (`id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
