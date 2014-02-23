CREATE TABLE `scheduled_task_profile`
(
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `partner_id` INTEGER  NOT NULL,
  `name` VARCHAR(127)  NOT NULL,
  `system_name` VARCHAR(127),
  `description` VARCHAR(255),
  `status` INTEGER  NOT NULL,
  `object_filter_engine_type` INTEGER  NOT NULL,
  `object_filter` TEXT  NOT NULL,
  `object_filter_api_type` VARCHAR(255)  NOT NULL,
  `object_tasks` TEXT  NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `last_execution_started_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `partner_id_status_index`(`partner_id`, `status`),
  KEY `system_name_partner_id`(`system_name`, `partner_id`),
  KEY `status_last_execution_started_at`(`status`, `last_execution_started_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;