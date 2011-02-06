SELECT COUNT(DISTINCT ev.entry_id) count_all
	FROM	kalturadw.dwh_hourly_events_entry  ev , dwh_dim_entries en
WHERE
	ev.entry_id = en.entry_id
	AND {OBJ_ID_CLAUSE}
	AND {SEARCH_TEXT_MATCH}
	AND {CATEGORIES_MATCH}
	AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
    AND date_id BETWEEN IF(7>0,(DATE(20110104) - INTERVAL 1 DAY)*1, 20110104)  
    			AND     IF(7<0,(DATE(20110108) + INTERVAL 1 DAY)*1, 20110108)
			AND hour_id >= IF (date_id = IF(7>0,(DATE(20110104) - INTERVAL 1 DAY)*1, 20110104), IF(7>0, 24 - 7, ABS(7)), 0)
			AND hour_id < IF (date_id = IF(7<0,(DATE(20110108) + INTERVAL 1 DAY)*1, 20110108), IF(7>0, 24 - 7, ABS(7)), 24)
	AND 
		( count_plays > 0 OR
		  count_plays_25 > 0 OR
		  count_plays_50 > 0 OR
		  count_plays_75 > 0 OR
		  count_plays_100 > 0 )

 