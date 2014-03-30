ALTER TABLE `batch_job_lock` 
	ADD `root_job_id` bigint(20) AFTER `batch_version`,
	ADD INDEX `root_job_id_index` (`root_job_id`);
ALTER TABLE `batch_job_lock_suspend` 
	ADD `root_job_id` bigint(20) AFTER `batch_version`,
	ADD INDEX `root_job_id_index` (`root_job_id`);
