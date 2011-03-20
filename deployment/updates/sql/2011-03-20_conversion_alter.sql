ALTER TABLE  `conversion_profile_2` 
ADD  `system_name` VARCHAR( 128 ) NOT NULL DEFAULT  '' AFTER  `description` ,
ADD  `tags` TEXT NOT NULL DEFAULT  '' AFTER  `system_name` ,
ADD  `status` INT NOT NULL DEFAULT  '2' AFTER  `tags` ,
ADD  `default_entry_id` VARCHAR( 20 ) AFTER  `status` ,
ADD  `custom_data` TEXT AFTER  `creation_mode`;

ALTER TABLE `conversion_profile_2` ADD INDEX `partner_id_status` ( `partner_id` , `status` );

ALTER TABLE  `flavor_params` 
ADD  `system_name` VARCHAR( 128 ) NOT NULL DEFAULT  '' AFTER  `name`;

ALTER TABLE  `flavor_params_conversion_profile` 
ADD  `system_name` VARCHAR( 128 ) NOT NULL DEFAULT  '' AFTER  `flavor_params_id`,
ADD  `origin` TINYINT NOT NULL DEFAULT  '0' AFTER  `system_name`;