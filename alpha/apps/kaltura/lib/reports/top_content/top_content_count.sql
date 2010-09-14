SELECT 
	COUNT(DISTINCT a.entry_id) count_all
FROM (
	SELECT ev.entry_id
		FROM	kalturadw.dwh_aggr_events_entry  ev , kalturadw.dwh_dim_entries en
	WHERE
	en.entry_id=ev.entry_id
	AND {OBJ_ID_CLAUSE}
	AND {SEARCH_TEXT_MATCH}
	AND {CATEGORIES_MATCH}
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
		AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
			AND {TO_DATE_ID} #TO_TIME
		AND 
	( count_time_viewed > 0 OR
	  count_plays > 0 OR
	  count_loads > 0 )
) AS a 