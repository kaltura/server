ALTER TABLE kuser_to_user_role ADD app_guid VARCHAR(255) DEFAULT NULL, ADD INDEX kuser_id_app_guid(`kuser_id`,`app_guid`);

TODO consider
1. Chaing app_guid varchar(24) - mongo db objectId is 24 char long (hex) and 12 char long (binary)
2. Setting index for 'app_guid' as we will fetch from this table by 'app_guid'