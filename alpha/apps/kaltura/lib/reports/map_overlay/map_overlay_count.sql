SELECT COUNT(DISTINCT ev.country_id) count_all
	FROM	kalturadw.dwh_aggr_events_country  ev 
WHERE
	{OBJ_ID_CLAUSE} /* ev.country_id in ( XXX ) */
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
		AND {TO_DATE_ID} #TO_TIME
	AND 
		( count_plays > 0 OR
		  count_plays_25 > 0 OR
		  count_plays_50 > 0 OR
		  count_plays_75 > 0 OR
		  count_plays_100 > 0 )
 	