ALTER TABLE `entry` ADD `available_from` DATETIME AFTER `end_date`;

update entry set available_from = start_date;
update entry set available_from = created_at where available_from is null;