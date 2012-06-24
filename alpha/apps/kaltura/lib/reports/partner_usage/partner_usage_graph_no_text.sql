SELECT
		{GROUP_COLUMN}, /*partner_id, */
        SUM(count_bandwidth) AS bandwidth_consumption,
        IF('{GROUP_COLUMN}' = 'date_id', aggr_storage_mb, SUM(aggr_storage_mb)/DAY(LAST_DAY(DATE(month_id) * 100 + 1))) AS average_storage,
        MAX(aggr_storage_mb) AS peak_storage,
        SUM(added_storage_mb) AS added_storage,
        SUM(count_bandwidth) + IF('{GROUP_COLUMN}' = 'date_id', aggr_storage_mb, SUM(aggr_storage_mb)/DAY(LAST_DAY(DATE(month_id) * 100 + 1))) AS combined_bandwidth_storage

FROM (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, IFNULL(count_bandwidth_kb, 0)/1024 count_bandwidth, IFNULL(aggr_storage_mb, 0) aggr_storage_mb, IFNULL(count_storage_mb, 0) added_storage_mb
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data
GROUP BY {GROUP_COLUMN};
