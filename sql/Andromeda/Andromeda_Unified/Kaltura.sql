use kaltura;

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
)ENGINE=MyISAM;

#-----------------------------------------------------------------------------
#-- kshow
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kshow`;


CREATE TABLE `kshow` (
  `id` varchar(20) NOT NULL default '',
  `producer_id` int(11) default NULL,
  `episode_id` varchar(20) default NULL,
  `name` varchar(60) default NULL,
  `subdomain` varchar(30) default NULL,
  `description` text,
  `status` int(11) default NULL,
  `type` int(11) default NULL,
  `media_type` int(11) default NULL,
  `format_type` int(11) default NULL,
  `language` int(11) default NULL,
  `start_date` date default NULL,
  `end_date` date default NULL,
  `skin` text,
  `thumbnail` varchar(48) default NULL,
  `show_entry_id` varchar(20) default NULL,
  `intro_id` int(11) default NULL,
  `views` int(11) default '0',
  `votes` int(11) default '0',
  `comments` int(11) default '0',
  `favorites` int(11) default '0',
  `rank` int(11) default '0',
  `entries` int(11) default '0',
  `contributors` int(11) default '0',
  `subscribers` int(11) default '0',
  `number_of_updates` int(11) default '0',
  `tags` text,
  `custom_data` text,
  `indexed_custom_data_1` int(11) default NULL,
  `indexed_custom_data_2` int(11) default NULL,
  `indexed_custom_data_3` varchar(256) default NULL,
  `reoccurence` int(11) default NULL,
  `license_type` int(11) default NULL,
  `length_in_msecs` int(11) default '0',
  `view_permissions` int(11) default NULL,
  `view_password` varchar(40) default NULL,
  `contrib_permissions` int(11) default NULL,
  `contrib_password` varchar(40) default NULL,
  `edit_permissions` int(11) default NULL,
  `edit_password` varchar(40) default NULL,
  `salt` varchar(32) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partner_id` int(11) default '0',
  `display_in_search` tinyint(4) default '1',
  `subp_id` int(11) default '0',
  `search_text` varchar(4096) default NULL,
  `permissions` varchar(1024) default NULL,
  `group_id` varchar(64) default NULL,
  `plays` int(11) default NULL,
  `partner_data` varchar(4096) default NULL,
  `int_id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `views_index` (`views`),
  KEY `votes_index` (`votes`),
  KEY `created_at_index` (`created_at`),
  KEY `type_index` (`type`),
  KEY `kshow_FI_1` (`producer_id`),
  KEY `indexed_custom_data_1_index` (`indexed_custom_data_1`),
  KEY `indexed_custom_data_2_index` (`indexed_custom_data_2`),
  KEY `indexed_custom_data_3_index` (`indexed_custom_data_3`),
  KEY `int_id_index` (`int_id`),
  FULLTEXT KEY `name` (`name`,`description`,`tags`),
  FULLTEXT KEY `search_text_index` (`search_text`)
) ENGINE=MyISAM AUTO_INCREMENT=16580 DEFAULT CHARSET=latin1;


#-----------------------------------------------------------------------------
#-- entry
#-----------------------------------------------------------------------------

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
/*
  `access_control_id` int(11) DEFAULT NULL,
  `conversion_profile_id` int(11) DEFAULT NULL,
  `categories` varchar(4096) DEFAULT NULL,
  `categories_ids` varchar(1024) DEFAULT NULL,
  `search_text_discrete` varchar(4096) DEFAULT NULL,
  `flavor_params_ids` varchar(512) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `available_from` datetime DEFAULT NULL,
*/
  PRIMARY KEY (`id`),
  KEY `kshow_rank_index` (`kshow_id`,`rank`),
  KEY `kshow_views_index` (`kshow_id`,`views`),
  KEY `kshow_votes_index` (`kshow_id`,`votes`),
  KEY `views_index` (`views`),
  KEY `votes_index` (`votes`),
  KEY `entry_FI_2` (`kuser_id`),
  KEY `partner_id_index` USING BTREE (`partner_id`,`id`),
  KEY `kshow_index` (`partner_id`,`kshow_id`,`subp_id`),
  KEY `kshow_index_2` (`partner_id`,`kshow_id`,`status`,`subp_id`),
  KEY `partner_created_at_index` (`partner_id`,`created_at`),
  KEY `partner_created_at_status_type_index` (`partner_id`,`created_at`,`status`,`type`),
  KEY `type_kuser_id_index` (`type`,`kuser_id`),
  KEY `created_index` (`created_at`),
  KEY `status_created_index` (`status`,`created_at`),
  KEY `type_status_created_index` (`type`,`status`,`created_at`),
  KEY `created_at_index` (`created_at`),
  KEY `partner_group_index` (`partner_id`,`group_id`),
  KEY `int_id_index` (`int_id`),
  KEY `partner_kuser_indexed_custom_data_index` (`partner_id`,`kuser_id`,`indexed_custom_data_1`),
  KEY `partner_status_index` (`partner_id`,`status`),
  KEY `partner_moderation_status` (`partner_id`,`moderation_status`),
  KEY `partner_modified_at_index` (`partner_id`,`modified_at`),
  KEY `partner_status_media_type_index` (`partner_id`,`status`,`media_type`),
  KEY `modified_at_index` (`modified_at`),
#  KEY `entry_FI_3` (`access_control_id`),
#  KEY `entry_FI_5` (`conversion_profile_id`),
  FULLTEXT KEY `search_text_index` (`search_text`)
#  FULLTEXT KEY `search_text_discrete_index` (`search_text_discrete`)
) ENGINE=MyISAM AUTO_INCREMENT=14742253 DEFAULT CHARSET=utf8;


/*
CREATE TABLE `entry` (
  `id` varchar(20) NOT NULL default '',
  `kshow_id` varchar(20) default NULL,
  `kuser_id` int(11) default NULL,
  `name` varchar(60) default NULL,
  `type` smallint(6) default NULL,
  `media_type` smallint(6) default NULL,
  `data` varchar(48) default NULL,
  `thumbnail` varchar(48) default NULL,
  `views` int(11) default '0',
  `votes` int(11) default '0',
  `comments` int(11) default '0',
  `favorites` int(11) default '0',
  `total_rank` int(11) default '0',
  `rank` int(11) default '0',
  `tags` text,
  `anonymous` tinyint(4) default NULL,
  `status` int(11) default NULL,
  `source` smallint(6) default NULL,
  `source_id` int(11) default NULL,
  `source_link` varchar(1024) default NULL,
  `license_type` smallint(6) default NULL,
  `credit` varchar(1024) default NULL,
  `length_in_msecs` int(11) default '0',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partner_id` int(11) default '0',
  `display_in_search` tinyint(4) default '1',
  `subp_id` int(11) default '0',
  `custom_data` text,
  `search_text` varchar(4096) default NULL,
  `screen_name` varchar(20) default NULL,
  `site_url` varchar(256) default NULL,
  `desired_status` smallint(6) default NULL,
  `permissions` int(11) default NULL,
  `group_id` varchar(64) default NULL,
  `plays` int(11) default '0',
  `partner_data` varchar(4096) default NULL,
  `int_id` int(11) NOT NULL auto_increment,
  `indexed_custom_data_1` int(11) default NULL,
  `description` text,
  `media_date` datetime default NULL,
  `admin_tags` text,
  `moderation_status` int(11) default '2',
  `moderation_count` int(11) default '0',
  `modified_at` datetime default NULL,
  `puser_id` varchar(64) default NULL,
#  `access_control_id` int(11) default NULL,
#  `conversion_profile_id` int(11) default NULL,
#  `categories` varchar(4096) default NULL,
#  `categories_ids` varchar(1024) default NULL,
#  `search_text_discrete` varchar(4096) default NULL,
#  `flavor_params_ids` varchar(512) default NULL,
#  `start_date` datetime default NULL,
#  `end_date` datetime default NULL,
#  `available_from` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `kshow_rank_index` (`kshow_id`,`rank`),
  KEY `kshow_views_index` (`kshow_id`,`views`),
  KEY `kshow_votes_index` (`kshow_id`,`votes`),
  KEY `kshow_created_index` (`kshow_id`,`created_at`),
  KEY `views_index` (`views`),
  KEY `votes_index` (`votes`),
  KEY `entry_FI_2` (`kuser_id`),
  KEY `partner_id_index` USING BTREE (`partner_id`,`id`),
  KEY `created_at_index` (`created_at`),
  KEY `int_id_index` (`int_id`),
  KEY `partner_kuser_indexed_custom_data_index` (`partner_id`,`kuser_id`,`indexed_custom_data_1`),
  KEY `partner_status_index` (`partner_id`,`status`),
  KEY `partner_moderation_status` (`partner_id`,`moderation_status`),
  KEY `partner_modified_at_index` (`partner_id`,`modified_at`),
  KEY `modified_at_index` (`modified_at`),
#  KEY `entry_FI_3` (`access_control_id`),
#  KEY `entry_FI_5` (`conversion_profile_id`),
  FULLTEXT KEY `name` (`name`,`tags`),
  FULLTEXT KEY `search_text_index` (`search_text`)
#  FULLTEXT KEY `search_text_discrete_index` (`search_text_discrete`)
) ENGINE=MyISAM AUTO_INCREMENT=67087 DEFAULT CHARSET=latin1;
*/
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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
#	`scheduler_id` INTEGER default null,
#	`worker_id` INTEGER default null,
#	`batch_index` INTEGER default null,
	`processor_expiration` DATETIME,
	`execution_attempts` TINYINT,
	`lock_version` INTEGER,
	`partner_id` INTEGER default 0,
	`updated_at` DATETIME,
#	`dc` VARCHAR(2),
	PRIMARY KEY (`id`),
	KEY `mail_job_index`(`mail_priority`, `created_at`),
	KEY `recipient_id_index`(`recipient_id`),
	KEY `campaign_id_index`(`campaign_id`),
	KEY `partner_id_index`(`partner_id`),
	CONSTRAINT `mail_job_FK_1`
		FOREIGN KEY (`recipient_id`)
		REFERENCES `kuser` (`id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
#	`file_size` INTEGER default null,
#	`duplication_key` VARCHAR(2047),
	`status` INTEGER,
	`abort` TINYINT,
	`check_again_timeout` INTEGER,
	`progress` TINYINT,
	`message` VARCHAR(1024),
	`description` VARCHAR(1024),
	`updates_count` SMALLINT,
	`created_at` DATETIME,
#	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
#	`updated_by` VARCHAR(20),
#	`deleted_at` DATETIME,
#	`priority` TINYINT,
#	`work_group_id` INTEGER,
#	`queue_time` DATETIME,
#	`finish_time` DATETIME,
	`entry_id` VARCHAR(20) default '',
	`partner_id` INTEGER default 0,
	`subp_id` INTEGER default 0,
#	`scheduler_id` INTEGER,
#	`worker_id` INTEGER,
#	`batch_index` INTEGER,
#	`last_scheduler_id` INTEGER,
#	`last_worker_id` INTEGER,
#	`last_worker_remote` INTEGER,
	`processor_expiration` DATETIME,
	`execution_attempts` TINYINT,
	`lock_version` INTEGER,
#	`twin_job_id` INTEGER default null,
#	`bulk_job_id` INTEGER default null,
#	`root_job_id` INTEGER default null,
	`parent_job_id` INTEGER default null,
#	`dc` VARCHAR(2),
#	`err_type` INTEGER,
#	`err_number` INTEGER,
#	`on_stress_divert_to` INTEGER,
	PRIMARY KEY (`id`),
	KEY `entry_id_index_id`(`entry_id`, `id`)
#	KEY `partner_id_index`(`partner_id`),
#	KEY `work_group_id_index_priority`(`work_group_id`, `priority`),
#	KEY `twin_job_id_index`(`twin_job_id`),
#	KEY `bulk_job_id_index`(`bulk_job_id`),
#	KEY `root_job_id_index`(`root_job_id`),
#	KEY `parent_job_id_index`(`parent_job_id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

#-----------------------------------------------------------------------------
#-- blocked_email
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `blocked_email`;


CREATE TABLE `blocked_email`
(
	`email` VARCHAR(40)  NOT NULL,
	PRIMARY KEY (`email`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
#	`priority_group_id` INTEGER,
#	`work_group_id` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_alias_index`(`partner_alias`),
	INDEX `partner_FI_1` (`anonymous_kuser_id`),
	CONSTRAINT `partner_FK_1`
		FOREIGN KEY (`anonymous_kuser_id`)
		REFERENCES `kuser` (`id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
#	`scheduler_id` INTEGER default null,
#	`worker_id` INTEGER default null,
#	`batch_index` INTEGER default null,
	`processor_expiration` DATETIME,
	`execution_attempts` TINYINT,
	`lock_version` INTEGER,
#	`dc` VARCHAR(2),
	PRIMARY KEY (`id`),
	KEY `status_partner_id_index`(`status`, `partner_id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

#-----------------------------------------------------------------------------
#-- ui_conf
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ui_conf`;

CREATE TABLE `ui_conf` (
  `id` int(11) NOT NULL auto_increment,
  `obj_type` smallint(6) default NULL,
  `partner_id` int(11) default NULL,
  `subp_id` int(11) default NULL,
  `conf_file_path` varchar(128) default NULL,
  `name` varchar(128) default NULL,
  `width` varchar(10) default NULL,
  `height` varchar(10) default NULL,
  `html_params` varchar(256) default NULL,
  `swf_url` varchar(256) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `conf_vars` varchar(4096) default NULL,
  `use_cdn` tinyint(4) default NULL,
  `tags` text,
  `custom_data` text,
  `status` int(11) default '2',
  `description` varchar(4096) default NULL,
  `display_in_search` tinyint(4) default '0',
  `creation_mode` tinyint(4) default '1',
#  `version` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `partner_id_creation_mode_index` (`partner_id`,`creation_mode`)
) ENGINE=MyISAM AUTO_INCREMENT=1001600 DEFAULT CHARSET=latin1;


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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
	`file_size` INTEGER,
	PRIMARY KEY (`id`),
	KEY `object_id_object_type_version_subtype_index`(`object_id`, `object_type`, `version`, `object_sub_type`),
	KEY `partner_id_object_id_object_type_index`(`partner_id`, `object_id`, `object_type`),
	KEY `dc_status_index`(`dc`, `status`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
	PRIMARY KEY (`id`),
	KEY `flavor_asset_id_index`(`flavor_asset_id`),
	CONSTRAINT `media_info_FK_1`
		FOREIGN KEY (`flavor_asset_id`)
		REFERENCES `flavor_asset` (`id`)
)ENGINE=MyISAM;

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
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
	PRIMARY KEY (`id`)
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

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
)ENGINE=MyISAM;

#-----------------------------------------------------------------------------
#-- track_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `track_entry`;


CREATE TABLE `track_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`track_event_type_id` SMALLINT,
	`partner_id` INTEGER,
	`entry_id` VARCHAR(20),
	`host_name` VARCHAR(20),
	`uid` VARCHAR(63),
	`track_event_status_id` SMALLINT,
	`param_1_str` VARCHAR(255),
	`param_2_str` VARCHAR(255),
	`param_3_int` INTEGER,
	`param_4_int` INTEGER,
	`description` VARCHAR(127),
	`context` VARCHAR(127),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_event_type_indx`(`partner_id`,`track_event_type_id`),
	KEY `entry_id_indx`(`entry_id`),
	KEY `track_event_type_id_indx`(`track_event_type_id`),
	KEY `param_1_indx`(`param_1_str`)
)ENGINE=MyISAM;

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
	`status_updated_at` DATETIME,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME,
	PRIMARY KEY (`id`),
	UNIQUE KEY `system_user_email_unique` (`email`)
)ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
