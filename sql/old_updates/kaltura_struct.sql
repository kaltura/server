/*
SQLyog Community Edition- MySQL GUI v5.32
Host - 5.0.37-log : Database - kaltura
*********************************************************************
Server version : 5.0.37-log
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `kaltura`;

USE `kaltura`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `admin_kuser` */

DROP TABLE IF EXISTS `admin_kuser`;

CREATE TABLE `admin_kuser` (
  `id` int(11) NOT NULL auto_increment,
  `screen_name` varchar(20) default NULL,
  `full_name` varchar(40) default NULL,
  `email` varchar(50) default NULL,
  `sha1_password` varchar(40) default NULL,
  `salt` varchar(32) default NULL,
  `picture` varchar(48) default NULL,
  `icon` tinyint(4) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partner_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `screen_name_index` (`screen_name`),
  KEY `admin_kuser_FI_1` (`partner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10859 DEFAULT CHARSET=utf8;

/*Table structure for table `admin_permission` */

DROP TABLE IF EXISTS `admin_permission`;

CREATE TABLE `admin_permission` (
  `id` int(11) NOT NULL auto_increment,
  `groups` varchar(512) default NULL,
  `admin_kuser_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `admin_permission_FI_1` (`admin_kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

/*Table structure for table `alert` */

DROP TABLE IF EXISTS `alert`;

CREATE TABLE `alert` (
  `id` int(11) NOT NULL auto_increment,
  `kuser_id` int(11) default NULL,
  `alert_type` int(11) default NULL,
  `subject_id` int(11) default NULL,
  `rule_type` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subject_index` (`alert_type`,`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

/*Table structure for table `batch_job` */

DROP TABLE IF EXISTS `batch_job`;

CREATE TABLE `batch_job` (
  `id` int(11) NOT NULL auto_increment,
  `job_type` smallint(6) default NULL,
  `job_sub_type` smallint(6) default NULL,
  `data` varchar(4096) default NULL,
  `status` int(11) default NULL,
  `abort` tinyint(4) default NULL,
  `check_again_timeout` int(11) default NULL,
  `progress` tinyint(4) default NULL,
  `message` varchar(1024) default NULL,
  `description` varchar(1024) default NULL,
  `updates_count` smallint(6) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `entry_id` varchar(10) default '',
  `partner_id` int(11) default NULL,
  `subp_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `entry_id_index_id` (`entry_id`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=261 DEFAULT CHARSET=utf8;

/*Table structure for table `bb_forum` */

DROP TABLE IF EXISTS `bb_forum`;

CREATE TABLE `bb_forum` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `post_count` int(11) default '0',
  `thread_count` int(11) default '0',
  `last_post` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `is_live` int(11) default '1',
  PRIMARY KEY  (`id`),
  KEY `bb_forum_FI_1` (`last_post`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `bb_post` */

DROP TABLE IF EXISTS `bb_post`;

CREATE TABLE `bb_post` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `content` text,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `kuser_id` int(11) default NULL,
  `forum_id` int(11) default NULL,
  `parent_id` int(11) default NULL,
  `node_level` int(11) default NULL,
  `node_id` varchar(64) default NULL,
  `num_childern` int(11) default '0',
  `last_child` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `bb_post_FI_1` (`kuser_id`),
  KEY `bb_post_FI_2` (`forum_id`),
  KEY `bb_post_FI_3` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

/*Table structure for table `blocked_email` */

DROP TABLE IF EXISTS `blocked_email`;

CREATE TABLE `blocked_email` (
  `email` varchar(40) NOT NULL,
  PRIMARY KEY  (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `comment` */

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int(11) NOT NULL auto_increment,
  `kuser_id` int(11) default NULL,
  `comment_type` int(11) default NULL,
  `subject_id` int(11) default NULL,
  `base_date` date default NULL,
  `reply_to` int(11) default NULL,
  `comment` varchar(256) default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `subject_created_index` (`comment_type`,`subject_id`,`base_date`,`reply_to`,`created_at`),
  KEY `comment_FI_1` (`kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1014 DEFAULT CHARSET=utf8;

/*Table structure for table `conversion` */

DROP TABLE IF EXISTS `conversion`;

CREATE TABLE `conversion` (
  `id` int(11) NOT NULL auto_increment,
  `entry_id` varchar(10) default NULL,
  `in_file_name` varchar(128) default NULL,
  `in_file_ext` varchar(16) default NULL,
  `in_file_size` int(11) default NULL,
  `source` int(11) default NULL,
  `status` int(11) default NULL,
  `conversion_params` varchar(512) default NULL,
  `out_file_name` varchar(128) default NULL,
  `out_file_size` int(11) default NULL,
  `out_file_name_2` varchar(128) default NULL,
  `out_file_size_2` int(11) default NULL,
  `conversion_time` int(11) default NULL,
  `total_process_time` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `entry_id_index` (`entry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=265 DEFAULT CHARSET=utf8;

/*Table structure for table `e1` */

DROP TABLE IF EXISTS `e1`;

CREATE TABLE `e1` (
  `id` varchar(10) NOT NULL,
  `int_id` int(11) NOT NULL auto_increment,
  KEY `int_id_index` (`int_id`)
) ENGINE=MyISAM AUTO_INCREMENT=766 DEFAULT CHARSET=utf8;

/*Table structure for table `email_campaign` */

DROP TABLE IF EXISTS `email_campaign`;

CREATE TABLE `email_campaign` (
  `id` int(11) NOT NULL auto_increment,
  `criteria_id` smallint(6) default NULL,
  `criteria_str` varchar(1024) default NULL,
  `criteria_params` varchar(1024) default NULL,
  `template_path` varchar(256) default NULL,
  `campaign_mgr_kuser_id` int(11) default NULL,
  `send_count` int(11) default NULL,
  `open_count` int(11) default NULL,
  `click_count` int(11) default NULL,
  `status` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `campaign_mgr_kuser_id_index` (`campaign_mgr_kuser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `entry` */

DROP TABLE IF EXISTS `entry`;

CREATE TABLE `entry` (
  `id` varchar(10) NOT NULL,
  `kshow_id` varchar(10) default NULL,
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
  `partner_id` int(11) default NULL,
  `display_in_search` tinyint(4) default NULL,
  `subp_id` int(11) default '0',
  `custom_data` text,
  `search_text` varchar(4096) default NULL,
  `screen_name` varchar(20) default NULL,
  `site_url` varchar(256) default NULL,
  `desired_status` smallint(6) default NULL,
  `permissions` int(11) default NULL,
  `group_id` varchar(64) default NULL,
  `plays` int(11) default NULL,
  `partner_data` varchar(4096) default NULL,
  `int_id` int(11) NOT NULL auto_increment,
  `indexed_custom_data_1` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `kshow_rank_index` (`kshow_id`,`rank`),
  KEY `kshow_views_index` (`kshow_id`,`views`),
  KEY `kshow_votes_index` (`kshow_id`,`votes`),
  KEY `kshow_created_index` (`kshow_id`,`created_at`),
  KEY `views_index` (`views`),
  KEY `votes_index` (`votes`),
  KEY `entry_FI_2` (`kuser_id`),
  KEY `partner_id_index` USING BTREE (`partner_id`,`id`),
  KEY `partner_group_index` (`partner_id`,`group_id`),
  KEY `int_id_index` (`int_id`),
  KEY `partner_kuser_indexed_custom_data_index` (`partner_id`,`kuser_id`,`indexed_custom_data_1`),
  FULLTEXT KEY `name` (`name`,`tags`),
  FULLTEXT KEY `search_text_index` (`search_text`)
) ENGINE=MyISAM AUTO_INCREMENT=16974 DEFAULT CHARSET=utf8;

/*Table structure for table `facebook_invite` */

DROP TABLE IF EXISTS `facebook_invite`;

CREATE TABLE `facebook_invite` (
  `id` int(11) NOT NULL auto_increment,
  `puser_id` varchar(64) default NULL,
  `invited_puser_id` varchar(64) default NULL,
  `status` smallint(6) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `puser_id_index` (`puser_id`),
  KEY `invited_puser_id_index` (`invited_puser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `favorite` */

DROP TABLE IF EXISTS `favorite`;

CREATE TABLE `favorite` (
  `kuser_id` int(11) default NULL,
  `subject_type` int(11) default NULL,
  `subject_id` int(11) default NULL,
  `privacy` int(11) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subject_index` (`subject_type`,`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1002 DEFAULT CHARSET=utf8;

/*Table structure for table `flag` */

DROP TABLE IF EXISTS `flag`;

CREATE TABLE `flag` (
  `id` int(11) NOT NULL auto_increment,
  `kuser_id` int(11) default NULL,
  `subject_type` int(11) default NULL,
  `subject_id` int(11) default NULL,
  `flag_type` int(11) default NULL,
  `other` varchar(60) default NULL,
  `comment` varchar(2048) default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `subject_created_index` (`subject_type`,`subject_id`,`created_at`),
  KEY `flag_FI_1` (`kuser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `flickr_token` */

DROP TABLE IF EXISTS `flickr_token`;

CREATE TABLE `flickr_token` (
  `kalt_token` varchar(256) NOT NULL,
  `anonymous` tinyint(4) default '0',
  `frob` varchar(64) default NULL,
  `token` varchar(64) default NULL,
  `nsid` varchar(64) default NULL,
  `response` varchar(512) default NULL,
  `is_valid` int(11) default '0',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`kalt_token`),
  KEY `is_valid_index` (`is_valid`,`kalt_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `keyword` */

DROP TABLE IF EXISTS `keyword`;

CREATE TABLE `keyword` (
  `word` varchar(30) NOT NULL,
  `entity_id` int(11) default NULL,
  `entity_type` int(11) default NULL,
  `entity_columns` varchar(30) default NULL,
  PRIMARY KEY  (`word`),
  KEY `word_index` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `kshow` */

DROP TABLE IF EXISTS `kshow`;

CREATE TABLE `kshow` (
  `id` varchar(10) NOT NULL,
  `producer_id` int(11) default NULL,
  `episode_id` varchar(10) default NULL,
  `name` varchar(60) default NULL,
  `subdomain` varchar(30) default NULL,
  `description` text,
  `status` int(11) default '0',
  `type` int(11) default NULL,
  `media_type` int(11) default NULL,
  `format_type` int(11) default NULL,
  `language` int(11) default NULL,
  `start_date` date default NULL,
  `end_date` date default NULL,
  `skin` text,
  `thumbnail` varchar(48) default NULL,
  `show_entry_id` varchar(10) default NULL,
  `intro_id` varchar(10) default NULL,
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
  `partner_id` int(11) default NULL,
  `display_in_search` tinyint(4) default '1',
  `subp_id` int(11) default '0',
  `search_text` varchar(4096) default NULL,
  `permissions` varchar(1024) default NULL,
  `group_id` varchar(64) default NULL,
  `plays` int(11) default '1',
  `partner_data` varchar(4096) default NULL,
  `int_id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `views_index` (`views`),
  KEY `votes_index` (`votes`),
  KEY `created_at_index` (`created_at`),
  KEY `type_index` (`type`),
  KEY `kshow_FI_1` (`producer_id`),
  KEY `kshow_FI_2` (`show_entry_id`),
  KEY `kshow_FI_3` (`intro_id`),
  KEY `partner_group_index` (`partner_id`,`group_id`),
  KEY `int_id_index` (`int_id`),
  FULLTEXT KEY `name` (`name`,`description`,`tags`),
  FULLTEXT KEY `search_text_index` (`search_text`)
) ENGINE=MyISAM AUTO_INCREMENT=12143 DEFAULT CHARSET=utf8;

/*Table structure for table `kshow_kuser` */

DROP TABLE IF EXISTS `kshow_kuser`;

CREATE TABLE `kshow_kuser` (
  `kshow_id` varchar(10) default NULL,
  `kuser_id` int(11) default NULL,
  `subscription_type` int(11) default NULL,
  `alert_type` int(11) default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `kshow_index` (`kshow_id`),
  KEY `kuser_index` (`kuser_id`),
  KEY `subscription_index` (`kshow_id`,`subscription_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `kuser` */

DROP TABLE IF EXISTS `kuser`;

CREATE TABLE `kuser` (
  `id` int(11) NOT NULL auto_increment,
  `screen_name` varchar(20) default NULL,
  `full_name` varchar(40) default NULL,
  `email` varchar(50) default NULL,
  `sha1_password` varchar(40) default NULL,
  `salt` varchar(32) default NULL,
  `date_of_birth` date default NULL,
  `country` varchar(2) default NULL,
  `state` varchar(16) default NULL,
  `city` varchar(30) default NULL,
  `zip` varchar(10) default NULL,
  `url_list` varchar(256) default NULL,
  `picture` varchar(48) default NULL,
  `icon` tinyint(4) default NULL,
  `about_me` varchar(4096) default NULL,
  `tags` text,
  `tagline` varchar(256) default NULL,
  `network_highschool` varchar(30) default NULL,
  `network_college` varchar(30) default NULL,
  `network_other` varchar(30) default NULL,
  `mobile_num` varchar(16) default NULL,
  `mature_content` tinyint(4) default NULL,
  `gender` tinyint(4) default NULL,
  `registration_ip` int(11) default NULL,
  `registration_cookie` varchar(256) default NULL,
  `im_list` varchar(256) default NULL,
  `views` int(11) default '0',
  `fans` int(11) default '0',
  `entries` int(11) default '0',
  `produced_kshows` int(11) default '0',
  `status` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partner_id` int(11) default NULL,
  `display_in_search` tinyint(4) default '1',
  `search_text` varchar(4096) default NULL,
  `partner_data` varchar(4096) default NULL,
  PRIMARY KEY  (`id`),
  KEY `screen_name_index` (`screen_name`),
  KEY `full_name_index` (`full_name`),
  KEY `network_college_index` (`network_college`),
  KEY `network_highschool_index` (`network_highschool`),
  KEY `entries_index` (`entries`),
  KEY `views_index` (`views`),
  FULLTEXT KEY `search_text_index` (`search_text`)
) ENGINE=MyISAM AUTO_INCREMENT=20099 DEFAULT CHARSET=utf8;

/*Table structure for table `kvote` */

DROP TABLE IF EXISTS `kvote`;

CREATE TABLE `kvote` (
  `id` int(11) NOT NULL auto_increment,
  `kshow_id` varchar(10) default NULL,
  `entry_id` varchar(10) default NULL,
  `kuser_id` int(11) default NULL,
  `rank` int(11) default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `kshow_index` (`kshow_id`),
  KEY `entry_user_index` (`entry_id`),
  KEY `kvote_FI_3` (`kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

/*Table structure for table `kwidget_log` */

DROP TABLE IF EXISTS `kwidget_log`;

CREATE TABLE `kwidget_log` (
  `id` int(11) NOT NULL auto_increment,
  `widget_id` int(11) default NULL,
  `source_widget_id` int(11) default NULL,
  `root_widget_id` int(11) default NULL,
  `kshow_id` varchar(10) default NULL,
  `entry_id` varchar(10) default NULL,
  `ui_conf_id` int(11) default NULL,
  `referer` varchar(1024) default NULL,
  `views` int(11) default '0',
  `ip1` int(11) default NULL,
  `ip1_count` int(11) default '0',
  `ip2` int(11) default NULL,
  `ip2_count` int(11) default '0',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `plays` int(11) default '0',
  `partner_id` int(11) default '0',
  `subp_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `referer_index` (`referer`(333)),
  KEY `entry_id_kshow_id_index` (`entry_id`,`kshow_id`),
  KEY `partner_id_subp_id_index` (`partner_id`,`subp_id`),
  KEY `kwidget_log_FI_1` (`widget_id`),
  KEY `kwidget_log_FI_2` (`kshow_id`),
  KEY `kwidget_log_FI_4` (`ui_conf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `mail_job` */

DROP TABLE IF EXISTS `mail_job`;

CREATE TABLE `mail_job` (
  `id` int(11) NOT NULL auto_increment,
  `mail_type` smallint(6) default NULL,
  `mail_priority` smallint(6) default NULL,
  `recipient_name` varchar(64) default NULL,
  `recipient_email` varchar(64) default NULL,
  `recipient_id` int(11) default NULL,
  `from_name` varchar(64) default NULL,
  `from_email` varchar(64) default NULL,
  `body_params` varchar(2048) default NULL,
  `subject_params` varchar(512) default NULL,
  `template_path` varchar(512) default NULL,
  `culture` tinyint(4) default NULL,
  `status` tinyint(4) default NULL,
  `created_at` datetime default NULL,
  `campaign_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `campaign_id_index` (`campaign_id`),
  KEY `STATUS_PRIORITY_INDEX` (`status`,`mail_priority`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;

/*Table structure for table `moderation` */

DROP TABLE IF EXISTS `moderation`;

CREATE TABLE `moderation` (
  `id` int(11) NOT NULL auto_increment,
  `partner_id` int(11) default NULL,
  `subp_id` int(11) default NULL,
  `object_id` varchar(10) default NULL,
  `object_type` smallint(6) default NULL,
  `status` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `comments` varchar(1024) default NULL,
  `group_id` varchar(64) default NULL,
  `puser_id` varchar(64) default NULL,
  `kuser_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `partner_id_status_index` (`partner_id`,`status`),
  KEY `partner_id_group_id_status_index` (`partner_id`,`group_id`,`status`),
  KEY `object_index` (`partner_id`,`status`,`object_id`,`object_type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `news` */

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(11) NOT NULL auto_increment,
  `image_path` varchar(256) default NULL,
  `href` varchar(1024) default NULL,
  `text` varchar(1024) default NULL,
  `alt` varchar(256) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `notification` */

DROP TABLE IF EXISTS `notification`;

CREATE TABLE `notification` (
  `id` int(11) NOT NULL auto_increment,
  `partner_id` int(11) default NULL,
  `puser_id` varchar(64) default NULL,
  `type` smallint(6) default NULL,
  `object_id` varchar(10) default NULL,
  `status` int(11) default NULL,
  `notification_data` varchar(4096) default NULL,
  `number_of_attempts` smallint(6) default '0',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `notification_result` varchar(256) default NULL,
  `object_type` smallint(6) default NULL,
  PRIMARY KEY  (`id`),
  KEY `status_partner_id_index` (`status`,`partner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1397 DEFAULT CHARSET=utf8;

/*Table structure for table `partner` */

DROP TABLE IF EXISTS `partner`;

CREATE TABLE `partner` (
  `id` int(11) NOT NULL auto_increment,
  `partner_name` varchar(256) default NULL,
  `url1` varchar(1024) default NULL,
  `url2` varchar(1024) default NULL,
  `secret` varchar(50) default NULL,
  `admin_secret` varchar(50) default NULL,
  `max_number_of_hits_per_day` int(11) default '-1',
  `appear_in_search` int(11) default '1',
  `debug_level` int(11) default '0',
  `invalid_login_count` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partner_alias` varchar(64) default NULL,
  `ANONYMOUS_KUSER_ID` int(11) default NULL,
  `ks_max_expiry_in_seconds` int(11) default NULL,
  `create_user_on_demand` tinyint(4) default '1',
  `prefix` varchar(32) default NULL,
  `admin_name` varchar(50) default NULL,
  `admin_email` varchar(50) default NULL,
  `description` varchar(1024) default NULL,
  `commercial_use` tinyint(4) default NULL,
  `moderate_content` tinyint(4) default '0',
  `notify` tinyint(4) default '0',
  `custom_data` text,
  PRIMARY KEY  (`id`),
  KEY `partner_alias_index` (`partner_alias`)
) ENGINE=MyISAM AUTO_INCREMENT=523 DEFAULT CHARSET=utf8;

/*Table structure for table `partnership` */

DROP TABLE IF EXISTS `partnership`;

CREATE TABLE `partnership` (
  `id` int(11) NOT NULL auto_increment,
  `partnership_order` int(11) default NULL,
  `image_path` varchar(256) default NULL,
  `href` varchar(1024) default NULL,
  `text` varchar(1024) default NULL,
  `alt` varchar(256) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partnership_date` varchar(128) default NULL,
  PRIMARY KEY  (`id`),
  KEY `partnership_order_index` (`partnership_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `pr_news` */

DROP TABLE IF EXISTS `pr_news`;

CREATE TABLE `pr_news` (
  `id` int(11) NOT NULL auto_increment,
  `pr_order` int(11) default NULL,
  `image_path` varchar(256) default NULL,
  `href` varchar(1024) default NULL,
  `text` varchar(1024) default NULL,
  `alt` varchar(256) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `press_date` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Table structure for table `puser_kuser` */

DROP TABLE IF EXISTS `puser_kuser`;

CREATE TABLE `puser_kuser` (
  `id` int(11) NOT NULL auto_increment,
  `partner_id` int(11) default NULL,
  `puser_id` varchar(64) default NULL,
  `kuser_id` int(11) default NULL,
  `puser_name` varchar(64) default NULL,
  `custom_data` varchar(1024) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `context` varchar(1024) default '',
  `subp_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `partner_puser_index` (`partner_id`,`puser_id`),
  KEY `kuser_id_index` (`kuser_id`),
  KEY `I_referenced_puser_role_FK_3_1` (`puser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;

/*Table structure for table `puser_role` */

DROP TABLE IF EXISTS `puser_role`;

CREATE TABLE `puser_role` (
  `id` int(11) NOT NULL auto_increment,
  `kshow_id` varchar(10) default NULL,
  `partner_id` int(11) default NULL,
  `puser_id` varchar(64) default NULL,
  `role` int(11) default NULL,
  `puser_name` varchar(64) default NULL,
  `custom_data` varchar(1024) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `subp_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `partner_puser_index` (`partner_id`,`puser_id`),
  KEY `kshow_id_index` (`kshow_id`),
  KEY `puser_role_FI_3` (`puser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `roughcut_entry` */

DROP TABLE IF EXISTS `roughcut_entry`;

CREATE TABLE `roughcut_entry` (
  `id` int(11) NOT NULL auto_increment,
  `roughcut_id` varchar(10) default NULL,
  `roughcut_version` int(11) default NULL,
  `roughcut_kshow_id` varchar(10) default NULL,
  `entry_id` varchar(10) default NULL,
  `partner_id` int(11) default NULL,
  `op_type` smallint(6) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `partner_id_index` (`partner_id`),
  KEY `entry_id_index` (`entry_id`),
  KEY `roughcut_id_index` (`roughcut_id`),
  KEY `roughcut_kshow_id_index` (`roughcut_kshow_id`)
) ENGINE=MyISAM AUTO_INCREMENT=740 DEFAULT CHARSET=utf8;

/*Table structure for table `tagword_count` */

DROP TABLE IF EXISTS `tagword_count`;

CREATE TABLE `tagword_count` (
  `tag` varchar(30) NOT NULL,
  `tag_count` int(11) default NULL,
  PRIMARY KEY  (`tag`),
  KEY `count_index` (`tag_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `ui_conf` */

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
  `use_cdn` tinyint(4) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19005 DEFAULT CHARSET=latin1;

/*Table structure for table `widget` */

DROP TABLE IF EXISTS `widget`;

CREATE TABLE `widget` (
  `id` varchar(32) NOT NULL,
  `int_id` int(11) NOT NULL auto_increment,
  `source_widget_id` varchar(32) default NULL,
  `root_widget_id` varchar(32) default NULL,
  `partner_id` int(11) default NULL,
  `subp_id` int(11) default NULL,
  `kshow_id` varchar(10) default NULL,
  `entry_id` varchar(10) default NULL,
  `ui_conf_id` int(11) default NULL,
  `custom_data` varchar(1024) default NULL,
  `security_type` smallint(6) default NULL,
  `security_policy` smallint(6) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `partner_data` varchar(4096) default NULL,
  PRIMARY KEY  (`id`),
  KEY `int_id_index` (`int_id`),
  KEY `widget_FI_1` (`kshow_id`),
  KEY `widget_FI_2` (`entry_id`),
  KEY `widget_FI_3` (`ui_conf_id`)
) ENGINE=MyISAM AUTO_INCREMENT=697 DEFAULT CHARSET=utf8;

/*Table structure for table `widget_log` */

DROP TABLE IF EXISTS `widget_log`;

CREATE TABLE `widget_log` (
  `id` int(11) NOT NULL auto_increment,
  `kshow_id` varchar(10) default NULL,
  `entry_id` varchar(10) default NULL,
  `kmedia_type` int(11) default NULL,
  `widget_type` int(11) default NULL,
  `referer` varchar(1024) default NULL,
  `views` int(11) default '0',
  `ip1` int(11) default NULL,
  `ip1_count` int(11) default '0',
  `ip2` int(11) default NULL,
  `ip2_count` int(11) default '0',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `plays` int(11) default '0',
  `partner_id` int(11) default '0',
  `subp_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `referer_index` (`referer`(333)),
  KEY `entry_id_kshow_id_index` (`entry_id`,`kshow_id`),
  KEY `partner_id_subp_id_index` (`partner_id`,`subp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1269 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
