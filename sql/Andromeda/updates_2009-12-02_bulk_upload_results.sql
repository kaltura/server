ALTER TABLE `bulk_upload_result` 
ADD `conversion_profile_id` INT NOT NULL AFTER `content_type` ,
ADD `access_control_profile_id` INT NOT NULL AFTER `conversion_profile_id` ,
ADD `category` VARCHAR( 128 ) NOT NULL AFTER `access_control_profile_id` ,
ADD `schedule_start_date` DATETIME NOT NULL AFTER `category` ,
ADD `schedule_end_date` DATETIME NOT NULL AFTER `schedule_start_date` ,
ADD `thumbnail_url` VARCHAR( 255 ) NOT NULL AFTER `schedule_end_date` ,
ADD `thumbnail_saved` INT NOT NULL AFTER `thumbnail_url`,
ADD `partner_data` VARCHAR( 4096 ) NOT NULL AFTER `thumbnail_saved`;

ALTER TABLE `bulk_upload_result` ADD `entry_status` INT NOT NULL AFTER `entry_id`;