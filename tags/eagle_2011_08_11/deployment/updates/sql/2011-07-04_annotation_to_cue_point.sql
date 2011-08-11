
CREATE TABLE `cue_point`
(
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`id` VARCHAR(255)  NOT NULL,
	`parent_id` VARCHAR(255),
	`entry_id` VARCHAR(31)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`name` VARCHAR(255),
	`system_name` VARCHAR(127),
	`text` TEXT,
	`tags` VARCHAR(255),
	`start_time` INTEGER  NOT NULL,
	`end_time` INTEGER,
	`status` INTEGER  NOT NULL,
	`type` INTEGER  NOT NULL,
	`sub_type` INTEGER  NOT NULL,
	`kuser_id` INTEGER  NOT NULL,
	`custom_data` TEXT,
	`partner_data` TEXT,
	`partner_sort_value` INTEGER,
	`thumb_offset` INTEGER,
	PRIMARY KEY (`id`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`),
	KEY `int_id_index`(`int_id`)
)Type=MyISAM;


INSERT INTO cue_point (int_id, id, parent_id, entry_id, partner_id, created_at, updated_at, TEXT, tags, start_time, end_time, STATUS, kuser_id, custom_data, partner_data)
SELECT int_id, id, parent_id, entry_id, partner_id, created_at, updated_at, TEXT, tags, start_time, end_time, STATUS, kuser_id, custom_data, partner_data
FROM annotation;


UPDATE cue_point
SET TYPE = (
	SELECT id
	FROM dynamic_enum
	WHERE enum_name = 'CuePointType'
	AND value_name = 'Annotation'
	AND plugin_name = 'annotation'
	LIMIT 1
);
