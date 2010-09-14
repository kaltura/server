ALTER TABLE conversion_profile_2 ADD COLUMN `creation_mode` SMALLINT DEFAULT 1;

ALTER TABLE conversion_profile ADD COLUMN `conversion_profile_2_id` integer DEFAULT NULL;
