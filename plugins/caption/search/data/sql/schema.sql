
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- caption_asset_item
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `caption_asset_item`;


CREATE TABLE `caption_asset_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME,
	`partner_id` INTEGER,
	`entry_id` VARCHAR(20),
	`caption_asset_id` VARCHAR(20),
	`content` VARCHAR(255),
	`start_time` INTEGER,
	`end_time` INTEGER,
	PRIMARY KEY (`id`),
	KEY `caption_asset`(`caption_asset_id`),
	KEY `partner_caption_asset`(`partner_id`, `caption_asset_id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
