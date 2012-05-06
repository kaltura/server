SELECT count(*) count_all
FROM dwh_dim_applications
WHERE partner_id = {PARTNER_ID}
