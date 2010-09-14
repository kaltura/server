ALTER TABLE `batch_job` ADD `err_type` INTEGER default 0 NOT NULL AFTER `dc`;
ALTER TABLE `batch_job` ADD `err_number` INTEGER default 0 NOT NULL AFTER `err_type`;
