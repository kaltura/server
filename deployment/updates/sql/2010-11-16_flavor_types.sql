ALTER TABLE  `flavor_asset` ADD  `type` INT NOT NULL DEFAULT  '1',
ADD  `custom_data` TEXT NULL;

ALTER TABLE  `flavor_params` ADD  `type` INT NOT NULL DEFAULT  '1';
ALTER TABLE  `flavor_params_output` ADD  `type` INT NOT NULL DEFAULT  '1';