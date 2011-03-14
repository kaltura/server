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
	all_time_aggr.count_storage_all_time "storage all time mb"
FROM
(
	SELECT	partner_status_id STATUS,
    	partner_name,
    	created_at,
    	partner_package,
    	dwh_dim_partners.partner_id partner_id,	
	SUM(count_loads) count_loads,
	SUM(count_plays) count_plays,
	SUM(count_video) count_video,
	SUM(count_audio) count_audio,
	SUM(count_mix) count_mix,
	SUM(count_image) count_image,
	SUM(count_bandwidth) count_bandwidth,
	SUM(count_storage) count_storage
	FROM kalturadw.dwh_hourly_partner RIGHT JOIN kalturadw.dwh_dim_partners
	ON (dwh_hourly_partner.partner_id = dwh_dim_partners.partner_id AND dwh_hourly_partner.date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID})
	WHERE  {OBJ_ID_CLAUSE} AND
	dwh_dim_partners.created_date_id <= {TO_DATE_ID}
	GROUP BY dwh_dim_partners.partner_id
	ORDER BY dwh_dim_partners.partner_id
	LIMIT LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) aggr_p,
(
	SELECT	partner_id, 
		SUM(count_video + count_audio + count_image) count_media_all_time,
		SUM(count_bandwidth ) count_bandwidth_all_time,
		SUM(count_storage ) count_storage_all_time
	FROM kalturadw.dwh_aggr_partner 
	WHERE {OBJ_ID_CLAUSE} AND
	date_id <= {TO_DATE_ID}
	GROUP BY partner_id
	ORDER BY partner_id
	LIMIT LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) all_time_aggr
WHERE aggr_p.partner_id = all_time_aggr.partner_id