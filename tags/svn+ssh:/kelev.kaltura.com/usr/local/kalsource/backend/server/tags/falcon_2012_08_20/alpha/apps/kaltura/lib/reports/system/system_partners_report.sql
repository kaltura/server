SELECT 
	calc.date_id "date",
	calc.partner_id "partner id",
	calc.partner_name "partner name",
	calc.partner_package "pkg",
	calc.partner_type_name "type",
#	calc.admin_name "admin name",
#	calc.admin_email "admin email",
	ROUND(calc.sum_storage_mb/1024,2) "storage GB",
	ROUND(calc.sum_bandwidth_kb/1048576,2) "bandwidth GB",
#	if (calc.sum_storage_mb is not null,ROUND(calc.sum_storage_mb/1024,2),0)+ 
#		if(calc.sum_bandwidth_kb is not null,ROUND(calc.sum_bandwidth_kb/1048576,2),0) "storage+bandwidth for period GB",
	calc.count_video "videos",
	calc.count_image "images",
	calc.count_audio "audios",
	calc.count_plays "plays",
	calc.count_loads "loads"
FROM
(
	SELECT 
		ap.date_id ,
		ap.partner_id ,
		dp.partner_name ,
		dp.partner_package ,
		pt.partner_type_name ,
		dp.admin_name ,
		dp.admin_email ,
		SUM(ap.count_storage) sum_storage_mb,
		SUM(ap.count_bandwidth) sum_bandwidth_kb,
		SUM(ap.count_video) count_video,
		SUM(ap.count_image) count_image,
		SUM(ap.count_audio) count_audio,
		SUM(ap.count_plays) count_plays,
		SUM(ap.count_loads) count_loads		
	FROM
		kalturadw.dwh_aggr_partner ap,
		kalturadw.dwh_dim_partners dp,
		kalturadw.dwh_dim_partner_type pt
	WHERE 
		ap.partner_id=dp.partner_id AND dp.partner_type_id=pt.partner_type_id
		AND ap.partner_id IN ({PARTNER_IDS}) 
		AND ap.date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}
	GROUP BY 
		{GROUP_BY}
) calc;
	