SELECT 
	COUNT( DISTINCT ifnull(ev.domain_id,"-")) count_all
FROM kalturadw.dwh_aggr_events_domain  ev 
WHERE
{OBJ_ID_CLAUSE}
AND  ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
		AND {TO_DATE_ID} #TO_TIME
	AND 
( count_time_viewed > 0 OR
  count_plays > 0 OR
  count_loads > 0 )