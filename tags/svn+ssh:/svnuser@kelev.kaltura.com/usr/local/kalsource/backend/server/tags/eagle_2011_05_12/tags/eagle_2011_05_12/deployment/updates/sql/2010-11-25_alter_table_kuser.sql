ALTER TABLE  `kuser` 
ADD `login_data_id` INTEGER AFTER `id`,
ADD `first_name` VARCHAR(40) AFTER `screen_name`,
ADD `last_name` VARCHAR(40) AFTER `first_name`,
ADD `is_admin` TINYINT AFTER `login_data_id`,
ADD `custom_data` TEXT,
ADD KEY `login_data_id_index`(`login_data_id`),
ADD KEY `is_admin_index`(`is_admin`);