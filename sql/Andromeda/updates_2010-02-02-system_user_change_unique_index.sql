# removed because a user can be marked as deleted (deleted_at = date), this is handled by code
ALTER TABLE `system_user` DROP KEY `system_user_email_unique`;
