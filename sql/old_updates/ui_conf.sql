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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=301 DEFAULT CHARSET=latin1;

/*Data for the table `ui_conf` */

insert  into `ui_conf`(`id`,`obj_type`,`partner_id`,`subp_id`,`conf_file_path`,`name`,`width`,`height`,`html_params`,`swf_url`,`created_at`,`updated_at`) values 
	(1,NULL,NULL,NULL,'/web/kaltura/alpha/web/swf/kdp/layout7.xml','Create Ui Conf!','400','425',NULL,'http://kaldev.kaltura.com/swf/kdp/Main.swf','2008-03-13 14:12:53','2008-03-13 14:12:53'),
	(2,2,0,0,'/web/content/uiconf/kaltura/cw_generic.xml','generic_cw','680','400',NULL,NULL,'2008-03-20 15:02:05','2008-03-20 15:02:05'),
	(3,2,0,0,'/web/content/uiconf/kaltura/cw_weplay.xml','weplay_cw','680','400',NULL,NULL,'2008-03-23 15:34:22','2008-03-23 15:34:22'),
	(100,1,0,0,'/web/content/uiconf/kaltura.kdp_demo.xml','kdp_demo','400','420','','/swf/kdp/kdp.swf','2008-04-07 16:18:09','2008-04-07 16:18:09'),
	(201,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_standard.xml','wiki standard','400','420','','/swf/kdp/kdp.swf','2008-04-13 17:34:01','2008-04-13 17:34:01'),
	(202,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_compare.xml','wiki compare','400','420','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26'),
	(203,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_preview.xml','wiki preview','400','420','','/swf/kdp/kdp.swf','2008-04-13 17:34:38','2008-04-13 17:34:38'),
	(204,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_embed.xml','wiki embed','400','420','','/swf/kdp/kdp.swf','2008-04-13 17:34:55','2008-04-13 17:34:55'),
	(205,1,0,0,'/web/content/uiconf/kaltura/wiki/kdp_wiki_standard_medium.xml','wiki standard_medium','267','303',NULL,NULL,NULL,NULL),
	(210,2,0,0,'/web/content/uiconf/kaltura/wiki/cy_wiki.xml','wiki cw','680','400','','','2008-04-13 17:34:26','2008-04-13 17:34:26'),
	(300,1,0,0,'/web/content/uiconf/kaltura/wordpress/kdp_wordpress.xml','wordpress','400','420',NULL,'/swf/kdp/kdp.swf','2008-04-15 13:23:35','2008-04-15 13:23:35'),
	(301,2,0,0,'/web/content/uiconf/kaltura/demopages/kdp_demopages_full.xml','demo pages full','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26'),
	(302,2,0,0,'/web/content/uiconf/kaltura/demopages/kdp_demopages_player_only.xml','demo pages player only','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26'),
	(303,2,0,0,'/web/content/uiconf/kaltura/corp/kdp_player_only.xml','corp player only','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26'),
	(304,2,0,0,'/web/content/uiconf/kaltura/corp/kdp_full.xml','corp full','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26'),
	(305,2,0,0,'/web/content/uiconf/kaltura/corp/kdp_corp_homepage.xml','corp homepage','0','0','','/swf/kdp/kdp.swf','2008-04-13 17:34:26','2008-04-13 17:34:26');
	
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
