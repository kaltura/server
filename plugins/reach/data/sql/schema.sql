
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
	`is_default` TINYINT default 0,
	`partner_id` INTEGER  NOT NULL,
	`vendor_partner_id` INTEGER  NOT NULL,
	`service_type` TINYINT  NOT NULL,
	`service_feature` TINYINT  NOT NULL,
	`turn_around_time` INTEGER  NOT NULL,
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
	`partner_id` INTEGER  NOT NULL,
	`created_at` DATETIME  NOT NULL,
	`status` TINYINT  NOT NULL,
	`vendor_catalog_item_id` INTEGER  NOT NULL,
	PRIMARY KEY (`id`),
	KEY `partner_id`(`partner_id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
