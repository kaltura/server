ALTER TABLE `flavor_params` ADD `two_pass` INTEGER default 0 NOT NULL AFTER `gop_size`;
ALTER TABLE `flavor_params_output` ADD `two_pass` INTEGER default 0 NOT NULL AFTER `gop_size`;
