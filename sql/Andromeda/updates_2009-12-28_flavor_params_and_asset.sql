ALTER TABLE  `flavor_asset` ADD  `file_ext` VARCHAR( 4 ) NULL DEFAULT NULL AFTER  `is_original`;
ALTER TABLE  `flavor_params_output` ADD  `file_ext` VARCHAR( 4 ) NULL DEFAULT NULL AFTER  `command_lines`;
ALTER TABLE  `flavor_params` ADD  `view_order` INT DEFAULT 0 AFTER  `custom_data`;
