
INSERT INTO partner (id, prefix, partner_name, admin_secret, description, status, created_at, updated_at)
VALUES (-5, '-5', 'Media', MD5(CONV(SUBSTRING(RAND(), 3), 10, 36)), 'Media Server', 1, NOW(), NOW());
