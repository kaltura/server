SELECT
	SUM(count_plays) count_plays,
#	AVG(distinct_plays) distinct_plays, /* Because we don't know the real number, we use avarage instead*/
	SUM(sum_time_viewed) sum_time_viewed,
	SUM(sum_time_viewed)/SUM(count_plays) avg_time_viewed,
	SUM(count_loads) count_loads,
	( SUM(count_plays) / SUM(count_loads) ) load_play_ratio
FROM 
	dwh_aggr_events_entry ev, kalturadw.dwh_dim_entries en
WHERE 	
	en.entry_id=ev.entry_id
	AND {OBJ_ID_CLAUSE}
	AND {SEARCH_TEXT_MATCH}
	AND {CATEGORIES_MATCH}
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
			AND {TO_DATE_ID} #TO_TIME
	AND 
		( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 )