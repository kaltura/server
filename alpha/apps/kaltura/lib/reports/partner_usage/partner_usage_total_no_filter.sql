SELECT total_bandwidth.bandwidth/1024 bandwidth_consumption,
	   total_storage.storage storage_used,
	   total_bandwidth.bandwidth/1024 + total_storage.storage combined_bandwidth_storage
FROM	   
(SELECT 
	SUM(IFNULL(count_bandwidth_kb,0)) as bandwidth
FROM 
	kalturadw.dwh_hourly_partner_usage
WHERE
	{OBJ_ID_CLAUSE}
	AND
	date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) AS total_bandwidth,
(SELECT 
	MAX(IFNULL(aggr_storage_mb,0)) storage
FROM 
	kalturadw.dwh_hourly_partner_usage
WHERE
	{OBJ_ID_CLAUSE}
	AND
	bandwidth_source_id = 1
	AND 
	IF ({FROM_DATE_ID} = {TO_DATE_ID}, date_id = {FROM_DATE_ID},
	FLOOR(date_id/100) BETWEEN FLOOR({FROM_DATE_ID}/100) AND FLOOR({TO_DATE_ID}/100))) AS  total_storage
        