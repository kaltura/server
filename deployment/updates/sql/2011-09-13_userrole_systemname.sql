ALTER TABLE  `user_role` 
ADD `system_name` VARCHAR(128) NULL AFTER `name`; 
UPDATE user_role SET system_name = NAME WHERE system_name IS NULL;