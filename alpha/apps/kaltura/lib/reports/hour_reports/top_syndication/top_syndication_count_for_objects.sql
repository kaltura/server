SELECT 
	COUNT( DISTINCT ifnull(ev.referrer,"-")) count_all
FROM kalturadw.dwh_fact_events  ev 
WHERE
{OBJ_ID_CLAUSE}
AND  ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
AND event_time BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
		AND '{TO_TIME}' /*TO_TIME*/
	AND event_type_id IN (2,3,4,5,6,7) /*event types %*/
	AND entry_media_type_id IN (1,5,6)  /* allow only video & audio & mix */