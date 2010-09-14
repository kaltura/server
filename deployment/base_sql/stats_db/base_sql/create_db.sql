CREATE TABLE `collect_stats` (
  `ip` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `widget_id` varchar(32) DEFAULT NULL,
  `command` varchar(10) DEFAULT NULL,
  `uv_id` varchar(32) DEFAULT NULL,
  KEY `partner_date` (`partner_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



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
) ENGINE=MyISAM AUTO_INCREMENT=16328875 DEFAULT CHARSET=utf8;



CREATE TABLE `partner_activity_temp` (
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
) ENGINE=MyISAM AUTO_INCREMENT=13959298 DEFAULT CHARSET=utf8;



CREATE TABLE `unique_visitors_cookie` (
  `uv_id` varchar(32) DEFAULT NULL,
  `date` date DEFAULT NULL,
  UNIQUE KEY `date_uv_id` (`date`,`uv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `unique_visitors_ip` (
  `ip` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  UNIQUE KEY `date_ip` (`date`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;