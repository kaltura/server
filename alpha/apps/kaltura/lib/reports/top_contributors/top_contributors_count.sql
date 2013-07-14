SELECT 	COUNT(DISTINCT ev.kuser_id) count_all
FROM dwh_dim_entries ev
WHERE 
	{OBJ_ID_CLAUSE}
	AND entry_media_type_id IN (1,2,5,6)
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND created_at BETWEEN '{FROM_TIME}' - interval {TIME_SHIFT} hour /*FROM_TIME*/ 
		AND '{TO_TIME}' - interval {TIME_SHIFT} hour /*TO_TIME*/
	