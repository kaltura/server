SELECT
	IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id) {GROUP_COLUMN}, 
	IFNULL(SUM(count_bandwidth),0) as bandwidth_consumption,
	IFNULL(MAX(aggr_storage_mb),0) as used_storage,
	IFNULL(SUM(count_bandwidth),0) + IFNULL(MAX(aggr_storage_mb),0)	AS combined_bandwidth_storage
FROM  kalturadw.dwh_dim_time t LEFT JOIN (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, IFNULL(count_bandwidth_kb, 0)/1024 count_bandwidth, IFNULL(aggr_storage_mb, 0) aggr_storage_mb
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data ON date_id = day_id
WHERE day_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
GROUP BY IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id) 
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */ 


