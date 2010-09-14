ALTER TABLE `scheduler` ADD `last_status` DATETIME NOT NULL AFTER `statuses`;

ALTER TABLE `scheduler_worker` ADD `last_status` DATETIME NOT NULL AFTER `statuses`;