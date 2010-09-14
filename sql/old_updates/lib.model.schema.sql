
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
	`produced_kshows` INTEGER default 0,
	`status` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`partner_id` INTEGER default 0,
	`display_in_search` TINYINT,
	`search_text` VARCHAR(4096),
	`partner_data` VARCHAR(4096),
	PRIMARY KEY (`id`),
	KEY `screen_name_index`(`screen_name`),
	KEY `full_name_index`(`full_name`),
	KEY `network_college_index`(`network_college`),
	KEY `network_highschool_index`(`network_highschool`),
	KEY `entries_index`(`entries`),
	KEY `views_index`(`views`),
	KEY `display_in_search_index`(`display_in_search`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kshow
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kshow`;


CREATE TABLE `kshow`
(
	`id` VARCHAR(10)  NOT NULL,
	`producer_id` INTEGER,
	`episode_id` VARCHAR(10),
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
	`show_entry_id` VARCHAR(10),
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
	`id` VARCHAR(10)  NOT NULL,
	`kshow_id` VARCHAR(10),
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
	`source_id` INTEGER,
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
		REFERENCES `kuser` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kvote
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kvote`;


CREATE TABLE `kvote`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kshow_id` VARCHAR(10),
	`entry_id` VARCHAR(10),
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
	`kshow_id` VARCHAR(10),
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
	PRIMARY KEY (`id`),
	KEY `mail_job_index`(`mail_priority`, `created_at`),
	KEY `recipient_id_index`(`recipient_id`),
	KEY `campaign_id_index`(`campaign_id`),
	CONSTRAINT `mail_job_FK_1`
		FOREIGN KEY (`recipient_id`)
		REFERENCES `kuser` (`id`)
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
	`status` INTEGER,
	`abort` TINYINT,
	`check_again_timeout` INTEGER,
	`progress` TINYINT,
	`message` VARCHAR(1024),
	`description` VARCHAR(1024),
	`updates_count` SMALLINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`entry_id` VARCHAR(10) default '',
	`partner_id` INTEGER default 0,
	`subp_id` INTEGER default 0,
	`processor_name` VARCHAR(64),
	`processor_expiration` DATETIME,
	PRIMARY KEY (`id`),
	KEY `entry_id_index_id`(`entry_id`, `id`),
	KEY `partner_id_subp_id`(`partner_id`, `subp_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- bb_forum
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `bb_forum`;


CREATE TABLE `bb_forum`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	`description` TEXT,
	`post_count` INTEGER default 0,
	`thread_count` INTEGER default 0,
	`last_post` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`is_live` INTEGER default 1,
	PRIMARY KEY (`id`),
	INDEX `bb_forum_FI_1` (`last_post`),
	CONSTRAINT `bb_forum_FK_1`
		FOREIGN KEY (`last_post`)
		REFERENCES `bb_post` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- bb_post
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `bb_post`;


CREATE TABLE `bb_post`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(255),
	`content` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`kuser_id` INTEGER,
	`forum_id` INTEGER,
	`parent_id` INTEGER,
	`node_level` INTEGER,
	`node_id` VARCHAR(64),
	`num_childern` INTEGER default 0,
	`last_child` INTEGER,
	PRIMARY KEY (`id`),
	KEY `nodeid_index`(`node_id`),
	INDEX `bb_post_FI_1` (`kuser_id`),
	CONSTRAINT `bb_post_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`),
	INDEX `bb_post_FI_2` (`forum_id`),
	CONSTRAINT `bb_post_FK_2`
		FOREIGN KEY (`forum_id`)
		REFERENCES `bb_forum` (`id`),
	INDEX `bb_post_FI_3` (`parent_id`),
	CONSTRAINT `bb_post_FK_3`
		FOREIGN KEY (`parent_id`)
		REFERENCES `bb_post` (`id`)
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
	`entry_id` VARCHAR(10),
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
	`kshow_id` VARCHAR(10),
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
	PRIMARY KEY (`id`),
	KEY `partner_alias_index`(`partner_alias`),
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
	`kshow_id` VARCHAR(10),
	`entry_id` VARCHAR(10),
	`kmedia_type` INTEGER,
	`widget_type` INTEGER,
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
	`object_id` VARCHAR(10),
	`status` INTEGER,
	`notification_data` VARCHAR(4096),
	`number_of_attempts` SMALLINT default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`notification_result` VARCHAR(256),
	`object_type` SMALLINT,
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
	`object_id` VARCHAR(10),
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
#-- roughcut_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `roughcut_entry`;


CREATE TABLE `roughcut_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`roughcut_id` VARCHAR(10),
	`roughcut_version` INTEGER,
	`roughcut_kshow_id` VARCHAR(10),
	`entry_id` VARCHAR(10),
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
	`id` VARCHAR(10)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`source_widget_id` VARCHAR(10),
	`root_widget_id` VARCHAR(10),
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`kshow_id` VARCHAR(10),
	`entry_id` VARCHAR(10),
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
	`widget_id` VARCHAR(10),
	`source_widget_id` VARCHAR(10),
	`root_widget_id` VARCHAR(10),
	`kshow_id` VARCHAR(10),
	`entry_id` VARCHAR(10),
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
	PRIMARY KEY (`id`)
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
	`amount` INTEGER,
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

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
