SELECT
	aggr_p.STATUS STATUS,
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
	aggr_p.count_storage "count storage mb",
	IFNULL(kalturadw.calc_partner_storage_data_time_range({FROM_DATE_ID}, {TO_DATE_ID}, aggr_p.partner_id), 0) "storage all time mb"
FROM
(
	SELECT 	STATUS, 	
	partner_name, 
	created_at, 
	partner_package, 
	media_usage.partner_id, 
	count_loads, 
	count_plays, 
	count_video, 
	count_audio, 
	count_mix, 
	count_image, 
	count_bandwidth, 
	count_storage FROM 
	(	SELECT	partner_status_id STATUS, partner_name, created_at, partner_package,
	    	dim_partner.partner_id partner_id,	IFNULL(SUM(count_loads), 0) count_loads,
			IFNULL(SUM(count_plays), 0) count_plays, IFNULL(SUM(count_video), 0) count_video,
			IFNULL(SUM(count_audio), 0) count_audio, IFNULL(SUM(count_mix), 0) count_mix,
			IFNULL(SUM(count_image), 0) count_image
		FROM kalturadw.dwh_dim_partners dim_partner 
		LEFT JOIN kalturadw.dwh_hourly_partner aggr_partner  
		ON (aggr_partner.partner_id = dim_partner.partner_id AND aggr_partner.date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID})
		WHERE   {OBJ_ID_CLAUSE} AND dim_partner.created_date_id <= {TO_DATE_ID}
		GROUP BY dim_partner.partner_id
		ORDER BY dim_partner.partner_id
		LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */) media_usage,
	(	SELECT dim_partner.partner_id partner_id, 	IFNULL(SUM(count_bandwidth_kb), 0) count_bandwidth,
			IFNULL(SUM(count_storage_mb), 0) count_storage
		FROM kalturadw.dwh_dim_partners dim_partner 
		LEFT JOIN kalturadw.dwh_hourly_partner_usage hourly_partner_usage 
		ON (hourly_partner_usage.partner_id = dim_partner.partner_id AND hourly_partner_usage.date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID})
		WHERE   {OBJ_ID_CLAUSE} AND	dim_partner.created_date_id <= {TO_DATE_ID}
		GROUP BY dim_partner.partner_id
		ORDER BY dim_partner.partner_id
		LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */) resources_usage
	WHERE media_usage.partner_id = resources_usage.partner_id	
) aggr_p,
(
	SELECT	dim_partner.partner_id, 
		SUM(IFNULL(count_video, 0) + IFNULL(count_audio, 0) + IFNULL(count_image, 0)) count_media_all_time
	FROM kalturadw.dwh_hourly_partner aggr_partner RIGHT JOIN kalturadw.dwh_dim_partners dim_partner ON (aggr_partner.partner_id = dim_partner.partner_id AND aggr_partner.date_id <= 20110101)
	WHERE {OBJ_ID_CLAUSE} AND
	dim_partner.created_date_id <= {TO_DATE_ID}
	GROUP BY dim_partner.partner_id
	ORDER BY dim_partner.partner_id
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) all_time_aggr
WHERE aggr_p.partner_id = all_time_aggr.partner_id;
