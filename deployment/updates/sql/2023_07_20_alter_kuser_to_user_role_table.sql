ALTER TABLE kuser_to_user_role ADD app_guid VARCHAR(255) DEFAULT NULL, ADD INDEX kuser_id_app_guid(`kuser_id`,`app_guid`);
