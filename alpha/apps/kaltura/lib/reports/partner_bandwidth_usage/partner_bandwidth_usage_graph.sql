SELECT
	{GROUP_COLUMN}, /*partner_id, */ SUM(count_bandwidth) as bandwidth
FROM (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, ifnull(count_bandwidth_kb, 0)/1024 count_bandwidth
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		partner_id = {PARTNER_ID}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data
GROUP BY {GROUP_COLUMN}, partner_id;
