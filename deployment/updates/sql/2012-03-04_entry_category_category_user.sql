ALTER TABLE entry
ADD `creator_kuser_id` INTEGER,
ADD `creator_puser_id` VARCHAR(64),
ADD	`entitled_users_edit` TEXT,
ADD	`entitled_users_publish` TEXT;
	
ALTER TABLE category
ADD `status` INTEGER,
ADD `direct_entries_count` INTEGER default 0,
ADD `members_count` INTEGER default 0,
ADD `pending_members_count` INTEGER default 0,
ADD `description` TEXT,
ADD `tags` TEXT,
ADD `listing` TINYINT default 1,
ADD `privacy` TINYINT default 1,
ADD `membership_setting` TINYINT default 2,
ADD `user_join_policy` TINYINT default 3,
ADD `default_permission_level` TINYINT default 3,
ADD `kuser_id` INTEGER,
ADD `reference_id` VARCHAR(512),
ADD `contribution_policy` TINYINT default 2,
ADD `custom_data` TEXT,
ADD `privacy_context` TINYINT default 0,
ADD `privacy_contexts` VARCHAR(255),
ADD  KEY `partner_id_full_name_index`(`partner_id`, `full_name`);
	
CREATE TABLE `category_kuser`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`category_id` INTEGER  NOT NULL,
	`kuser_id` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`permission_level` TINYINT,
	`status` TINYINT,
	`inherit_from_category` INTEGER,
	`update_method` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_category_index`(`partner_id`, `category_id`, `status`),
	KEY `partner_id_kuser_index`(`partner_id`, `kuser_id`, `status`)
)Type=MyISAM;


UPDATE category SET STATUS=2 WHERE deleted_at IS NULL;
UPDATE category SET STATUS=3 WHERE deleted_at IS NOT NULL;
UPDATE category SET 
`listing`=1,
`privacy`=1,
`membership_setting`=2,
`user_join_policy`=3,
`default_permission_level`=3,
`contribution_policy`=2,
`privacy_context`=0;