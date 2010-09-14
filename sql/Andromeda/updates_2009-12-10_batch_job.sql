ALTER TABLE `batch_job` ADD `last_scheduler_id` INT default NULL AFTER `batch_index`; 
ALTER TABLE `batch_job` ADD `last_worker_id` INT default NULL AFTER `last_scheduler_id`; 
ALTER TABLE `batch_job` ADD `last_worker_remote` INT default 0 AFTER `last_worker_id`; 
ALTER TABLE `batch_job` ADD `file_size` INT default NULL AFTER `data`;