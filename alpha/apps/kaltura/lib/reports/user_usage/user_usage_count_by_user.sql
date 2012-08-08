SELECT COUNT(DISTINCT kuser_id) count_all
FROM
	kalturadw.dwh_hourly_user_usage u
WHERE
	{OBJ_ID_CLAUSE} 
	AND partner_id = {PARTNER_ID}
	AND date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}

