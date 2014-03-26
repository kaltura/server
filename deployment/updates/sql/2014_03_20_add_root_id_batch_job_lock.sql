ALTER TABLE `batch_job_lock` ADD `root_job_id` bigint(20) AFTER `batch_version`;
ALTER TABLE `batch_job_lock_suspend` ADD `root_job_id` bigint(20) AFTER `batch_version`;
CREATE INDEX root_job_id_index ON batch_job_lock (root_job_id);
CREATE INDEX root_job_id_index ON batch_job_lock_suspend (root_job_id);
