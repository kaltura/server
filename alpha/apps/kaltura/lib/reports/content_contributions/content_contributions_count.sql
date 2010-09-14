SELECT 	COUNT( DISTINCT en.entry_media_source_id) count_all
FROM dwh_dim_entries en
WHERE
{OBJ_ID_CLAUSE}
AND entry_media_type_id IN (1,2,5,6)
	AND partner_id = {PARTNER_ID}
	AND created_at BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
		AND '{TO_TIME}' /*TO_TIME*/
