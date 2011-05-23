SELECT
	{GROUP_COLUMN}, /*partner_id, */ SUM(ifnull(count_bandwidth, 0)) as bandwidth
FROM (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, count_bandwidth/1024 count_bandwidth
	FROM
		kalturadw.dwh_hourly_partner
        WHERE
		partner_id = {PARTNER_ID}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data
GROUP BY {GROUP_COLUMN}, partner_id;
