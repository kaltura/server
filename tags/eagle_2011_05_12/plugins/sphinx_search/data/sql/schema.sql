
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- sphinx_log
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `sphinx_log`;


CREATE TABLE `sphinx_log`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`entry_id` VARCHAR(20),
	`partner_id` INTEGER default 0,
	`dc` INTEGER,
	`sql` LONGTEXT,
	`created_at` DATETIME,
	PRIMARY KEY (`id`),
	KEY `entry_id`(`entry_id`),
	KEY `creatd_at`(`created_at`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- sphinx_log_server
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `sphinx_log_server`;


CREATE TABLE `sphinx_log_server`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`server` VARCHAR(63),
	`dc` INTEGER,
	`last_log_id` INTEGER,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `sphinx_log_server_FI_1` (`last_log_id`),
	CONSTRAINT `sphinx_log_server_FK_1`
		FOREIGN KEY (`last_log_id`)
		REFERENCES `sphinx_log` (`id`)
)Type=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
