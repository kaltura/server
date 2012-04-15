ALTER TABLE entry
ADD `creator_kuser_id` INTEGER,
ADD `creator_puser_id` VARCHAR(64),
ADD	`entitled_users_edit` TEXT,
ADD	`entitled_users_publish` TEXT;
	