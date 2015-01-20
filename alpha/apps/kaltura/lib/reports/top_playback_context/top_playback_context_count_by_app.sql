SELECT COUNT(DISTINCT context_id) count_all
FROM 
		dwh_hourly_events_context_app ev, dwh_dim_applications ap
WHERE 	
	{CAT_ID_CLAUSE}
	AND ap.name = {APPLICATION_NAME}
	AND ap.partner_id = ev.partner_id
	AND ap.application_id = ev.application_id
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
    AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
    		AND     IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
			AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
			AND hour_id < IF (date_id = IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
	AND ( count_time_viewed > 0 OR
	      count_plays > 0 OR
		  count_loads > 0 OR 
		  sum_time_viewed > 0)
	