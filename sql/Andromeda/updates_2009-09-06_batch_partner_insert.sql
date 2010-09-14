
INSERT INTO `partner` (`id`, `partner_name`, `url1`, `url2`, `secret`, `admin_secret`, `max_number_of_hits_per_day`, `appear_in_search`, `debug_level`, `invalid_login_count`, `created_at`, `updated_at`, `partner_alias`, `ANONYMOUS_KUSER_ID`, `ks_max_expiry_in_seconds`, `create_user_on_demand`, `prefix`, `admin_name`, `admin_email`, `description`, `commercial_use`, `moderate_content`, `notify`, `custom_data`, `service_config_id`, `status`, `content_categories`, `type`, `phone`, `describe_yourself`, `adult_content`, `partner_package`, `usage_percent`, `storage_usage`, `eighty_percent_warning`, `usage_limit_warning`, `monitor_usage`) VALUES
(-1, 'batch partner', '', NULL, 'a92e32b463cd86182051c5821278fe0c', 'c2d5c06481e0a444ea8c3f7f0dab16bd', -1, 0, 0, NULL, NOW(), NOW(), '74cea349eb7add28efdebbb3bf5b3ddd', NULL, 86400, 1, '-10', 'batch admin', 'batch@kaltura.com', 'Build-in partner - used for batch operations', 0, 0, 0, 'a:1:{s:12:"isFirstLogin";b:1;}', 'services_batch.ct', 1, NULL, 0, NULL, NULL, 0, 1, 0, 0, NULL, NULL, 1);


INSERT INTO `admin_kuser` (`screen_name`, `full_name`, `email`, `sha1_password`, `salt`, `picture`, `icon`, `created_at`, `updated_at`, `partner_id`) VALUES
(NULL, 'batch admin', 'batch@kaltura.com', '117f0fc066b96a00ad8b49756be489c569318581', '7c394ce97c51b2109ffa6511f175cc6e', NULL, NULL, '2009-10-06 05:24:23', '2009-10-06 05:24:23', -1);
