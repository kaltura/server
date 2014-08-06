INSERT INTO partner (id, prefix, partner_name, secret, admin_secret, description, status, created_at, updated_at)
VALUES (-6, '-6', 'Play', MD5(CONV(SUBSTRING(RAND(), 3), 10, 36)), MD5(CONV(SUBSTRING(RAND(), 3), 10, 36)), 'Play Server', 1, NOW(), NOW());
