SELECT 	
	en.entry_media_source_id object_id,
	es.entry_media_source_name,
	COUNT(1) count_total,
	COUNT(IF(entry_media_type_id = 1, 1,NULL)) count_video ,
	COUNT(IF(entry_media_type_id = 5, 1,NULL)) count_audio ,
	COUNT(IF(entry_media_type_id = 2, 1,NULL)) count_image ,
	COUNT(IF(entry_media_type_id = 6, 1,NULL)) count_mix,
	COUNT(IF(is_admin_content = 0, 1,NULL)) count_ugc,
	COUNT(IF(is_admin_content = 1, 1,NULL)) count_admin
FROM dwh_dim_entries en,dwh_dim_entry_media_source es
WHERE
{OBJ_ID_CLAUSE}
AND entry_media_type_id IN (1,2,5,6)
	AND partner_id = {PARTNER_ID}
	AND created_at BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
		AND '{TO_TIME}' /*TO_TIME*/
	AND en.entry_media_source_id = es.entry_media_source_id
GROUP BY en.entry_media_source_id,entry_media_source_name
ORDER BY {SORT_FIELD}
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */ 