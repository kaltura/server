CREATE TABLE `moderation_flag` (
  `id` int(11) NOT NULL auto_increment,
  `partner_id` int(11) default NULL,
  `kuser_id` int(11) default NULL,
  `object_type` smallint(6) default NULL,
  `flagged_entry_id` varchar(10) collate latin1_general_ci default NULL,
  `flagged_kuser_id` int(11) default NULL,
  `status` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `comments` varchar(1024) collate latin1_general_ci default NULL,
  `flag_type` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `partner_id_status_index` (`partner_id`,`status`),
  KEY `entry_object_index` (`partner_id`,`status`,`object_type`,`flagged_kuser_id`),
  KEY `moderation_flag_FI_1` (`kuser_id`),
  KEY `moderation_flag_FI_2` (`flagged_entry_id`),
  KEY `moderation_flag_FI_3` (`flagged_kuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
