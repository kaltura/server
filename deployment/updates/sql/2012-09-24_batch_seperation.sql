DROP TABLE IF EXISTS `batch_job_sep`;

CREATE TABLE `batch_job_sep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` varchar(20) DEFAULT NULL,
  `object_type` int(6) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `parent_job_id` int(11) DEFAULT NULL,
  `bulk_job_id` int(11) DEFAULT NULL,
  `root_job_id` int(11) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `job_type` int(6) DEFAULT NULL,
  `job_sub_type` int(6) DEFAULT NULL,
  `priority` tinyint(4) NOT NULL,
  `status` int(11) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `data` TEXT ,
  `description` varchar(1024) DEFAULT NULL,
  `err_type` int(11) NOT NULL DEFAULT '0',
  `err_number` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `queue_time` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `finish_time` datetime DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `last_scheduler_id` int(11) DEFAULT NULL,
  `last_worker_id` int(11) DEFAULT NULL,
  `execution_status` int(4) DEFAULT NULL,
  `batch_job_lock_id` int(20),
  `history` TEXT,
  `lock_info` TEXT,
  PRIMARY KEY (`id`),
  KEY `entry_id_index` (`entry_id`), 
  KEY `object_index` (`object_id`,`object_type`),
  KEY `created_at_job_type_status_index` (`created_at`,`job_type`,`status`),
  KEY `bulk_job_id_index` (`bulk_job_id`),
  KEY `root_job_id_index` (`root_job_id`),
  KEY `parent_job_id_index` (`parent_job_id`),
  KEY `updated_at_index` (`updated_at`),
  KEY `partner_type_status_index` (`partner_id`,`job_type`, `status`)
) ENGINE=innodb AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `batch_job_lock`;

CREATE TABLE `batch_job_lock` (
  `id` int(11) NOT NULL,
  `entry_id` varchar(20) DEFAULT NULL,
  `object_type` int(6) DEFAULT NULL,
  `object_id` varchar(20) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `dc` int(11) DEFAULT NULL,
  `job_type` int(6) DEFAULT NULL,
  `job_sub_type` int(6) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `priority` tinyint(4) NOT NULL,
  `urgency` tinyint(4) NOT NULL,
  `estimated_effort` bigint(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `execution_attempts` tinyint(4) DEFAULT NULL,
  `scheduler_id` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `batch_index` int(11) DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `execution_status` int(4) DEFAULT NULL,
  `batch_job_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  
  KEY `lock_index` (`scheduler_id`, `worker_id`, `batch_index`),
  KEY `partner_urgency_type_index` (`partner_id`, `job_type`, `worker_id`, `urgency`),
  KEY `partner+type_status_index` (`job_type`, `status`, `dc`, `partner_id`),
  KEY `urgency_type_status_index` (`job_type`, `status`, `dc`, `urgency`),
  KEY `execution_attempts_index` (`job_type`,`execution_attempts`,`dc`),
  KEY `processor_expiration_index` (`job_type`,`execution_attempts`,`expiration`)
) ENGINE=innodb AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `partner_load`;

CREATE TABLE `partner_load` (
  `partner_id` int(11) DEFAULT NULL,
  `job_type` int(6) DEFAULT NULL,
  `partner_load` int(11) DEFAULT 0,
  `weighted_partner_load` int(11) DEFAULT 0,
  PRIMARY KEY (`partner_id`,`job_type`),
  KEY `weight_index`(`weighted_partner_load`),
  KEY `load_index`(`partner_load`)
 ) ENGINE=innodb AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

ALTER TABLE flavor_params_conversion_profile ADD `priority` tinyint(4) DEFAULT 0;
ALTER TABLE flavor_params_conversion_profile ADD `custom_data` text;