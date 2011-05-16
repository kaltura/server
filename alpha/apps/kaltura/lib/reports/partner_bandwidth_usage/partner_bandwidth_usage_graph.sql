SELECT
	{GROUP_COLUMN}, /*partner_id, */ SUM(ifnull(count_bandwidth, 0)) as bandwidth
FROM (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id, count_bandwidth
	FROM
		kalturadw.dwh_hourly_partner
        WHERE
		partner_id = {PARTNER_ID}
		AND
		date_id BETWEEN  IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}) AND
				IF({TIME_SHIFT}<0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
        ) raw_data
GROUP BY {GROUP_COLUMN}, partner_id;
