CREATE TABLE IF NOT EXISTS version_management (
	`version` INT(11),
	`filename` VARCHAR(250) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`filename`)
);

INSERT IGNORE INTO version_management(VERSION, filename) VALUES(5999, 'create_version_mng_table.sql');
