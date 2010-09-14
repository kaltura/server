ALTER TABLE  `flavor_params` ADD  `operators` TEXT NULL AFTER  `rotate` ,
ADD  `engine_version` SMALLINT NULL AFTER  `operators`;

ALTER TABLE  `flavor_params_output` ADD  `operators` TEXT NULL AFTER  `rotate` ,
ADD  `engine_version` SMALLINT NULL AFTER  `operators`;