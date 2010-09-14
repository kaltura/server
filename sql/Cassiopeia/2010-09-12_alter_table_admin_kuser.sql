ALTER TABLE  `admin_kuser`
ADD  `login_blocked_until` DATETIME NULL AFTER `partner_id`,
ADD  `custom_data` TEXT NULL AFTER `login_blocked_until`;

