SELECT name
FROM dwh_dim_applications
WHERE partner_id = {PARTNER_ID}
ORDER BY name 