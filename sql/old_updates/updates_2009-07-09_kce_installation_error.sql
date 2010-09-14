CREATE TABLE `kce_installation_error`

(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,

	`partner_id` INTEGER,

	`browser` VARCHAR(100),

	`server_ip` VARCHAR(20),

	`server_os` VARCHAR(100),

	`php_version` VARCHAR(20),

	`ce_admin_email` VARCHAR(50),

	`type` VARCHAR(50),

	`description` VARCHAR(100),

	`data` TEXT,

	PRIMARY KEY (`id`),

	KEY `partner_id_index`(`partner_id`),

	KEY `server_os_index`(`server_os`),

	KEY `php_version_index`(`php_version`),

	KEY `type_index`(`type`)

);