ALTER TABLE `ui_conf`
	ADD `reference_id` VARCHAR(512) AFTER `partner_tags`,
	ADD INDEX `partner_id_reference_id_index`(`partner_id`, `reference_id`);
