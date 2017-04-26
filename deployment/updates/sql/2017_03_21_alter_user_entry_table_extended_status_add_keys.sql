ALTER TABLE user_entry 
ADD extended_status INT AFTER type,
ADD KEY `kuser_id_updated_at` (`kuser_id`,`updated_at`),
ADD	KEY `kuser_id_extended_status_updated_at` (`kuser_id`, `extended_status`, `updated_at`);