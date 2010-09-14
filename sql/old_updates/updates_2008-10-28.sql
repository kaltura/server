# add columns to batch_job for processor_name and processor_expiration
alter table batch_job add column (processor_name varchar(64), processor_expiration datetime);
