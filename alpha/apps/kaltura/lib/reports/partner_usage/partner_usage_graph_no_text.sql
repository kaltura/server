SELECT
	{GROUP_COLUMN}, /*partner_id, */ 
	SUM(count_bandwidth) as bandwidth_consumption,
	MAX(aggr_storage_mb) as storage_used,
	SUM(count_bandwidth) + MAX(aggr_storage_mb) AS combined_bandwidth_storage
FROM (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, ifnull(count_bandwidth_kb, 0)/1024 count_bandwidth, ifnull(aggr_storage_mb, 0) aggr_storage_mb
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data
GROUP BY {GROUP_COLUMN};
