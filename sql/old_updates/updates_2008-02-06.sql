# fix entry table:
# screen_name & site_url for anonymous users (kuser_id will point to an anonymouse kuser per partner)
# desired_status - what should be the status once the entry ends it cycle just before ENTRY_STATUS_READY. this is good for moderations
alter table entry add column (screen_name varchar(20), site_url varchar (256), permissions integer ,group_id varchar(64));
alter table entry add KEY `partner_group_index` (`partner_id`, `group_id` );

alter table kshow add column (permissions varchar (1024),group_id varchar(64) );
alter table kshow add KEY `partner_group_index` (`partner_id`, `group_id` );

# partner has a default if to moderate new entries or not 
# notify - should send notifications for changes in the system
alter table partner add column (moderate_content tinyint(4)  default 0, notify tinyint(4)  default 0);
alter table partner alter appear_in_search set default 1

# changes for widget_log - make it easy to search by partner
alter table widget_log add (partner_id integer default 0);
alter table widget_log add (subp_id integer default 0);
alter table widget_log add KEY `partner_id_subp_id_index`(`partner_id`, `subp_id`);



DROP TABLE IF EXISTS `notification`;

CREATE TABLE `notification`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`puser_id` VARCHAR(64),
	`type` SMALLINT,
	`object_id` INTEGER,
	`status` INTEGER,
	`notification_data` VARCHAR(4096),
	`number_of_attempts` SMALLINT default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`notification_result` VARCHAR(256),
	PRIMARY KEY (`id`),
	KEY `status_partner_id_index`(`status`, `partner_id`)
)Type=MyISAM;


DROP TABLE IF EXISTS `moderation`;


CREATE TABLE `moderation`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`object_id` INTEGER,
	`object_type` SMALLINT,
	`kuser_id` INTEGER,
	`puser_id` VARCHAR(64),
	`status` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`comments` VARCHAR(1024),
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `object_index`(`partner_id`, `status`, `object_id`, `object_type`),
	INDEX `moderation_FI_1` (`kuser_id`),
	CONSTRAINT `moderation_FK_1` FOREIGN KEY (`kuser_id`)  REFERENCES `kuser` (`id`)
)Type=MyISAM;
