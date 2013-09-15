SELECT
        IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id) AS {GROUP_COLUMN}, 
        SUM(count_bandwidth) AS bandwidth_consumption,
        IF('{GROUP_COLUMN}' = 'date_id', aggr_storage_mb,
        IF(FLOOR({FROM_DATE_ID}/100) = FLOOR({TO_DATE_ID}/100), (SUM(aggr_storage_mb)/(DATEDIFF({TO_DATE_ID},{FROM_DATE_ID})+1)), 
		IF(t.month_id = FLOOR({FROM_DATE_ID}/100),(SUM(aggr_storage_mb)/(DAY(LAST_DAY(DATE(t.month_id * 100 + 1))) - DAY({FROM_DATE_ID}) + 1)),
        IF(t.month_id = FLOOR({TO_DATE_ID}/100),(SUM(aggr_storage_mb)/DAY({TO_DATE_ID})),(SUM(aggr_storage_mb)/DAY(LAST_DAY(DATE(t.month_id * 100 + 1)))))))) AS average_storage,
        MAX(aggr_storage_mb) AS peak_storage,
        SUM(added_storage_mb) AS added_storage,
		SUM(deleted_storage_mb) AS deleted_storage,
        SUM(count_bandwidth) + 
        IF('{GROUP_COLUMN}' = 'date_id', aggr_storage_mb,
        IF(FLOOR({FROM_DATE_ID}/100) = FLOOR({TO_DATE_ID}/100), (SUM(aggr_storage_mb)/(DATEDIFF({TO_DATE_ID},{FROM_DATE_ID})+1)), 
		IF(t.month_id = FLOOR({FROM_DATE_ID}/100),(SUM(aggr_storage_mb)/(DAY(LAST_DAY(DATE(t.month_id * 100 + 1))) - DAY({FROM_DATE_ID}) + 1)),
        IF(t.month_id = FLOOR({TO_DATE_ID}/100),(SUM(aggr_storage_mb)/DAY({TO_DATE_ID})),(SUM(aggr_storage_mb)/DAY(LAST_DAY(DATE(t.month_id * 100 + 1)))))))) AS combined_bandwidth_storage,
		SUM(count_transcoding_mb) AS transcoding_consumption
FROM  kalturadw.dwh_dim_time t LEFT JOIN (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, IFNULL(count_bandwidth_kb, 0)/1024 count_bandwidth, IFNULL(aggr_storage_mb, 0) aggr_storage_mb, IFNULL(added_storage_mb, 0) added_storage_mb, IFNULL(deleted_storage_mb, 0) deleted_storage_mb, IFNULL(count_transcoding_mb, 0) count_transcoding_mb
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data ON date_id = day_id
WHERE day_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
GROUP BY IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id) 



