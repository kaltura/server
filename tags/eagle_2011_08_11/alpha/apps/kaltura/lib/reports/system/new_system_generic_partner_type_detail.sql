SELECT 
	concat("" , aggr_p.partner_type_id , "(" , aggr_p.partner_package , ")" ) partner_type_and_package, 
	IF(aggr_p.partner_package > 1 , "PAYING" , aggr_p.partner_type_name ) partner_type_name,
	aggr_p.number_of_partners,	
	aggr_p.new_partners "new partners for period",
	aggr_p.sum_time_viewed "sum time viewed", 
	aggr_p.count_time_viewed "count time viewed", 
	aggr_p.count_plays "count plays",
	aggr_p.count_loads "count loads",
	aggr_p.count_widgets "count widgets",
	(aggr_p.count_video + aggr_p.count_audio + aggr_p.count_image) "count media",
	(all_time_aggr.count_audio_all_time + all_time_aggr.count_image_all_time + all_time_aggr.count_audio_all_time) "count media all time",
	aggr_p.count_video "count video",
	aggr_p.count_image "count image",
	aggr_p.count_audio "count audio",
	all_time_aggr.count_video_all_time "count video all time",
	all_time_aggr.count_image_all_time "count image all time",
	all_time_aggr.count_audio_all_time "count audio all time",
	aggr_p.active_site_7 "active site 7",
	aggr_p.active_site_30 "active site 30",
	aggr_p.active_site_180 "active site 180",
	100*(aggr_p.active_site_7/aggr_p.number_of_partners) "% active site 7",
	100*(aggr_p.active_site_30/aggr_p.number_of_partners) "% active site 30",
	100*(aggr_p.active_site_180/aggr_p.number_of_partners) "% active site 180",
	aggr_p.active_publisher_7 "active publisher 7",
	aggr_p.active_publisher_30 "active publisher 30",
	aggr_p.active_publisher_180 "active publisher 180",
	floor(aggr_p.count_bandwidth / 1024) "count bandwidth mb",
	floor(all_time_aggr.count_bandwidth / 1024) "bandwidth gt",
	aggr_p.count_storage "count storage"
FROM
(
	SELECT
		dim_partner.partner_type_id partner_type_id,
		dim_partner_type.partner_type_name partner_type_name,
		dim_partner.partner_package partner_package,
		count(distinct aggr_single_partner.partner_id) number_of_partners,
		SUM(aggr_single_partner.sum_time_viewed) sum_time_viewed, 
		SUM(aggr_single_partner.count_time_viewed) count_time_viewed,
		SUM(aggr_single_partner.count_plays) count_plays,
		SUM(aggr_single_partner.count_loads) count_loads,
		SUM(aggr_single_partner.count_widgets) count_widgets,
		SUM(aggr_single_partner.count_video) count_video,
		SUM(aggr_single_partner.count_image) count_image,
		SUM(aggr_single_partner.count_audio) count_audio,
		COUNT(aggr_single_partner.active_site_7 ) active_site_7,
		COUNT(aggr_single_partner.active_site_30) active_site_30,
		COUNT(aggr_single_partner.active_site_180) active_site_180,
		COUNT(aggr_single_partner.active_publisher_7) active_publisher_7,
		COUNT(aggr_single_partner.active_publisher_30) active_publisher_30,
		COUNT(aggr_single_partner.active_publisher_180) active_publisher_180,
		SUM(aggr_single_partner.count_bandwidth) count_bandwidth,
		SUM(aggr_single_partner.count_storage) count_storage,
		SUM(IF(dim_partner.created_date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, 1 , NULL ) ) new_partners
	FROM 
	(
		SELECT
			aggr_partner.partner_id,
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
			kalturadw.dwh_aggr_partner aggr_partner
		WHERE 
			aggr_partner.date_id BETWEEN {TIME_SLOT_180} AND {TO_DATE_ID} 
		GROUP BY 
			aggr_partner.partner_id
	) aggr_single_partner
		RIGHT OUTER JOIN kalturadw.dwh_dim_partners dim_partner ON aggr_single_partner.partner_id=dim_partner.partner_id
		LEFT OUTER JOIN kalturadw.dwh_dim_partner_type dim_partner_type ON dim_partner.partner_type_id=dim_partner_type.partner_type_id
	GROUP BY 
		IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
	ORDER BY 
		IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
) aggr_p ,
/* second part is for the grand total of the bandwidth */
(
        SELECT
                dim_partner.partner_type_id partner_type_id,
				dim_partner.partner_package partner_package,
               	SUM(aggr_partner.count_bandwidth) count_bandwidth,
				SUM(aggr_partner.count_video ) count_video_all_time,
				SUM(aggr_partner.count_image ) count_image_all_time,
				SUM(aggr_partner.count_audio ) count_audio_all_time
        FROM
                kalturadw.dwh_aggr_partner aggr_partner RIGHT OUTER JOIN kalturadw.dwh_dim_partners dim_partner ON aggr_partner.partner_id=dim_partner.partner_id
                LEFT OUTER JOIN kalturadw.dwh_dim_partner_type dim_partner_type ON dim_partner.partner_type_id=dim_partner_type.partner_type_id
        WHERE
                /*aggr_partner.partner_id<400  AND */aggr_partner.date_id <={TO_DATE_ID} 
        GROUP BY
                IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
        ORDER BY
                IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id                
) all_time_aggr
WHERE aggr_p.partner_type_id = all_time_aggr.partner_type_id AND aggr_p.partner_package = all_time_aggr.partner_package