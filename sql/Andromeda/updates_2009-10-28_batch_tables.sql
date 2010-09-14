

ALTER TABLE `batch_job` ADD `scheduler_id` INTEGER NULL DEFAULT NULL AFTER `subp_id` ,
ADD `worker_id` INTEGER NULL DEFAULT NULL AFTER `scheduler_id` ,
ADD `batch_index` INTEGER NULL DEFAULT NULL AFTER `worker_id` ;

ALTER TABLE `mail_job` ADD `scheduler_id` INTEGER NULL DEFAULT NULL AFTER `min_send_date` ,
ADD `worker_id` INTEGER NULL DEFAULT NULL AFTER `scheduler_id` ,
ADD `batch_index` INTEGER NULL DEFAULT NULL AFTER `worker_id` ;

ALTER TABLE `notification` ADD `scheduler_id` INTEGER NULL DEFAULT NULL AFTER `object_type` ,
ADD `worker_id` INTEGER NULL DEFAULT NULL AFTER `scheduler_id` ,
ADD `batch_index` INTEGER NULL DEFAULT NULL AFTER `worker_id` ;

ALTER TABLE `scheduler` ADD `configured_id` INTEGER NOT NULL AFTER `updated_by` ;

ALTER TABLE `scheduler_worker` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ,
ADD `configured_id` INTEGER NOT NULL AFTER `scheduler_configured_id` ;

ALTER TABLE `scheduler_status` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ;
ALTER TABLE `scheduler_status` ADD `worker_configured_id` INTEGER NOT NULL AFTER `worker_id` ;

ALTER TABLE `scheduler_config` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ;
ALTER TABLE `scheduler_config` ADD `worker_configured_id` INTEGER NOT NULL AFTER `worker_id` ;

ALTER TABLE `control_panel_command` ADD `scheduler_configured_id` INTEGER NOT NULL AFTER `scheduler_id` ;
ALTER TABLE `control_panel_command` ADD `worker_configured_id` INTEGER NOT NULL AFTER `worker_id` ;
