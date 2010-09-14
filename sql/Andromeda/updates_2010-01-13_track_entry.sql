#-- track_entry
#-----------------------------------------------------------------------------
DROP TABLE IF EXISTS `track_entry`;
CREATE TABLE `track_entry`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`track_event_type_id` SMALLINT,
	`ps_version` VARCHAR(10),
	`context` VARCHAR(511),
	`partner_id` INTEGER,
	`entry_id` VARCHAR(20),
	`host_name` VARCHAR(20),
	`uid` VARCHAR(63),
	`track_event_status_id` SMALLINT,
	`changed_properties` VARCHAR(1023),
	`param_1_str` VARCHAR(255),
	`param_2_str` VARCHAR(511),
	`param_3_str` VARCHAR(511),
	`ks` VARCHAR(511),
	`description` VARCHAR(127),
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `partner_event_type_indx`(`partner_id`,`track_event_type_id`),
	KEY `entry_id_indx`(`entry_id`),
	KEY `track_event_type_id_indx`(`track_event_type_id`),
	KEY `param_1_indx`(`param_1_str`)
)Type=MyISAM;