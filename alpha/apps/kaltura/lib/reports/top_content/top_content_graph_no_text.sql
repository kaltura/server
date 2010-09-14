SELECT
	date_id,
	SUM(count_plays) count_plays,
#	AVG(distinct_plays) distinct_plays, /* Because we don't know the real number, we use avarage instead*/
	SUM(sum_time_viewed) sum_time_viewed,
	SUM(sum_time_viewed)/SUM(count_plays) avg_time_viewed,
	SUM(count_loads) count_loads
FROM 
	dwh_aggr_events_entry ev
WHERE 	{OBJ_ID_CLAUSE}
	AND partner_id =  {PARTNER_ID} # PARTNER_ID
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
			AND {TO_DATE_ID} #TO_TIME
	AND 
		( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 )
GROUP BY date_id
ORDER BY date_id
LIMIT 0,365  /* pagination  */
