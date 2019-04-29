alter table batch_job_lock add index entry(entry_id), add index job_type_status(job_type,status);
