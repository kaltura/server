SELECT 
	a.referrer referrer,
	SUM(count_plays) count_plays,
	SUM(time_viewed) sum_time_viewed,
	AVG(time_viewed) avg_time_viewed,
	SUM(count_loads) count_loads,
	( SUM(count_plays) / SUM(count_loads) ) load_play_ratio
FROM (
	SELECT 
		ev.referrer,
		session_id,
		IF(event_type_id = 3, uid,NULL) uid,
		MAX(IF(event_type_id IN(4,5,6,7),current_point,NULL))/60000  time_viewed,
		COUNT(IF(event_type_id = 3, 1,NULL)) count_plays,
		COUNT(IF(event_type_id = 2, 1,NULL)) count_loads
	FROM kalturadw.dwh_fact_events  ev 
	WHERE
	{OBJ_ID_CLAUSE}
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND event_time BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
			AND '{TO_TIME}' /*TO_TIME*/
		AND event_type_id IN (2,3,4,5,6,7) /*event types %*/
		AND entry_media_type_id IN (1,5,6)  /* allow only video & audio & mix */
	GROUP BY ev.referrer,session_id,uid
) AS a
GROUP BY a.referrer 
ORDER BY {SORT_FIELD}
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */

