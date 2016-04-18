
CREATE TABLE schedule_event
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER,
	`partner_id` INTEGER  NOT NULL,
	`summary` VARCHAR(256),
	`description` TEXT,
	`type` INTEGER,
	`status` INTEGER,
	`original_start_date` DATETIME  NOT NULL,
	`start_date` DATETIME,
	`end_date` DATETIME,
	`reference_id` VARCHAR(256),
	`classification_type` INTEGER,
	`geo_lat` FLOAT,
	`geo_long` FLOAT,
	`location` VARCHAR(256),
	`organizer` VARCHAR(256),
	`owner_kuser_id` INTEGER,
	`priority` INTEGER,
	`sequence` INTEGER,
	`recurrence_type` INTEGER  NOT NULL,
	`duration` INTEGER,
	`contact` VARCHAR(1024),
	`comment` TEXT,
	`tags` TEXT,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_status_recurrence_index`(`partner_id`, `status`, `recurrence_type`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE schedule_resource
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`parent_id` INTEGER,
	`partner_id` INTEGER  NOT NULL,
	`name` VARCHAR(256)  NOT NULL,
	`system_name` VARCHAR(256)  NOT NULL,
	`description` TEXT,
	`tags` TEXT,
	`type` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_status_type_index`(`partner_id`, `status`, `type`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE schedule_event_resource
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`event_id` INTEGER  NOT NULL,
	`resource_id` INTEGER  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_event_index`(`partner_id`, `event_id`),
	KEY `partner_resource_index`(`partner_id`, `resource_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8;
