SELECT 
	date_id,
	SUM(count_plays) count_plays,
	SUM(count_edit) count_edit,
	SUM(count_viral) count_viral,
	SUM(count_download) count_download,
	SUM(count_report) count_report
FROM 
	dwh_aggr_events_entry ev , dwh_dim_entries en
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
		  count_edit > 0 OR
		  count_viral > 0 OR
		  count_download > 0 OR
		  count_report > 0 )
GROUP BY date_id
ORDER BY date_id
LIMIT 0,365 /* pagination  */	