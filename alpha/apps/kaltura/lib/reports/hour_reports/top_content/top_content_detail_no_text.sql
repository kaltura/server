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
		dwh_hourly_events_entry ev,
        (SELECT {TIME_SHIFT} time_shift, # time shift in hours
            {FROM_DATE_ID} start_date, # from date
            {TO_DATE_ID} end_date # to date
        ) p    
	WHERE 	{OBJ_ID_CLAUSE}
		AND partner_id =  {PARTNER_ID} # PARTNER_ID
        AND date_id BETWEEN calc_time_shift(p.start_date, 0, time_shift) AND calc_time_shift(p.end_date, 23, time_shift)
        AND calc_time_shift(date_id, hour_id, time_shift) between p.start_date AND p.end_date
		AND ( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 )
	GROUP BY entry_id
	
	ORDER BY {SORT_FIELD}
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) AS ev_stats, dwh_dim_entries en
WHERE ev_stats.entry_id = en.entry_id
