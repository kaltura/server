SELECT total_bandwidth.bandwidth/1024 bandwidth_consumption,
               total_storage.avg_storage average_storage,
               total_storage.peak_storage peak_storage,
               total_storage.added_storage added_storage,
               total_storage.deleted_storage deleted_storage,
               total_bandwidth.bandwidth/1024 + total_storage.avg_storage combined_bandwidth_storage,
               total_bandwidth.transcoding transcoding_consumption,
               total_avg_storage.total_avg_storage_mb aggregated_monthly_avg_storage,
	       total_bandwidth.bandwidth/1024 + total_avg_storage.total_avg_storage_mb combined_bandwidth_aggregated_storage
FROM     
(SELECT
            SUM(IFNULL(count_bandwidth_kb,0)) as bandwidth,
            SUM(IFNULL(count_transcoding_mb,0)) as transcoding
FROM
            kalturadw.dwh_hourly_partner_usage
WHERE
            {OBJ_ID_CLAUSE}
            AND
            date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) AS total_bandwidth,
(SELECT
            MAX(IFNULL(aggr_storage_mb,0)) peak_storage,
            SUM(IFNULL(aggr_storage_mb,0))/(DATEDIFF({TO_DATE_ID},{FROM_DATE_ID}) + 1) avg_storage,
            SUM(IFNULL(added_storage_mb,0)) added_storage,
            SUM(IFNULL(deleted_storage_mb,0)) deleted_storage
FROM
            kalturadw.dwh_hourly_partner_usage
WHERE
            {OBJ_ID_CLAUSE}
            AND
            bandwidth_source_id = 1
            AND
            date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) AS  total_storage,
(SELECT
            SUM(monthly_avg_storage) total_avg_storage_mb
FROM
(SELECT
            FLOOR(date_id/100) month_id,
            AVG(aggr_storage_mb) monthly_avg_storage
FROM
            kalturadw.dwh_hourly_partner_usage
WHERE
            {OBJ_ID_CLAUSE}
            AND
            date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
            AND
            bandwidth_source_id = 1
            GROUP BY month_id) AS monthly_avg_storage)
AS total_avg_storage
               
