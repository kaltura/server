ALTER TABLE user_entry 
ADD privacy_context VARCHAR(255) AFTER extended_status,
DROP KEY `entry_id`,
DROP KEY `kuser_id_updated_at`,
DROP KEY `kuser_id_extended_status_updated_at`,
ADD KEY (`entry_id`, `kuser_id`, `privacy_context`),
ADD KEY `kuser_id_updated_at` (`kuser_id`,`updated_at`),
ADD KEY `kuser_id_extended_status_updated_at` (`kuser_id`, `extended_status`, `updated_at`, `privacy_context`);
