# add new table - partner_transactions
CREATE TABLE `partner_transactions`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`partner_id` INTEGER,
	`package_id` TINYINT,
	`type` TINYINT,
	`created_at` DATETIME,
	`amount` FLOAT,
	`currency` VARCHAR(6),
	`transaction_id` VARCHAR(17),
	`timestamp` DATETIME,
	`correlation_id` VARCHAR(12),
	`ack` VARCHAR(20),
	`transaction_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)