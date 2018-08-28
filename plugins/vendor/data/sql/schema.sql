
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- vendor_integration
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `vendor_integration`;


CREATE TABLE `vendor_integration`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`account_id` VARCHAR(64)  NOT NULL,
	`partner_id` INTEGER  NOT NULL,
	`vendor_Type` TINYINT  NOT NULL,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `account_id_partner_id_vendor_Type_index`(`account_id`, `partner_id`, `vendor_Type`),
	KEY `partner_id_vendor_Type_index`(`partner_id`, `vendor_Type`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
