alter table mail_job
add `processor_name` varchar(64),
add `processor_location` VARCHAR(64),
add `processor_expiration` DATETIME,
add	`execution_attempts` TINYINT,
add `lock_version` INTEGER;
