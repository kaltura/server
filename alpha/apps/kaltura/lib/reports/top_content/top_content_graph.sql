SELECT
	calc_time_shift(date_id, hour_id, time_shift) date_id, # time shifted date
	SUM(count_plays) count_plays,
#	AVG(distinct_plays) distinct_plays, /* Because we don't know the real number, we use avarage instead*/
	SUM(sum_time_viewed) sum_time_viewed,
	SUM(sum_time_viewed)/SUM(count_plays) avg_time_viewed,
	SUM(count_loads) count_loads
FROM 
	dwh_hourly_events_entry ev , kalturadw.dwh_dim_entries en,
    (SELECT {TIME_SHIFT} time_shift, # time shift in hours
		{FROM_DATE_ID} start_date, # from date
		{TO_DATE_ID} end_date # to date
	) p
WHERE 	
	en.entry_id=ev.entry_id
	AND {OBJ_ID_CLAUSE}
	AND {SEARCH_TEXT_MATCH}
	AND {CATEGORIES_MATCH}
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
	AND date_id BETWEEN calc_time_shift(start_date, 0, time_shift) AND calc_time_shift(end_date, 23, time_shift)
	AND calc_time_shift(date_id, hour_id, time_shift) between p.start_date AND p.end_date
	AND 
		( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 )
GROUP BY calc_time_shift(date_id, hour_id, time_shift)
ORDER BY calc_time_shift(date_id, hour_id, time_shift)
LIMIT 0,365  /* pagination  */
