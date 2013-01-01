INSERT INTO permission (`partner_id`, `type`, `name`, `status`, `created_at`, `updated_at`)
SELECT DISTINCT partner_id, 2 AS `type`, 'FEATURE_ENTITLEMENT_USED' AS `name`, 1 AS `status`, NOW() AS `created_at`, NOW() AS `updated_at` FROM category_kuser WHERE STATUS = 1 AND partner_id > 100;
