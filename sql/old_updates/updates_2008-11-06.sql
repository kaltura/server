# create partner_activity table
DROP TABLE IF EXISTS `partner_activity`;
CREATE TABLE `partner_activity`
(
        `id` INTEGER  NOT NULL AUTO_INCREMENT,
        `partner_id` INTEGER,
        `activity_date` DATE,
        `activity` INTEGER,
        `sub_activity` INTEGER,
        `amount` INTEGER,
        PRIMARY KEY (`id`),
        KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;

