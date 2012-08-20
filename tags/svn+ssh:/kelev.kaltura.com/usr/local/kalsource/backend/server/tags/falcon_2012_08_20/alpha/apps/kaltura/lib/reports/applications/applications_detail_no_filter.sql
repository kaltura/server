SELECT name
FROM dwh_dim_applications
WHERE partner_id = {PARTNER_ID}
AND name IS NOT NULL
ORDER BY name 