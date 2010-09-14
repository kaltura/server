
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
	KEY `creatd_at`(`created_at`),
	INDEX `sphinx_log_FI_1` (`partner_id`),
	CONSTRAINT `sphinx_log_FK_1`
		FOREIGN KEY (`partner_id`)
		REFERENCES `partner` (`id`),
	CONSTRAINT `sphinx_log_FK_2`
		FOREIGN KEY (`entry_id`)
		REFERENCES `entry` (`id`)
)Type=MyISAM;


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
