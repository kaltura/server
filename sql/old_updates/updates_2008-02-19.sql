#-----------------------------------------------------------------------------
#-- roughcut_entry
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `roughcut_entry`;


CREATE TABLE `roughcut_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`roughcut_id` INTEGER,
	`roughcut_version` INTEGER,
	`roughcut_kshow_id` INTEGER,
	`entry_id` INTEGER,
	`partner_id` INTEGER,
	`op_type` SMALLINT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `entry_id_index`(`entry_id`),
	KEY `roughcut_id_index`(`roughcut_id`),
	KEY `roughcut_kshow_id_index`(`roughcut_kshow_id`),
	CONSTRAINT `roughcut_entry_FK_1` FOREIGN KEY (`roughcut_id`) REFERENCES `entry` (`id`),
	CONSTRAINT `roughcut_entry_FK_2` FOREIGN KEY (`roughcut_kshow_id`) 	REFERENCES `kshow` (`id`),
	CONSTRAINT `roughcut_entry_FK_3` FOREIGN KEY (`entry_id`) 	REFERENCES `entry` (`id`)
)Type=MyISAM;


alter table partner add column (custom_data text);