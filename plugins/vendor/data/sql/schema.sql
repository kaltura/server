
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- vendor_integration
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `vendor_integration`;


CREATE TABLE `vendor_integration`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `account_id` varchar(64) NOT NULL,
    `partner_id` int(11) NOT NULL,
    `vendor_type` smallint(6) NOT NULL,
    `custom_data` text,
    `status` tinyint(4) NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `partner_id_vendor_type_status_index` (`partner_id`,`vendor_type`,`status`),
    KEY `account_id_vendor_type_index` (`account_id`,`vendor_type`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
