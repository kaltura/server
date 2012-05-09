ALTER TABLE `bulk_upload_result` 
ADD COLUMN `status` INTEGER,
ADD COLUMN `object_status` INTEGER,
ADD COLUMN `object_error_description` VARCHAR(255),
ADD COLUMN `error_code` INTEGER,
ADD COLUMN `error_type` INTEGER;