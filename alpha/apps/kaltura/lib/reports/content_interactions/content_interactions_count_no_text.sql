SELECT COUNT(DISTINCT ev.entry_id) count_all
	FROM	kalturadw.dwh_hourly_events_entry  ev    
WHERE
{OBJ_ID_CLAUSE}
AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
   			AND     IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
		AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
		AND hour_id < IF (date_id = IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
	AND 
( count_plays > 0 OR
  count_edit > 0 OR
  count_download > 0 OR
  count_viral > 0 OR
  count_report > 0 )
 