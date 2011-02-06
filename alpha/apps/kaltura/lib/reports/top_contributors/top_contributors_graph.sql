SELECT 
	DATE(created_at - interval {TIME_SHIFT} hour)*1 created_date_id,
	COUNT(1) count_total,
	COUNT(IF(entry_media_type_id = 1, 1,NULL)) count_video ,
	COUNT(IF(entry_media_type_id = 2, 1,NULL)) count_image ,
	COUNT(IF(entry_media_type_id = 5, 1,NULL)) count_audio ,
	COUNT(IF(entry_media_type_id = 6, 1,NULL)) count_mix
FROM dwh_dim_entries ev
WHERE
{OBJ_ID_CLAUSE}
AND entry_media_type_id IN (1,2,5,6)
AND partner_id = {PARTNER_ID}
		AND created_at BETWEEN '{FROM_TIME}' - interval {TIME_SHIFT} hour /*FROM_TIME*/ 
		AND '{TO_TIME}' - interval {TIME_SHIFT} hour /*TO_TIME*/
GROUP BY DATE(created_at - interval {TIME_SHIFT} hour) *1