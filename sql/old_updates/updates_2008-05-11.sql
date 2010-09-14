# not relevant - we will create the tables anyway
#alter table moderation modify object_id varchar(10);
#alter table notification modify object_id varchar(10);

alter table batch_job add  column `partner_id` int,add  column `subp_id` int;

#alter table entry add  column `desired_status` smallint(6);

alter table entry alter  column plays set default 0 ;
alter table kshow alter  column status set default 0, alter  column plays set default 0;
alter table kuser modify column email varchar(50);


# new schema for moderation & notification tables - we never used the previous one on production

#-----------------------------------------------------------------------------
#-- moderation
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `moderation`;


CREATE TABLE `moderation`
(
`id` INTEGER  NOT NULL AUTO_INCREMENT,
`partner_id` INTEGER,
`subp_id` INTEGER,
`object_id` VARCHAR(10),
`object_type` SMALLINT,
`kuser_id` INTEGER,
`puser_id` VARCHAR(64),
`status` INTEGER,
`created_at` DATETIME,
`updated_at` DATETIME,
`comments` VARCHAR(1024),
`group_id` VARCHAR(64),
PRIMARY KEY (`id`),
KEY `partner_id_status_index`(`partner_id`, `status`),
KEY `partner_id_group_id_status_index`(`partner_id`, `group_id`, `status`),
KEY `object_index`(`partner_id`, `status`, `object_id`, `object_type`),
INDEX `moderation_FI_1` (`kuser_id`),
CONSTRAINT `moderation_FK_1`
FOREIGN KEY (`kuser_id`)
REFERENCES `kuser` (`id`)
)Type=MyISAM;


#-----------------------------------------------------------------------------
#-- notification
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `notification`;


CREATE TABLE `notification`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`puser_id` VARCHAR(64),
	`type` SMALLINT,
	`object_id` VARCHAR(10),
	`status` INTEGER,
	`notification_data` VARCHAR(4096),
	`number_of_attempts` SMALLINT default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`notification_result` VARCHAR(256),
	PRIMARY KEY (`id`),
	KEY `status_partner_id_index`(`status`, `partner_id`)
)Type=MyISAM;




