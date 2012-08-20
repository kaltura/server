SELECT added_entries, deleted_entries, total_entries, added_storage_mb, deleted_storage_mb, total_storage_mb, added_msecs, deleted_msecs, total_msecs
FROM 
	(SELECT
		IFNULL(SUM(added_storage_kb),0)/1024 added_storage_mb,
		IFNULL(SUM(deleted_storage_kb),0)/1024 deleted_storage_mb,
		IFNULL(SUM(added_entries),0) added_entries,
		IFNULL(SUM(deleted_entries),0) deleted_entries,
		IFNULL(SUM(added_msecs),0) added_msecs,
		IFNULL(SUM(deleted_msecs),0) deleted_msecs
	FROM
			kalturadw.dwh_hourly_user_usage u
			WHERE
			{OBJ_ID_CLAUSE}
			AND partner_id = {PARTNER_ID}
			AND
	date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) added,
	(SELECT
		IFNULL(SUM(total_storage_kb/1024),0) total_storage_mb,
        IFNULL(SUM(total_entries),0) total_entries,
        IFNULL(SUM(total_msecs),0) total_msecs
	FROM
		kalturadw.dwh_hourly_user_usage u JOIN (SELECT kuser_id, MAX(date_id) date_id FROM kalturadw.dwh_hourly_user_usage u WHERE {OBJ_ID_CLAUSE} AND partner_id = {PARTNER_ID} and date_id <= {TO_DATE_ID} GROUP BY kuser_id) total
		ON u.kuser_id = total.kuser_id AND u.date_id = total.date_id WHERE {OBJ_ID_CLAUSE}) total
