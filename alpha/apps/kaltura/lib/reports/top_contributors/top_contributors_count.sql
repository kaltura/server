SELECT 	COUNT(DISTINCT en.kuser_id) count_all
FROM dwh_dim_entries en
WHERE entry_media_type_id IN (1,2,5,6)
	AND en.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND en.created_at BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
		AND '{TO_TIME}' /*TO_TIME*/
	