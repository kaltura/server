ALTER TABLE short_link
    MODIFY COLUMN id VARCHAR(20) NOT NULL,
    MODIFY int_id BIGINT(20) NOT NULL AUTO_INCREMENT,
    ADD COLUMN unique_id VARCHAR(63),
    ADD COLUMN custom_data TEXT,
    ADD INDEX expires_at(`expires_at`),
    ADD INDEX partner_id_status(`partner_id`, `status`),
    ADD INDEX partner_unique_id(`partner_id`,`unique_id`),
    ADD INDEX partner_kuser_status(`partner_id`,`kuser_id`,`status`);
