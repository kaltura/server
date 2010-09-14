ALTER TABLE `conversion_profile_2` ADD `input_tags_map` VARCHAR(1023) default NULL AFTER `clip_duration`;

ALTER TABLE `flavor_params_conversion_profile` ADD `force` INTEGER default 0 AFTER `ready_behavior`;
ALTER TABLE `flavor_params_conversion_profile` ADD `created_at` DATETIME AFTER `force`;
ALTER TABLE `flavor_params_conversion_profile` ADD `updated_at` DATETIME AFTER `created_at`;

ALTER TABLE `flavor_params_output` CHANGE `frame_rate` `frame_rate` FLOAT NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_codec` `audio_codec` VARCHAR(20) NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_bitrate` `audio_bitrate` INTEGER NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_channels` `audio_channels` TINYINT NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_sample_rate` `audio_sample_rate` INTEGER NULL DEFAULT NULL;
ALTER TABLE `flavor_params_output` CHANGE `audio_resolution` `audio_resolution` INTEGER NULL DEFAULT NULL;

ALTER TABLE `flavor_params_output` ADD `command_lines` VARCHAR(2047) default NULL AFTER `custom_data`;