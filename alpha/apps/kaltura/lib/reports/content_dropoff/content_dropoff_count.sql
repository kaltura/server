SELECT COUNT(DISTINCT ev.entry_id) count_all
	FROM	kalturadw.dwh_aggr_events_entry  ev , dwh_dim_entries en
WHERE
	ev.entry_id = en.entry_id
	AND {OBJ_ID_CLAUSE}
	AND {SEARCH_TEXT_MATCH}
	AND {CATEGORIES_MATCH}
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
		AND {TO_DATE_ID} #TO_TIME
	AND 
		( count_plays > 0 OR
		  count_plays_25 > 0 OR
		  count_plays_50 > 0 OR
		  count_plays_75 > 0 OR
		  count_plays_100 > 0 )

 