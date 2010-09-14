
CREATE TABLE `scheduler`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`name` VARCHAR(20) default '',
	`description` VARCHAR(20) default '',
	PRIMARY KEY (`id`)
)Type=MyISAM;


CREATE TABLE `scheduler_worker`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`scheduler_id` INTEGER,
	`type` SMALLINT,
	`name` VARCHAR(20) default '',
	`description` VARCHAR(20) default '',
	PRIMARY KEY (`id`)
)Type=MyISAM;


CREATE TABLE `scheduler_status`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`scheduler_id` INTEGER,
	`worker_id` INTEGER default null,
	`worker_type` SMALLINT default null,
	`type` SMALLINT,
	`value` INTEGER,
	PRIMARY KEY (`id`),
	KEY `status_type_index`(`type`),
	KEY `scheduler_id_index`(`scheduler_id`),
	KEY `worker_id_index_type`(`worker_id`, `worker_type`)
)Type=MyISAM;


CREATE TABLE `scheduler_config`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`command_id` INTEGER default null,
	`command_status` TINYINT,
	`scheduler_id` INTEGER,
	`scheduler_name` VARCHAR(20),
	`worker_id` INTEGER default null,
	`worker_name` VARCHAR(50) default 'null',
	`variable` VARCHAR(100),
	`variable_part` VARCHAR(100),
	`value` VARCHAR(255),
	PRIMARY KEY (`id`)
)Type=MyISAM;


CREATE TABLE `control_panel_command`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`created_by_id` INTEGER,
	`scheduler_id` INTEGER default null,
	`worker_id` INTEGER default null,
	`worker_name` VARCHAR(50) default 'null',
	`type` SMALLINT,
	`target_type` SMALLINT,
	`status` SMALLINT,
	`cause` VARCHAR(255),
	`description` VARCHAR(255),
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`)
)Type=MyISAM;


CREATE TABLE `priority_group`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`name` VARCHAR(20),
	`description` VARCHAR(100),
	`priority` TINYINT,
	`bulk_priority` TINYINT,
	PRIMARY KEY (`id`)
)Type=MyISAM;


CREATE TABLE `work_group`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(20),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(20),
	`name` VARCHAR(20),
	`description` VARCHAR(100),
	PRIMARY KEY (`id`)
)Type=MyISAM;


ALTER TABLE  `batch_job` ADD  `created_by` VARCHAR( 20 ) NOT NULL AFTER  `created_at`;
ALTER TABLE  `batch_job` ADD  `updated_by` VARCHAR( 20 ) NOT NULL AFTER  `updated_at`;
ALTER TABLE  `batch_job` ADD  `priority` TINYINT NOT NULL AFTER  `updated_by`;
ALTER TABLE  `batch_job` ADD  `work_group_id` INTEGER NOT NULL AFTER  `priority`;
ALTER TABLE  `batch_job` ADD  `queue_time` DATETIME NULL DEFAULT NULL AFTER  `work_group_id`;
ALTER TABLE  `batch_job` ADD  `finish_time` DATETIME NULL DEFAULT NULL AFTER  `queue_time`;
ALTER TABLE  `batch_job` ADD  `twin_job_id` INTEGER NULL DEFAULT NULL AFTER  `lock_version`;
ALTER TABLE  `batch_job` ADD  `bulk_job_id` INTEGER NULL DEFAULT NULL AFTER  `twin_job_id`;
ALTER TABLE  `batch_job` ADD  `root_job_id` INTEGER NULL DEFAULT NULL AFTER  `bulk_job_id`;

#-----------------------------------------------------------------------------
#-- ALTER TABLE  `batch_job` DROP INDEX  `status_job_type_index`;
#-- ALTER TABLE  `batch_job` DROP INDEX  `created_at_job_type_status_index`;
#-- ALTER TABLE  `batch_job` DROP INDEX  `partner_type_index`;
#-----------------------------------------------------------------------------

ALTER TABLE  `batch_job` ADD INDEX `partner_id_index` (`partner_id`);
ALTER TABLE  `batch_job` ADD INDEX `work_group_id_index_priority` (`work_group_id`,  `priority`);
ALTER TABLE  `batch_job` ADD INDEX `twin_job_id_index` (`twin_job_id`);
ALTER TABLE  `batch_job` ADD INDEX `bulk_job_id_index` (`bulk_job_id`); 
ALTER TABLE  `batch_job` ADD INDEX `root_job_id_index` (`root_job_id`);
ALTER TABLE  `batch_job` ADD INDEX `parent_job_id_index` (`parent_job_id`);


ALTER TABLE  `partner` ADD  `priority_group_id` INTEGER NOT NULL AFTER  `monitor_usage`;
ALTER TABLE  `partner` ADD  `work_group_id` INTEGER NOT NULL AFTER  `priority_group_id`;


