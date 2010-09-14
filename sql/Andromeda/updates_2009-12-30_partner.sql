ALTER TABLE  `partner` 	
	ADD `partner_group_type` SMALLINT default 1,
	ADD `partner_parent_id` INTEGER default null,
	ADD KEY `partner_parent_index`(`partner_parent_id`);
