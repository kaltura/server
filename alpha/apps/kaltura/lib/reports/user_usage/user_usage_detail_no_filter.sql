SELECT
	raw_data.kuser_id,
	users.puser_id name,
	raw_data.added_entries added_entries,
	raw_data.deleted_entries deleted_entries,
	total.total_entries total_entries,
	raw_data.added_storage_mb added_storage_mb,
	raw_data.deleted_storage_mb deleted_storage_mb,
	total.total_storage_mb total_storage_mb,
	raw_data.added_msecs added_msecs,
	raw_data.deleted_msecs deleted_msecs,
	total.total_msecs total_msecs
FROM	
	(SELECT
		kuser_id, date_id,
		IFNULL(SUM(added_storage_kb),0)/1024 added_storage_mb,
		IFNULL(SUM(deleted_storage_kb),0)/1024 deleted_storage_mb,
		IFNULL(SUM(added_entries),0) added_entries,
		IFNULL(SUM(deleted_entries),0) deleted_entries,
		IFNULL(SUM(added_msecs),0) added_msecs,
		IFNULL(SUM(deleted_msecs),0) deleted_msecs
	FROM
		kalturadw.dwh_hourly_user_usage
        WHERE
		partner_id = {PARTNER_ID}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
	GROUP BY kuser_id	
    ) raw_data,
	(SELECT
		u.kuser_id,
		total_storage_kb/1024 total_storage_mb,
		total_entries,
		total_msecs
	FROM
		kalturadw.dwh_hourly_user_usage u JOIN (SELECT kuser_id, MAX(date_id) date_id FROM kalturadw.dwh_hourly_user_usage WHERE partner_id = {PARTNER_ID} AND date_id <= {TO_DATE_ID} GROUP BY kuser_id) MAX
	    ON u.kuser_id = max.kuser_id AND u.date_id = max.date_id) total,
	dwh_dim_kusers users
WHERE raw_data.kuser_id = total.kuser_id
AND raw_data.kuser_id = users.kuser_id
ORDER BY {SORT_FIELD}
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}		

