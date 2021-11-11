CREATE TABLE IF NOT EXISTS `virtual_event`
(
    `id` bigint(20)  NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(256),
    `description` varchar(1024),
    `partner_id` bigint(20),
    `status` tinyint(4),
    `tags` TEXT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `custom_data` TEXT,
    PRIMARY KEY (`id`),
    KEY `partner_id_index`(`partner_id`),
    KEY `status_partner_id_index`(`status`, `partner_id`),
    KEY `updated_at_index`(`updated_at`)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8;
