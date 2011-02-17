SELECT
	aggr_p.status status,
	aggr_p.partner_id id,
	aggr_p.partner_name "partner name",
	aggr_p.created_at "created at",
	aggr_p.partner_package "partner package",
	aggr_p.count_loads "count loads",
	aggr_p.count_plays "count plays",
	(aggr_p.count_video + aggr_p.count_audio + aggr_p.count_image) "count media",
	all_time_aggr.count_media_all_time "count media all time",
	aggr_p.count_video "count video",
	aggr_p.count_image "count image",
	aggr_p.count_audio "count audio",
	aggr_p.count_mix "count mix",
	FLOOR(aggr_p.count_bandwidth / 1024) "count bandwidth mb",
#	FLOOR(all_time_aggr.count_bandwidth_all_time / 1024) "bandwidth all time",
	aggr_p.count_storage "count storage mb",
	all_time_aggr.count_storage_all_time "storage all time mb"
FROM
(
	SELECT
		dim_partner.partner_status_id status,
    	dim_partner.partner_name,
    	dim_partner.created_at,
    	dim_partner.partner_package,
    	dim_partner.partner_id partner_id,	
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_loads , NULL ) ) count_loads,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_plays , NULL ) ) count_plays,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_video , NULL ) ) count_video,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_audio , NULL ) ) count_audio,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_mix , NULL ) ) count_mix,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_image , NULL ) ) count_image,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_bandwidth , NULL ) ) count_bandwidth,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_storage , NULL ) ) count_storage
	FROM 
		kalturadw.dwh_hourly_partner aggr_partner RIGHT JOIN kalturadw.dwh_dim_partners dim_partner
		ON ( aggr_partner.partner_id = dim_partner.partner_id 
			AND	aggr_partner.date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID})
	WHERE 
		{OBJ_ID_CLAUSE}
		AND dim_partner.created_date_id BETWEEN 0 and  {TO_DATE_ID}
	GROUP BY 
		dim_partner.partner_id
	ORDER BY 
		dim_partner.partner_id
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) aggr_p ,
(
	SELECT
		aggr_partner.partner_id, 
		(SUM(aggr_partner.count_video ) + SUM(aggr_partner.count_audio ) + SUM(aggr_partner.count_mix )+ SUM(aggr_partner.count_image )) count_media_all_time,
		SUM(aggr_partner.count_bandwidth ) count_bandwidth_all_time,
		SUM(aggr_partner.count_storage ) count_storage_all_time
	FROM 
		kalturadw.dwh_hourly_partner aggr_partner RIGHT JOIN kalturadw.dwh_dim_partners dim_partner
		ON ( aggr_partner.partner_id = dim_partner.partner_id )
	WHERE 
		{OBJ_ID_CLAUSE}
		AND dim_partner.created_date_id BETWEEN 0 and  {TO_DATE_ID}
	GROUP BY 
		dim_partner.partner_id
	ORDER BY 
		dim_partner.partner_id
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) all_time_aggr
WHERE aggr_p.partner_id = all_time_aggr.partner_id
