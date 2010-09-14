/* so far was deployed on slaves only */

ALTER TABLE `batch_job` ADD INDEX `execution_attempts_index` ( `job_type` , `execution_attempts` );
ALTER TABLE `batch_job` ADD INDEX `processor_expiration_index` ( `job_type` , `processor_expiration` );
ALTER TABLE `batch_job` ADD INDEX `lock_index` ( `batch_index` , `scheduler_id` , `worker_id` );