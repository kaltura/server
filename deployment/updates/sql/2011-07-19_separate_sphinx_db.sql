CREATE DATABASE kaltura_sphinx_log;

USE kaltura_sphinx_log;

DROP TABLE IF EXISTS `sphinx_log`;

CREATE TABLE `sphinx_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `entry_id` VARCHAR(20) DEFAULT NULL,
  `partner_id` INT(11) DEFAULT '0',
  `dc` INT(11) DEFAULT NULL,
  `sql` LONGTEXT,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `creatd_at` (`created_at`),
  KEY `sphinx_log_FI_1` (`partner_id`)
) ENGINE=MYISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sphinx_log_server`;

CREATE TABLE `sphinx_log_server` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `server` VARCHAR(63) DEFAULT NULL,
  `dc` INT(11) DEFAULT NULL,
  `last_log_id` INT(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sphinx_log_server_FI_1` (`last_log_id`)
) ENGINE=MYISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `kaltura_sphinx_log`.`sphinx_log` SELECT * FROM `kaltura`.`sphinx_log`;
INSERT INTO `kaltura_sphinx_log`.`sphinx_log_server` SELECT * FROM `kaltura`.`sphinx_log_server`;

DROP TABLE `kaltura`.`sphinx_log`;
DROP TABLE `kaltura`.`sphinx_log_server`;