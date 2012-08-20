SELECT 
	date_id "Date",
	widget_id,
	SUM(count_plays)  "Play count",
	SUM(count_loads) "Entry load count",
	SUM(count_widget_loads) "Widget load count"
FROM
	kalturadw.dwh_aggr_events_widget
WHERE 
	partner_id={PARTNER_ID} 
	AND date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
GROUP BY 
	partner_id,
	date_id,
	widget_id;
	
