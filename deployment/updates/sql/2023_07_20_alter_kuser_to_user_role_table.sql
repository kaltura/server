ALTER TABLE kuser_to_user_role
    ADD app_guid VARCHAR(25) DEFAULT NULL,
    ADD INDEX app_guid(`app_guid`),
    ADD INDEX kuser_id_app_guid(`kuser_id`,`app_guid`);
