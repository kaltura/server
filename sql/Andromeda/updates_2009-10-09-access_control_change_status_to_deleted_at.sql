ALTER TABLE access_control DROP COLUMN `status`;
ALTER TABLE access_control ADD COLUMN `deleted_at` DATETIME default null AFTER `updated_at`;