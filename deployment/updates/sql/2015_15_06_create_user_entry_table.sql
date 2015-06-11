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
        INDEX `user_entry_FI_1` (`entry_id`),
        CONSTRAINT `user_entry_FK_1`
        FOREIGN KEY (`entry_id`)
        REFERENCES `entry` (`id`),
        INDEX `user_entry_FI_2` (`kuser_id`),
        CONSTRAINT `user_entry_FK_2`
        FOREIGN KEY (`kuser_id`)
        REFERENCES `kuser` (`id`)
)Type=InnoDB COMMENT='Describes the relationship between a specific user and a specific entry';