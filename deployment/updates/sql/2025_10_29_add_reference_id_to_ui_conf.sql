ALTER TABLE `ui_conf`
	ADD `reference_id` VARCHAR(512) AFTER `partner_tags`,
	ADD KEY `partner_id_reference_id_index`(`partner_id`, `reference_id`);
