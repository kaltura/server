ALTER TABLE `system_user` ADD `is_primary` TINYINT DEFAULT 0 AFTER `status`;
update system_user set is_primary = 1 where id = 1;