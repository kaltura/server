ALTER TABLE `system_user` ADD COLUMN `role` VARCHAR(40) default 'ps' AFTER `deleted_at`;
UPDATE `system_user` SET role = 'admin';