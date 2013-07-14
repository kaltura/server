SELECT 
	widget_id, IFNULL(SUM(count_plays),0) count_plays
FROM 
	dwh_hourly_events_widget
WHERE 
	partner_id = {PARTNER_ID} 
	AND date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID} 
GROUP BY widget_id
ORDER BY SUM(count_plays) DESC;
