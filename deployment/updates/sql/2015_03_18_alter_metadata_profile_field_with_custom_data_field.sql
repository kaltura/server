ALTER TABLE `metadata_profile_field`
ADD `custom_data` TEXT,
ADD `related_metadata_profile_id` INTEGER
AFTER `status`;