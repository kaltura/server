CREATE TABLE `category`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER  NOT NULL,
	`depth` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(128) default '' NOT NULL,
	`full_name` VARCHAR(512) default '' NOT NULL,
	`entries_count` INTEGER default 0 NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`deleted_at` DATETIME default NULL,
	PRIMARY KEY (`id`),
	KEY `partner_id_full_name_index`(`partner_id`, `full_name`)
)Type=MyISAM;

ALTER TABLE entry
 ADD categories VARCHAR(4096) AFTER conversion_profile_id;
 
ALTER TABLE entry
 ADD categories_ids VARCHAR(1024) AFTER categories;

ALTER TABLE entry
 ADD search_text_discrete VARCHAR(4096) AFTER categories_ids;
 
ALTER TABLE entry 
 ADD FULLTEXT INDEX search_text_discrete_index (search_text_discrete);