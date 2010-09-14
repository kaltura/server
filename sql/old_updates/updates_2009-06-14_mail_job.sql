alter table mail_job add	(
	`partner_id` INTEGER default 0,
	`updated_at` DATETIME,
	KEY `partner_id_index`(`partner_id`)
	);
