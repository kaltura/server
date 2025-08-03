
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- sphinx_log
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `sphinx_log`;


CREATE TABLE `sphinx_log` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `executed_server_id` int(11) NOT NULL,
    `object_type` varchar(255) NOT NULL,
    `object_id` varchar(20) NOT NULL,
    `entry_id` varchar(20) DEFAULT NULL,
    `partner_id` int(11) DEFAULT '0',
    `dc` int(11) DEFAULT NULL,
    `sql` longtext,
    `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `type` int(11) DEFAULT NULL,
    `index_name` varchar(128) DEFAULT NULL,
    `custom_data` text,
    PRIMARY KEY (`id`,`created_at`),
    KEY `entry_id` (`entry_id`),
    KEY `sphinx_log_FI_1` (`partner_id`),
    KEY `created_at` (`created_at`),
    KEY `dc_id` (`dc`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#-----------------------------------------------------------------------------
#-- sphinx_log_server
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `sphinx_log_server`;


CREATE TABLE `sphinx_log_server` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `server` varchar(63) DEFAULT NULL,
 `dc` int(11) DEFAULT NULL,
 `last_log_id` bigint(20) DEFAULT NULL,
 `created_at` datetime DEFAULT NULL,
 `updated_at` datetime DEFAULT NULL,
 `populate_active` tinyint DEFAULT 1,
 PRIMARY KEY (`id`),
 KEY `sphinx_log_server_FI_1` (`last_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
