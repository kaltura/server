
SELECT
	aggr_p.partner_id id,
	aggr_p.partner_type_id "partner type", 
	aggr_p.partner_name "partner name",
	aggr_p.admin_email "admin email",
	aggr_p.description "description",
	aggr_p.url1 "url",
	aggr_p.created_at "created at",
	aggr_p.content_categories,
	aggr_p.sum_time_viewed "sum time viewed", 
	aggr_p.count_time_viewed "count time viewed", 
	aggr_p.count_plays "count plays",
	aggr_p.count_loads "count loads",
	aggr_p.count_widgets "count widgets",
	(aggr_p.count_video + aggr_p.count_audio + aggr_p.count_image) "count media",
	aggr_p.count_video "count video",
	aggr_p.count_image "count image",
	aggr_p.count_audio "count audio",
	aggr_p.active_site_7 "active site 7",
	aggr_p.active_site_30 "active site 30",
	aggr_p.active_site_180 "active site 180",
	aggr_p.active_publisher_7 "active publisher 7",
	aggr_p.active_publisher_30 "active publisher 30",
	aggr_p.active_publisher_180 "active publisher 180",
	FLOOR(aggr_p.count_bandwidth / 1024) "count bandwidth mb",
	FLOOR(all_time_aggr.count_bandwidth_all_time / 1024) "bandwidth grand total",
	aggr_p.count_storage "count storage mb"
FROM
(
	SELECT
    	dim_partner.partner_name,
    	dim_partner.admin_email,
    	dim_partner.description,
    	dim_partner.url1,
    	dim_partner.created_at,
    	dim_partner.content_categories,
    	dim_partner.partner_id partner_id,	
		dim_partner.partner_package,
		dim_partner.partner_type_id,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.sum_time_viewed , NULL ) ) sum_time_viewed, 
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_time_viewed , NULL ) ) count_time_viewed,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_plays , NULL ) ) count_plays,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_loads , NULL ) ) count_loads,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_widgets , NULL ) ) count_widgets,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_video , NULL ) ) count_video,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_audio , NULL ) ) count_audio,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_image , NULL ) ) count_image,
		SUM(IF((date_id BETWEEN {TIME_SLOT_7} AND {TO_DATE_ID}) AND ( aggr_partner.count_loads>=10 OR aggr_partner.count_plays>=1), 1 , NULL ) ) active_site_7,
		SUM(IF((date_id BETWEEN {TIME_SLOT_30} AND {TO_DATE_ID}) AND ( aggr_partner.count_loads>=10 OR aggr_partner.count_plays>=1), 1 , NULL ) ) active_site_30,
		SUM(IF((date_id BETWEEN {TIME_SLOT_180} AND {TO_DATE_ID}) AND ( aggr_partner.count_loads>=10 OR aggr_partner.count_plays>=1), 1 , NULL ) ) active_site_180,
		SUM(IF((date_id BETWEEN {TIME_SLOT_7} AND {TO_DATE_ID}) AND ( aggr_partner.count_media >=1 OR aggr_partner.count_widgets>=1), 1 , NULL ) ) active_publisher_7,
		SUM(IF((date_id BETWEEN {TIME_SLOT_30} AND {TO_DATE_ID})AND ( aggr_partner.count_media >=1 OR aggr_partner.count_widgets>=1), 1 , NULL ) ) active_publisher_30,
		SUM(IF((date_id BETWEEN {TIME_SLOT_180} AND {TO_DATE_ID}) AND ( aggr_partner.count_media >=1 OR aggr_partner.count_widgets>=1), 1 , NULL ) ) active_publisher_180,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_bandwidth , NULL ) ) count_bandwidth,
		SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_storage , NULL ) ) count_storage
	FROM 
		kalturadw.dwh_aggr_partner aggr_partner, kalturadw.dwh_dim_partners dim_partner
	WHERE 
		aggr_partner.partner_id = dim_partner.partner_id AND {PARTNER_PACKAGE_CRITERIA}
			AND	aggr_partner.date_id BETWEEN {TIME_SLOT_180} AND {TO_DATE_ID} 
	GROUP BY 
		aggr_partner.partner_id
	ORDER BY 
		aggr_partner.partner_id
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) aggr_p ,
(
	SELECT
		aggr_partner.partner_id, 
		dim_partner.partner_package,
		dim_partner.partner_type_id,
		SUM(aggr_partner.count_bandwidth ) count_bandwidth_all_time
	FROM 
		kalturadw.dwh_aggr_partner aggr_partner, kalturadw.dwh_dim_partners dim_partner
	WHERE 
		aggr_partner.partner_id = dim_partner.partner_id AND {PARTNER_PACKAGE_CRITERIA}
	GROUP BY 
		aggr_partner.partner_id
	LIMIT {PAGINATION_FIRST},{PAGINATION_SIZE}  /* pagination  */
) all_time_aggr
WHERE aggr_p.partner_id = all_time_aggr.partner_id
