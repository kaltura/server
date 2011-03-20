ALTER TABLE  `metadata_profile` 
ADD  `system_name` VARCHAR( 127 ) NOT NULL DEFAULT  '' AFTER  `name` ,
ADD  `description` VARCHAR( 255 ) NOT NULL DEFAULT  '' AFTER  `system_name` ,
ADD  `create_mode` INT NOT NULL DEFAULT  '1' AFTER  `object_type`;