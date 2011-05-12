
#-----------------------------------------------------------------------------
#-- scheduler
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler`;


CREATE TABLE `scheduler`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(127),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(127),
	`configured_id` INTEGER,
	`name` VARCHAR(127) default '',
	`description` VARCHAR(255) default '',
	`statuses` VARCHAR(1023) default '',
	`last_status` DATETIME,
	`host` VARCHAR(255) default '',
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler_worker
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler_worker`;


CREATE TABLE `scheduler_worker`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(127),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(127),
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`configured_id` INTEGER,
	`type` INTEGER,
	`name` VARCHAR(127) default '',
	`description` VARCHAR(255) default '',
	`statuses` VARCHAR(1023) default '',
	`last_status` DATETIME,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler_status
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler_status`;


CREATE TABLE `scheduler_status`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(127),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(127),
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`worker_id` INTEGER,
	`worker_configured_id` INTEGER,
	`worker_type` INTEGER,
	`type` INTEGER,
	`value` INTEGER,
	PRIMARY KEY (`id`),
	KEY `status_type_index`(`type`),
	KEY `status_created_at_index`(`created_at`),
	KEY `scheduler_id_index`(`scheduler_id`),
	KEY `worker_id_index_type`(`worker_id`, `worker_type`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- scheduler_config
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `scheduler_config`;


CREATE TABLE `scheduler_config`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(127),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(127),
	`command_id` INTEGER,
	`command_status` TINYINT,
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`scheduler_name` VARCHAR(127),
	`worker_id` INTEGER,
	`worker_configured_id` INTEGER,
	`worker_name` VARCHAR(127),
	`variable` VARCHAR(127),
	`variable_part` VARCHAR(127),
	`value` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `status_variable_index`(`variable`, `variable_part`),
	KEY `status_created_at_index`(`created_at`),
	KEY `scheduler_id_index`(`scheduler_id`),
	KEY `worker_id_index_type`(`worker_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- control_panel_command
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `control_panel_command`;


CREATE TABLE `control_panel_command`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`created_by` VARCHAR(127),
	`updated_at` DATETIME,
	`updated_by` VARCHAR(127),
	`created_by_id` INTEGER,
	`scheduler_id` INTEGER,
	`scheduler_configured_id` INTEGER,
	`worker_id` INTEGER,
	`worker_configured_id` INTEGER,
	`worker_name` VARCHAR(127),
	`batch_index` INTEGER,
	`type` INTEGER,
	`target_type` INTEGER,
	`status` INTEGER,
	`cause` VARCHAR(255),
	`description` VARCHAR(255),
	`error_description` VARCHAR(255),
	PRIMARY KEY (`id`)
)Type=MyISAM;
