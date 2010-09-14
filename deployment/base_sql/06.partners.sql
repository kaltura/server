


INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-1, 'batch partner', '', NULL, '@BATCH_PARTNER_SECRET@', '@BATCH_PARTNER_ADMIN_SECRET@', -1, 0, 0, NULL, NOW(), NOW(), '@BATCH_PARTNER_PARTNER_ALIAS@', NULL, 86400, 1, '-10', 'batch admin', '@BATCH_ADMIN_MAIL@', 'Build-in partner - used for batch operations', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_batch.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);

INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-2,  'admin console', '', NULL, '@ADMIN_CONSOLE_PARTNER_SECRET@', '@ADMIN_CONSOLE_PARTNER_ADMIN_SECRET@', -1, 0, 0, NULL, NOW(), NOW(), '@ADMIN_CONSOLE_PARTNER_ALIAS@', NULL, 86400, 1, '-10', 'console admin', '@ADMIN_CONSOLE_ADMIN_MAIL@', 'Build-in partner - used for admin console', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_console.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);



INSERT INTO `admin_kuser` ( `id`, `screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
( 99998 , NULL, 'batch admin', '@BATCH_KUSER_MAIL@', '@BATCH_KUSER_SHA1@', '@BATCH_KUSER_SALT@', NULL, NULL, NOW(), NOW(), -1);

INSERT INTO `admin_kuser` (`id`,  `screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
( 99999 , NULL , 'console admin', '@ADMIN_CONSOLE_KUSER_MAIL@', '@ADMIN_CONSOLE_KUSER_SHA1@', '@ADMIN_CONSOLE_KUSER_SALT@', NULL, NULL, NOW(), NOW(), -2);



insert into `system_user` (`email`, `first_name`, `last_name`, `sha1_password`, `salt`, `created_by`, `status`, `is_primary`, `status_updated_at`, `created_at`, `updated_at`, `deleted_at`, `role`) 
values('@SYSTEM_USER_ADMIN_EMAIL@','admin','admin','@SYSTEM_USER_ADMIN_SHA1@','@SYSTEM_USER_ADMIN_SALT@','0','1','1',NULL,NOW(),NOW(),NULL,'admin');
