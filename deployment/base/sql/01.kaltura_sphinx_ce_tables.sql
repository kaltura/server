SET GLOBAL sql_mode = '';
/*Table structure for table `sphinx_log` */
CREATE TABLE IF NOT EXISTS `sphinx_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `executed_server_id` int(11) NOT NULL,
  `object_type` varchar(255) NOT NULL,
  `object_id` varchar(20) NOT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT '0',
  `dc` int(11) DEFAULT NULL,
  `sql` longtext,
  `created_at` datetime DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `index_name` VARCHAR(128),
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `creatd_at` (`created_at`),
  KEY `sphinx_log_FI_1` (`partner_id`)
) ENGINE=MYISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

/*Table structure for table `sphinx_log_server` */
CREATE TABLE IF NOT EXISTS `sphinx_log_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server` varchar(63) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `last_log_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sphinx_log_server_FI_1` (`last_log_id`)
) ENGINE=MYISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
