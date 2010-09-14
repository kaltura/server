alter table batch_job
add 	`processor_location` VARCHAR(64),
add	`execution_attempts` TINYINT,
add `lock_version` INTEGER;


