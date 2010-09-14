ALTER TABLE `bulk_upload_result` CHANGE `entry_id` `entry_id` varchar(20);
ALTER TABLE `bulk_upload_result` CHANGE `schedule_start_date` `schedule_start_date` DATETIME DEFAULT NULL;
ALTER TABLE `bulk_upload_result` CHANGE `schedule_end_date` `schedule_end_date` DATETIME DEFAULT NULL;