
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- vendor_catalog_item
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `vendor_catalog_item`;


CREATE TABLE `vendor_catalog_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(256)  NOT NULL,
	`system_name` VARCHAR(256),
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`status` TINYINT  NOT NULL,
	`vendor_partner_id` INTEGER  NOT NULL,
	`service_type` TINYINT  NOT NULL,
	`service_feature` TINYINT  NOT NULL,
	`turn_around_time` INTEGER  NOT NULL,
	`source_language` VARCHAR(256)  NOT NULL,
	`target_language` VARCHAR(256)  NOT NULL,
	`output_format` VARCHAR(256)  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `status_service_type_index`(`status`, `service_type`),
	KEY `status_service_type_service_feature_index`(`status`, `service_type`, `service_feature`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- partner_catalog_item
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `partner_catalog_item`;


CREATE TABLE `partner_catalog_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`status` TINYINT  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`catalog_item_id` INTEGER  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- vendor_profile
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `vendor_profile`;


CREATE TABLE `vendor_profile`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`type` TINYINT  NOT NULL,
	`status` TINYINT  NOT NULL,
	`used_credit` INTEGER  NOT NULL,
	`used_credit_period` INTEGER  NOT NULL,
	`rules` TEXT,
	`dictionary` TEXT,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `partner_id_type_index`(`partner_id`, `type`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- entry_vendor_task
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `entry_vendor_task`;


CREATE TABLE `entry_vendor_task`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME  NOT NULL,
	`updated_at` DATETIME  NOT NULL,
	`queue_time` DATETIME  NOT NULL,
	`finish_time` DATETIME  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`vendor_partner_id` INTEGER  NOT NULL,
	`entry_id` VARCHAR(31)  NOT NULL,
	`status` TINYINT  NOT NULL,
	`price` INTEGER  NOT NULL,
	`catalog_item_id` INTEGER  NOT NULL,
	`vendor_profile_id` INTEGER  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_status_index`(`partner_id`, `status`),
	KEY `vendor_partner_id_status_index`(`vendor_partner_id`, `status`),
	KEY `partner_entry_index`(`partner_id`, `entry_id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
