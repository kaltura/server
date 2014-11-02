SELECT SUM(peak_storage)
FROM
(SELECT
    partner_id,
    MAX(IFNULL(aggr_storage_mb,0)) peak_storage
FROM
    kalturadw.dwh_hourly_partner_usage
WHERE
    {OBJ_ID_CLAUSE}
AND
    bandwidth_source_id = 1
AND
    date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
GROUP BY partner_id) partner_peak_storage

