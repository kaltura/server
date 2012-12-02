ALTER TABLE `drop_folder` ADD COLUMN `error_code` INTEGER AFTER `tags`;
ALTER TABLE `drop_folder` ADD COLUMN `error_description` TEXT AFTER `error_code`;

ALTER TABLE `drop_folder_file` ADD COLUMN `lead_drop_folder_file_id` INTEGER AFTER `parsed_flavor`;
ALTER TABLE `drop_folder_file` ADD COLUMN `deleted_drop_folder_file_id` INTEGER NOT NULL DEFAULT '0' AFTER `lead_drop_folder_file_id`;
ALTER TABLE `drop_folder_file` ADD COLUMN `md5_file_name` VARCHAR(32) AFTER `deleted_drop_folder_file_id`;
ALTER TABLE `drop_folder_file` ADD COLUMN `entry_id` VARCHAR(20) AFTER `md5_file_name`;
ALTER TABLE `drop_folder_file` ADD COLUMN `upload_start_detected_at` DATETIME AFTER `updated_at`;
ALTER TABLE `drop_folder_file` ADD COLUMN `upload_end_detected_at` DATETIME AFTER `upload_start_detected_at`;
ALTER TABLE `drop_folder_file` ADD COLUMN `import_started_at` DATETIME AFTER `upload_end_detected_at`;
ALTER TABLE `drop_folder_file` ADD COLUMN `import_ended_at` DATETIME AFTER `import_started_at`;

UPDATE `drop_folder_file` SET `deleted_drop_folder_file_id` = `id` WHERE `status` = '7';
UPDATE `drop_folder_file` SET `md5_file_name` = md5(`file_name`);

ALTER TABLE `drop_folder_file` ADD UNIQUE KEY `file_name_in_drop_folder_unique` (`md5_file_name`, `drop_folder_id`, `deleted_drop_folder_file_id`);