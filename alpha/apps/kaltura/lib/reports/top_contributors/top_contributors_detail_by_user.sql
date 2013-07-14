SELECT 
	kuser_id object_id,
	IFNULL(screen_name, puser_id) name,
	COUNT(DISTINCT entry_id) count_total,
	COUNT(DISTINCT IF(entry_media_type_id = 1, entry_id,NULL)) count_video ,
	COUNT(DISTINCT IF(entry_media_type_id = 5, entry_id,NULL)) count_audio ,
	COUNT(DISTINCT IF(entry_media_type_id = 2, entry_id,NULL)) count_image ,
	COUNT(DISTINCT IF(entry_media_type_id = 6, entry_id,NULL)) count_mix 
FROM (
	SELECT 	
		ev.kuser_id,
		ku.screen_name,
		ku.puser_id,
		ev.entry_id,
		ev.entry_media_type_id
	FROM 
		dwh_dim_entries ev JOIN dwh_dim_kusers ku ON ev.kuser_id = ku.kuser_id  
	WHERE {OBJ_ID_CLAUSE} 
		AND entry_media_type_id IN (1,2,5,6)
		AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
		AND ku.partner_id = {PARTNER_ID} /* PARTNER_ID*/
		AND ev.created_at BETWEEN '{FROM_TIME}' - interval {TIME_SHIFT} hour /*FROM_TIME*/ 
			AND '{TO_TIME}' - interval {TIME_SHIFT} hour /*TO_TIME*/
		AND ku.puser_id IN {PUSER_ID}	
	 
	GROUP BY ev.kuser_id,ku.screen_name,ev.entry_id,ev.entry_media_type_id
) a
GROUP BY kuser_id,screen_name
ORDER BY {SORT_FIELD}
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */