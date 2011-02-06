SELECT 
	ev_stats.entry_id object_id,
	en.entry_name,
	count_plays,
#	distinct_plays,
	sum_time_viewed,
	avg_time_viewed,
	count_loads,
	load_play_ratio
FROM
(
	SELECT 
		entry_id,
		SUM(sum_time_viewed) sum_time_viewed,
		SUM(sum_time_viewed)/SUM(count_plays) avg_time_viewed,
		SUM(count_plays) count_plays,
#		AVG(distinct_plays) distinct_plays, /* Because we don't know the real number, we use avarage instead*/
		SUM(count_loads) count_loads,
		( SUM(count_plays) / SUM(count_loads) ) load_play_ratio
	FROM 
		dwh_hourly_events_entry ev
	WHERE 	{OBJ_ID_CLAUSE}
		AND partner_id =  {PARTNER_ID} # PARTNER_ID
        AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
    			AND     IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
			AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
			AND hour_id < IF (date_id = IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
		AND ( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 )
	GROUP BY entry_id
	
	ORDER BY {SORT_FIELD}
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) AS ev_stats, dwh_dim_entries en
WHERE ev_stats.entry_id = en.entry_id
