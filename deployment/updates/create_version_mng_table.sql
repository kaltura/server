CREATE TABLE IF NOT EXISTS version_management (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `filename` varchar(250) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `status` int(11) DEFAULT '1',
      `server_version` varchar(20) DEFAULT NULL,
      PRIMARY KEY (`id`)
);

INSERT IGNORE INTO version_management(filename) VALUES('create_version_mng_table.sql');
