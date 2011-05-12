ALTER TABLE  `annotation`
ADD `partner_data` TEXT AFTER `custom_data`,
ADD	`type` TINYINT  NOT NULL DEFAULT  '1' AFTER `status`;