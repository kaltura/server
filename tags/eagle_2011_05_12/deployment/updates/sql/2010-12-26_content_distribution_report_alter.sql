ALTER TABLE  `distribution_profile` ADD  `report_interval` INT NOT NULL AFTER  `required_thumb_dimensions`;

ALTER TABLE  `entry_distribution` ADD  `last_report` DATETIME NOT NULL AFTER  `error_description`;