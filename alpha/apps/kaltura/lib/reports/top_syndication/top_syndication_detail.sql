SELECT 
	ev_stats.domain_id object_id,
	dom.domain_name domain_name, 
	count_plays,
	sum_time_viewed,
	avg_time_viewed,
	count_loads,
	load_play_ratio
FROM
(
	SELECT 
		domain_id,
		SUM(sum_time_viewed) sum_time_viewed,
		SUM(sum_time_viewed)/SUM(count_plays) avg_time_viewed,
		SUM(count_plays) count_plays,
		SUM(count_loads) count_loads,
		( SUM(count_plays) / SUM(count_loads) ) load_play_ratio
	FROM 
		dwh_hourly_events_domain ev
	WHERE 	{OBJ_ID_CLAUSE}
		AND partner_id =  {PARTNER_ID} # PARTNER_ID
		AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
   			AND     IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
		AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
		AND hour_id < IF (date_id = IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
	
        AND 
		( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 )
	GROUP BY domain_id
	
	ORDER BY {SORT_FIELD}
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) AS ev_stats, dwh_dim_domain dom
WHERE ev_stats.domain_id = dom.domain_id

