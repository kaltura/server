SELECT
	aggr_p.STATUS STATUS,
	aggr_p.partner_id id,
	aggr_p.partner_name "partner name",
	UNIX_TIMESTAMP(aggr_p.created_at) "created at",
	aggr_p.partner_package "partner package",
	aggr_p.count_loads "count loads",
	aggr_p.count_plays "count plays",
	(aggr_p.new_videos + aggr_p.new_audios + aggr_p.new_images) "count media",
	all_time_aggr.count_media_all_time "count media all time",
	aggr_p.new_videos "count video",
	aggr_p.new_images "count image",
	aggr_p.new_audios "count audio",
	0 "count mix",
	FLOOR(aggr_p.count_bandwidth / 1024) "count bandwidth mb",
	aggr_p.added_storage "added storage mb",
	aggr_p.deleted_storage "deleted storage mb",
	aggr_p.peak_storage "peak storage mb",
	aggr_p.average_storage "average storage mb",
	FLOOR(aggr_p.count_bandwidth / 1024) + aggr_p.average_storage "combined bandwidth storage",
	aggr_p.count_transcoding  "transcoding mb"
FROM
(
	SELECT 	STATUS, 	
	partner_name, 
	created_at, 
	partner_package, 
	media_usage.partner_id, 
	count_loads, 
	count_plays, 
	new_videos, 
	new_audios, 
	new_images, 
	count_bandwidth, 
	count_transcoding,
	added_storage,
	deleted_storage,
	peak_storage,
	average_storage
	FROM
	(	SELECT	partner_status_id STATUS, partner_name, created_at, partner_package,
	    	dim_partner.partner_id partner_id,	IFNULL(SUM(count_loads), 0) count_loads,
			IFNULL(SUM(count_plays), 0) count_plays, IFNULL(SUM(new_videos), 0) new_videos,
			IFNULL(SUM(new_audios), 0) new_audios,
			IFNULL(SUM(new_images), 0) new_images
		FROM kalturadw.dwh_dim_partners dim_partner 
		LEFT JOIN kalturadw.dwh_hourly_partner aggr_partner  
		ON (aggr_partner.partner_id = dim_partner.partner_id AND aggr_partner.date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID})
		WHERE   {OBJ_ID_CLAUSE} AND dim_partner.created_date_id <= {TO_DATE_ID}
		GROUP BY dim_partner.partner_id
		ORDER BY dim_partner.partner_id
		LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */) media_usage,
	(	SELECT dim_partner.partner_id partner_id, 	IFNULL(SUM(count_bandwidth_kb), 0) count_bandwidth,
			IFNULL(SUM(count_transcoding_mb), 0) count_transcoding,
			IFNULL(SUM(added_storage_mb), 0) added_storage,
			IFNULL(SUM(deleted_storage_mb), 0) deleted_storage,
			IFNULL(MAX(aggr_storage_mb), 0) peak_storage,
			IFNULL(SUM(aggr_storage_mb), 0) / (DATEDIFF({TO_DATE_ID},{FROM_DATE_ID}) + 1) average_storage
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
		SUM(IFNULL(new_videos, 0) + IFNULL(new_audios, 0) + IFNULL(new_images, 0)) count_media_all_time
	FROM kalturadw.dwh_hourly_partner aggr_partner RIGHT JOIN kalturadw.dwh_dim_partners dim_partner ON (aggr_partner.partner_id = dim_partner.partner_id AND aggr_partner.date_id <= {TO_DATE_ID})
	WHERE {OBJ_ID_CLAUSE} AND
	dim_partner.created_date_id <= {TO_DATE_ID}
	GROUP BY dim_partner.partner_id
	ORDER BY dim_partner.partner_id
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) all_time_aggr
WHERE aggr_p.partner_id = all_time_aggr.partner_id;
