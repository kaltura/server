alter table kuser add	(
	`puser_id` VARCHAR(64),
	`admin_tags` TEXT,
	`indexed_partner_data_int` INTEGER,
	`indexed_partner_data_string` VARCHAR(64),
	KEY `partner_indexed_partner_data_int`(`partner_id`, `indexed_partner_data_int`),
	KEY `partner_indexed_partner_data_string`(`partner_id`, `indexed_partner_data_string`),
	KEY `partner_puser_id`(`partner_id`, `puser_id`)
	);
	
	
alter table entry add(
	`puser_id` VARCHAR(64)
);