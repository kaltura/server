SELECT 
	DATE(created_at - interval {TIME_SHIFT} hour)*1 created_date_id,
	COUNT(1) count_total,
	IFNULL(COUNT(IF(is_admin_content = 0, 1,NULL)),0) count_ugc,
	IFNULL(COUNT(IF(is_admin_content = 1, 1,NULL)),0) count_admin,
	IFNULL(COUNT(IF(entry_media_type_id = 1, 1,NULL)),0) count_video ,
	IFNULL(COUNT(IF(entry_media_type_id = 5, 1,NULL)),0) count_audio ,
	IFNULL(COUNT(IF(entry_media_type_id = 2, 1,NULL)),0) count_image ,
	IFNULL(COUNT(IF(entry_media_type_id = 6, 1,NULL)),0) count_mix
FROM dwh_dim_entries ev
WHERE
{OBJ_ID_CLAUSE}
AND entry_media_type_id IN (1,2,5,6)
	AND partner_id = {PARTNER_ID}
	AND created_at BETWEEN '{FROM_TIME}' - interval {TIME_SHIFT} hour /*FROM_TIME*/ 
		AND '{TO_TIME}' - interval {TIME_SHIFT} hour /*TO_TIME*/
GROUP BY DATE(created_at - interval {TIME_SHIFT} hour) *1