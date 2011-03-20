ALTER TABLE  `access_control` 
ADD  `system_name` VARCHAR( 128 ) NOT NULL DEFAULT  '' AFTER  `name` ,
ADD  `custom_data` TEXT AFTER  `kdir_restrict_type`;

ALTER TABLE  `storage_profile` 
ADD  `system_name` VARCHAR( 128 ) NOT NULL DEFAULT  '' AFTER  `name`;

ALTER TABLE  `distribution_profile` 
ADD  `system_name` VARCHAR( 128 ) NOT NULL DEFAULT  '' AFTER  `name`;
