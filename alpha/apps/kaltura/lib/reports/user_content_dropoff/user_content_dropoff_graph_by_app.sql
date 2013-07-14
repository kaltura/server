SELECT
	DATE(DATE(date_id) + INTERVAL hour_id HOUR + INTERVAL {TIME_SHIFT} HOUR)*1 date_id, # time shifted date
	IFNULL(SUM(count_plays),0) count_plays,
	IFNULL(SUM(count_plays_25),0) count_plays_25,
	IFNULL(SUM(count_plays_50),0) count_plays_50,
	IFNULL(SUM(count_plays_75),0) count_plays_75,
	IFNULL(SUM(count_plays_100),0) count_plays_100,
	IFNULL(( SUM(count_plays_100) / SUM(count_plays) ),0) play_through_ratio
FROM 
	dwh_hourly_events_context_app ev, kalturadw.dwh_dim_applications ap
WHERE 	
	{CAT_ID_CLAUSE}
	AND ap.name = {APPLICATION_NAME}
	AND ap.application_id = ev.application_id 
	AND ap.partner_id = ev.partner_id
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
	AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
    			AND     IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
			AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
			AND hour_id < IF (date_id = IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
	AND 
		( count_plays > 0 OR
		  count_plays_25 > 0 OR
		  count_plays_50 > 0 OR
		  count_plays_75 > 0 OR
		  count_plays_100 > 0 )
