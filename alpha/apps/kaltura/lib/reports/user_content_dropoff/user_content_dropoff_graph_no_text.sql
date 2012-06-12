SELECT
	IFNULL(SUM(count_plays),0) count_plays,
	IFNULL(SUM(count_plays_25),0) count_plays_25,
	IFNULL(SUM(count_plays_50),0) count_plays_50,
	IFNULL(SUM(count_plays_75),0) count_plays_75,
	IFNULL(SUM(count_plays_100),0) count_plays_100,
	IFNULL(( SUM(count_plays_100) / SUM(count_plays) ),0) play_through_ratio
FROM 
	dwh_hourly_events_entry ev, dwh_dim_user_reports_allowed_partners p
WHERE 	{OBJ_ID_CLAUSE} # ev.entry_id in 
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
	AND ev.partner_id = p.partner_id
	AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
    			AND     IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
			AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
			AND hour_id < IF (date_id = IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
	AND date_id >= p.created_date_id and hour_id >= p.created_hour_id
	AND 
		( count_plays > 0 OR
		  count_plays_25 > 0 OR
		  count_plays_50 > 0 OR
		  count_plays_75 > 0 OR
		  count_plays_100 > 0 )
GROUP BY DATE(DATE(date_id) + INTERVAL hour_id HOUR + INTERVAL {TIME_SHIFT} HOUR)*1
ORDER BY DATE(DATE(date_id) + INTERVAL hour_id HOUR + INTERVAL {TIME_SHIFT} HOUR)*1
LIMIT 0,365  /* pagination  */
