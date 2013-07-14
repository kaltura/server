SELECT 	COUNT(DISTINCT ev.kuser_id) count_all
FROM dwh_dim_entries ev JOIN dwh_dim_kusers ku ON ev.kuser_id = ku.kuser_id  
WHERE 
	{OBJ_ID_CLAUSE}
	AND entry_media_type_id IN (1,2,5,6)
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND ku.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND created_at BETWEEN '{FROM_TIME}' - interval {TIME_SHIFT} hour /*FROM_TIME*/ 
		AND '{TO_TIME}' - interval {TIME_SHIFT} hour /*TO_TIME*/
	AND ku.puser_id IN {PUSER_ID}	
	