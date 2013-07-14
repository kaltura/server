SELECT
	IFNULL(SUM(total_storage_kb/1024),0) total_storage_mb,
	IFNULL(SUM(total_entries),0) total_entries,
	IFNULL(SUM(total_msecs),0) total_msecs
FROM
	kalturadw.dwh_hourly_user_usage u JOIN (SELECT kuser_id, MAX(date_id) date_id FROM kalturadw.dwh_hourly_user_usage WHERE partner_id = {PARTNER_ID} and date_id < {FROM_DATE_ID} GROUP BY kuser_id) total
	ON u.kuser_id = total.kuser_id AND u.date_id = total.date_id   										
	WHERE {OBJ_ID_CLAUSE}
UNION SELECT
	0 total_storage_mb,
	0 total_entries,
	0 total_msecs

 	






