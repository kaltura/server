CREATE TABLE `user_entry`
(
        `id` INTEGER  NOT NULL AUTO_INCREMENT,
        `entry_id` VARCHAR(20),
        `kuser_id` INTEGER  NOT NULL,
        `partner_id` INTEGER,
        `created_at` DATETIME,
        `updated_at` DATETIME,
        `status` INTEGER,
        `type` INTEGER,
        `custom_data` TEXT,
        PRIMARY KEY (`id`),
       	KEY (`entry_id`, `kuser_id`)
)ENGINE=InnoDB COMMENT='Describes the relationship between a specific user and a specific entry';