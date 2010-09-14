alter table widget add  column `security_type` SMALLINT;
alter table moderation add column 	`puser_id` VARCHAR(64);

#-----------------------------------------------------------------------------
#-- widget
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `widget`;


CREATE TABLE `widget`
(
	`id` VARCHAR(24)  NOT NULL,
	`int_id` INTEGER  NOT NULL AUTO_INCREMENT,
	`source_widget_int_id` INTEGER,
	`root_widget_int_id` INTEGER,
	`partner_id` INTEGER,
	`subp_id` INTEGER,
	`kshow_id` INTEGER,
	`entry_id` INTEGER,
	`ui_conf_id` INTEGER,
	`custom_data` VARCHAR(1024),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `int_id_index`(`int_id`),
	INDEX `widget_FI_1` (`kshow_id`),
CONSTRAINT `widget_FK_1`
FOREIGN KEY (`kshow_id`)
REFERENCES `kshow` (`id`),
INDEX `widget_FI_2` (`entry_id`),
CONSTRAINT `widget_FK_2`
FOREIGN KEY (`entry_id`)
REFERENCES `entry` (`id`),
INDEX `widget_FI_3` (`ui_conf_id`),
CONSTRAINT `widget_FK_3`
FOREIGN KEY (`ui_conf_id`)
REFERENCES `ui_conf` (`id`)
)Type=MyISAM;



#-----------------------------------------------------------------------------
#-- kwidget_log
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `kwidget_log`;


CREATE TABLE `kwidget_log`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`widget_id` VARCHAR(24),
	`source_widget_int_id` INTEGER,
	`root_widget_int_id` INTEGER,
	`kshow_id` INTEGER,
	`entry_id` INTEGER,
	`ui_conf_id` INTEGER,
	`referer` VARCHAR(1024),
	`views` INTEGER default 0,
	`ip1` INTEGER,
	`ip1_count` INTEGER default 0,
	`ip2` INTEGER,
	`ip2_count` INTEGER default 0,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`plays` INTEGER default 0,
	`partner_id` INTEGER default 0,
	`subp_id` INTEGER default 0,
	PRIMARY KEY (`id`),
	KEY `referer_index`(`referer`),
	KEY `entry_id_kshow_id_index`(`entry_id`, `kshow_id`),
	KEY `partner_id_subp_id_index`(`partner_id`, `subp_id`),
	INDEX `kwidget_log_FI_1` (`widget_id`),
CONSTRAINT `kwidget_log_FK_1`
FOREIGN KEY (`widget_id`)
REFERENCES `widget` (`id`),
INDEX `kwidget_log_FI_2` (`kshow_id`),
CONSTRAINT `kwidget_log_FK_2`
FOREIGN KEY (`kshow_id`)
REFERENCES `kshow` (`id`),
CONSTRAINT `kwidget_log_FK_3`
FOREIGN KEY (`entry_id`)
REFERENCES `entry` (`id`),
INDEX `kwidget_log_FI_4` (`ui_conf_id`),
CONSTRAINT `kwidget_log_FK_4`
FOREIGN KEY (`ui_conf_id`)
REFERENCES `ui_conf` (`id`)
)Type=MyISAM;
