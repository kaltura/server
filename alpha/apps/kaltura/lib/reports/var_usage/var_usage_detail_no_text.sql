SELECT {GROUP_COLUMN}, 
        status,
	partner_name, 
	partner_id,
	created_at,
	IFNULL(bandwidth_consumption,0) bandwidth_consumption,
	IFNULL(average_storage,0) average_storage,
	IFNULL(peak_storage,0) peak_storage,
	IFNULL(added_storage,0) added_storage,
	IFNULL(deleted_storage,0) deleted_storage,
	IFNULL(combined_bandwidth_storage,0) combined_bandwidth_storage,
	IFNULL(count_transcoding,0) transcoding_usage
FROM (SELECT DISTINCT(IF('{GROUP_COLUMN}' = 'date_id',day_id,t.month_id)) AS {GROUP_COLUMN},
	partner_status_id status,
	partner_name, 
	partner_id,
	UNIX_TIMESTAMP(created_at) created_at
FROM kalturadw.dwh_dim_time t, kalturadw.dwh_dim_partners
WHERE {OBJ_ID_CLAUSE}
AND day_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) partner_date LEFT JOIN
(SELECT
        {GROUP_COLUMN} usage_date_id,
	partner_id usage_partner_id,
        SUM(count_bandwidth) AS bandwidth_consumption,
		SUM(count_transcoding) AS count_transcoding,
        IF('{GROUP_COLUMN}' = 'date_id', aggr_storage_mb,
        IF(FLOOR({FROM_DATE_ID}/100) = FLOOR({TO_DATE_ID}/100), (SUM(aggr_storage_mb)/(DATEDIFF({TO_DATE_ID},{FROM_DATE_ID})+1)), 
		IF(month_id = FLOOR({FROM_DATE_ID}/100),(SUM(aggr_storage_mb)/(DAY(LAST_DAY(DATE(month_id * 100 + 1))) - DAY({FROM_DATE_ID}) + 1)),
        IF(month_id = FLOOR({TO_DATE_ID}/100),(SUM(aggr_storage_mb)/DAY({TO_DATE_ID})),(SUM(aggr_storage_mb)/DAY(LAST_DAY(DATE(month_id * 100 + 1)))))))) AS average_storage,
        MAX(aggr_storage_mb) AS peak_storage,
        SUM(added_storage_mb) AS added_storage,
		SUM(deleted_storage_mb) AS deleted_storage,
        SUM(count_bandwidth) + 
        IF('{GROUP_COLUMN}' = 'date_id', aggr_storage_mb,
        IF(FLOOR({FROM_DATE_ID}/100) = FLOOR({TO_DATE_ID}/100), (SUM(aggr_storage_mb)/(DATEDIFF({TO_DATE_ID},{FROM_DATE_ID})+1)), 
		IF(month_id = FLOOR({FROM_DATE_ID}/100),(SUM(aggr_storage_mb)/(DAY(LAST_DAY(DATE(month_id * 100 + 1))) - DAY({FROM_DATE_ID}) + 1)),
        IF(month_id = FLOOR({TO_DATE_ID}/100),(SUM(aggr_storage_mb)/DAY({TO_DATE_ID})),(SUM(aggr_storage_mb)/DAY(LAST_DAY(DATE(month_id * 100 + 1)))))))) AS combined_bandwidth_storage
FROM  (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, IFNULL(count_bandwidth_kb, 0)/1024 count_bandwidth, IFNULL(count_transcoding_mb, 0) count_transcoding, IFNULL(aggr_storage_mb, 0) aggr_storage_mb, IFNULL(added_storage_mb, 0) added_storage_mb, IFNULL(deleted_storage_mb, 0) deleted_storage_mb
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data 
GROUP BY usage_date_id, usage_partner_id) p_usage
ON partner_date.partner_id = p_usage.usage_partner_id
AND partner_date.{GROUP_COLUMN} = p_usage.usage_date_id
ORDER BY {SORT_FIELD}
LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */ 
	   