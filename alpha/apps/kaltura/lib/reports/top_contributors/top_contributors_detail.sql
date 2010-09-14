SELECT 
	kuser_id object_id,
	screen_name,
	COUNT(DISTINCT entry_id) count_total,
	COUNT(DISTINCT IF(entry_media_type_id = 1, entry_id,NULL)) count_video ,
	COUNT(DISTINCT IF(entry_media_type_id = 5, entry_id,NULL)) count_audio ,
	COUNT(DISTINCT IF(entry_media_type_id = 2, entry_id,NULL)) count_image ,
	COUNT(DISTINCT IF(entry_media_type_id = 6, entry_id,NULL)) count_mix 
FROM (
	SELECT 	
		en.kuser_id,
		ku.screen_name,
		en.entry_id,
		en.entry_media_type_id
	FROM 
		dwh_dim_entries en JOIN dwh_dim_kusers ku ON en.kuser_id = ku.kuser_id  
	WHERE entry_media_type_id IN (1,2,5,6)
		AND en.partner_id = {PARTNER_ID} /* PARTNER_ID*/
		AND en.created_at BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
			AND '{TO_TIME}' /*TO_TIME*/
	 
	GROUP BY en.kuser_id,ku.screen_name,en.entry_id,en.entry_media_type_id
) a
GROUP BY kuser_id,screen_name
ORDER BY {SORT_FIELD}
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */