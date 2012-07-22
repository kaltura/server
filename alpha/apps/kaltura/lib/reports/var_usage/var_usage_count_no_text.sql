SELECT COUNT(*) count_all
FROM kalturadw.dwh_dim_partners p,
(SELECT
        {GROUP_COLUMN},
		partner_id
FROM  (
	SELECT
		date_id, FLOOR(date_id/100) month_id, partner_id
	FROM
		kalturadw.dwh_hourly_partner_usage
        WHERE
		{OBJ_ID_CLAUSE}
		AND
		date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
        ) raw_data 
GROUP BY {GROUP_COLUMN}, partner_id) p_usage
WHERE p_usage.partner_id = p.partner_id

