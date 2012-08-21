ALTER TABLE kvote
ADD `status` INTEGER,
ADD KEY `entry_user_status_index`(`entry_id`, `kuser_id`, `status`)
;