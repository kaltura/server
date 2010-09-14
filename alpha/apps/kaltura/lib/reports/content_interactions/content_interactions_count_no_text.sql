SELECT COUNT(DISTINCT ev.entry_id) count_all
	FROM	kalturadw.dwh_aggr_events_entry  ev 
WHERE
{OBJ_ID_CLAUSE}
AND ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND date_id BETWEEN {FROM_DATE_ID} #FROM_TIME
		AND {TO_DATE_ID} #TO_TIME
	AND 
( count_plays > 0 OR
  count_edit > 0 OR
  count_download > 0 OR
  count_viral > 0 OR
  count_report > 0 )
 