ALTER TABLE `bulk_upload_result`
ADD COLUMN `entry_status` INTEGER,
ADD COLUMN `title` VARCHAR(127),
ADD COLUMN `description` VARCHAR(255),
ADD COLUMN `tags` VARCHAR(255),
ADD COLUMN `url` VARCHAR(255),
ADD COLUMN `content_type` VARCHAR(31),
ADD COLUMN `conversion_profile_id` INTEGER,
ADD COLUMN `access_control_profile_id` INTEGER,
ADD COLUMN `category` VARCHAR(128),
ADD COLUMN `schedule_start_date` DATETIME,
ADD COLUMN `schedule_end_date` DATETIME,
ADD COLUMN `thumbnail_url` VARCHAR(255),
ADD COLUMN `thumbnail_saved` TINYINT;