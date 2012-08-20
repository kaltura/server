SELECT
	count(1) count_all
FROM
	kalturadw.dwh_dim_partners dim_partner
WHERE
		{OBJ_ID_CLAUSE}
		AND dim_partner.created_date_id BETWEEN 0 and  {TO_DATE_ID}