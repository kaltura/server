SELECT
	IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id) {GROUP_COLUMN}, 
	IFNULL(SUM(added_entries),0) added_entries,
	IFNULL(SUM(deleted_entries),0) deleted_entries,
	IFNULL(SUM(added_storage_kb),0)/1024 added_storage_mb,
	IFNULL(SUM(deleted_storage_kb),0)/1024 deleted_storage_mb,
	IFNULL(SUM(added_msecs),0) added_msecs,
	IFNULL(SUM(deleted_msecs),0) deleted_msecs
FROM kalturadw.dwh_dim_time t LEFT JOIN(
	SELECT
		date_id, FLOOR(date_id/100) month_id, added_storage_kb, deleted_storage_kb, added_entries, deleted_entries, added_msecs, deleted_msecs
	FROM
		kalturadw.dwh_hourly_user_usage u
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		partner_id = {PARTNER_ID}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data ON date_id = day_id
WHERE day_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
GROUP BY IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id) 

ORDER BY {SORT_FIELD}

LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}






