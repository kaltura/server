alter table batch_job_sep modify id bigint NOT NULL AUTO_INCREMENT, modify parent_job_id bigint DEFAULT NULL, modify bulk_job_id bigint DEFAULT NULL, modify root_job_id bigint DEFAULT NULL,modify batch_job_lock_id bigint DEFAULT NULL;

alter table batch_job_log modify id bigint NOT NULL AUTO_INCREMENT,modify job_id bigint DEFAULT NULL,modify twin_job_id bigint DEFAULT NULL,modify bulk_job_id bigint DEFAULT NULL,modify root_job_id bigint DEFAULT NULL,modify parent_job_id bigint DEFAULT NULL;

alter table batch_job_lock modify id bigint NOT NULL, modify batch_job_id bigint NOT NULL;