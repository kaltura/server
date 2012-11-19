update batch_job_lock set job_sub_type=0 where job_sub_type is null;
ALTER TABLE batch_job_lock CHANGE job_sub_type job_sub_type INT( 6 ) NOT NULL DEFAULT  '0';

update batch_job_sep set job_sub_type=0 where job_sub_type is null;
ALTER TABLE batch_job_sep CHANGE job_sub_type job_sub_type INT( 6 ) NOT NULL DEFAULT  '0';
