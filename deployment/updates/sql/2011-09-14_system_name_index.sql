ALTER TABLE `access_control` ADD INDEX `system_name_partner_id` ( `system_name` , `partner_id` );
ALTER TABLE `user_role` ADD INDEX `system_name_partner_id` ( `partner_id` , `system_name` );
ALTER TABLE `flavor_params` ADD INDEX `system_name_partner_id` ( `partner_id` , `system_name` );
ALTER TABLE `conversion_profile_2` ADD INDEX `system_name_partner_id` ( `partner_id` , `system_name` );
ALTER TABLE `storage_profile` ADD INDEX `system_name_partner_id` ( `partner_id` , `system_name` );
