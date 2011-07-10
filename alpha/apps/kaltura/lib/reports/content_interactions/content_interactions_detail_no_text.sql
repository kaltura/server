SELECT 
	ev_stats.entry_id object_id,
	en.entry_name,
	count_plays,
	count_edit,
	count_viral,
	count_download,
	count_report
FROM
(
	SELECT 
		entry_id,
		SUM(count_plays) count_plays,
		SUM(count_edit) count_edit,
		SUM(count_viral) count_viral,
		SUM(count_download) count_download,
		SUM(count_report) count_report
	FROM 
		dwh_hourly_events_entry ev
	WHERE 	{OBJ_ID_CLAUSE}
		AND partner_id =  {PARTNER_ID} # PARTNER_ID
        AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
    			AND     IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
			AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
			AND hour_id < IF (date_id = IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
		AND 
			( count_plays > 0 OR
			  count_edit > 0 OR
			  count_viral > 0 OR
			  count_download > 0 OR
			  count_report > 0 )
	GROUP BY entry_id
	ORDER BY {SORT_FIELD}
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) AS ev_stats, dwh_dim_entries en
WHERE ev_stats.entry_id = en.entry_id


