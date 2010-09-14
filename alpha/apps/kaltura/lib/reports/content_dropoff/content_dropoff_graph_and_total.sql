SELECT 
	SUM(count_plays) count_plays,
	SUM(count_plays_25) count_plays_25,
	SUM(count_plays_50) count_plays_50,
	SUM(count_plays_75) count_plays_75,
	SUM(count_plays_100) count_plays_100,
	( SUM(count_plays_100) / SUM(count_plays) ) play_through_ratio
FROM 
	dwh_aggr_events_entry ev, dwh_dim_entries en
WHERE
	ev.entry_id = en.entry_id 	
	AND {OBJ_ID_CLAUSE}
	AND {SEARCH_TEXT_MATCH}
	AND {CATEGORIES_MATCH}
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
		AND {TO_DATE_ID} #TO_TIME
	AND 
		( count_plays > 0 OR
		  count_plays_25 > 0 OR
		  count_plays_50 > 0 OR
		  count_plays_75 > 0 OR
		  count_plays_100 > 0 )