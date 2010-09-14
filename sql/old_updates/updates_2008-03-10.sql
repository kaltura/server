alter table moderation add column  subp_id  integer;
alter table moderation add column  group_id varchar(64);
alter table moderation add KEY `partner_id_group_id_status_index` (`partner_id`, `group_id`, `status`); 

alter table kshow add column  plays integer;
alter table entry add column  plays integer;

#-----------------------------------------------------------------------------
#-- widget
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `widget`;


CREATE TABLE `widget`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`alias` VARCHAR(24),
	`source_widget_id` INTEGER,
	`root_widget_id` INTEGER,
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`kshow_id` INTEGER,
	`entry_id` INTEGER,
	`ui_conf_id` INTEGER default 1,
	`custom_data` VARCHAR(1024),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),  
	KEY `alias_index`(`alias`),    
	INDEX widget_FI_1 (`kshow_id`),   	CONSTRAINT `widget_FK_1`    FOREIGN KEY (`kshow_id`)	 	REFERENCES `kshow` (`id`),
	INDEX widget_FI_2 (`entry_id`),	   CONSTRAINT `widget_FK_2`	 	FOREIGN KEY (`entry_id`)	 	REFERENCES `entry` (`id`),
	INDEX widget_FI_3 (`ui_conf_id`), 	  CONSTRAINT `widget_FK_3`	FOREIGN KEY (`ui_conf_id`)	 	REFERENCES `ui_conf` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- ui_conf
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `ui_conf`;


CREATE TABLE `ui_conf`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`obj_type` SMALLINT,
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`conf_file_path` VARCHAR(48),
	`name` VARCHAR(24),
	`width` VARCHAR(10),
	`height` VARCHAR(10),
	`html_params` VARCHAR(256),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
