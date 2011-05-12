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
	(all_time_aggr.count_video_all_time + all_time_aggr.count_image_all_time + all_time_aggr.count_audio_all_time) "count media all time",
	aggr_p.count_video "count video",
	aggr_p.count_image "count image",
	aggr_p.count_audio "count audio",
	all_time_aggr.count_video_all_time "count video all time",
	all_time_aggr.count_image_all_time "count image all time",
	all_time_aggr.count_audio_all_time "count audio all time",
	aggr_p.active_site "active site",
	100*(aggr_p.active_site/aggr_p.number_of_partners) "% active site",
	aggr_p.active_publisher "active publisher",
	FLOOR(aggr_p.count_bandwidth / 1024) "count bandwidth mb",
	FLOOR(all_time_aggr.count_bandwidth / 1024) "bandwidth grand total",
	billing_bandwidth_greater_10_gb "billing bandwidth greater 10 gb",
	aggr_p.count_storage "count storage mb"
FROM
(
	SELECT
		IF(dim_partner.partner_package >1,0,dim_partner.partner_type_id) partner_type_id,
		dim_partner_type.partner_type_name partner_type_name,
		IF(dim_partner.partner_package >1,100,1) partner_package,
		count(distinct aggr_single_partner.partner_id) number_of_partners,
		SUM(aggr_single_partner.sum_time_viewed) sum_time_viewed, 
		SUM(aggr_single_partner.count_time_viewed) count_time_viewed,
		SUM(aggr_single_partner.count_plays) count_plays,
		SUM(aggr_single_partner.count_loads) count_loads,
		SUM(aggr_single_partner.count_widgets) count_widgets,
		SUM(aggr_single_partner.count_video) count_video,
		SUM(aggr_single_partner.count_image) count_image,
		SUM(aggr_single_partner.count_audio) count_audio,
		COUNT(aggr_single_partner.active_site ) active_site,
		COUNT(aggr_single_partner.active_publisher) active_publisher,
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
			SUM(IF((date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) AND ( aggr_partner.count_loads>=10 OR aggr_partner.count_plays>=1), 1 , NULL ) ) active_site,
			SUM(IF((date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}) AND ( aggr_partner.count_media >=1 OR aggr_partner.count_widgets>=1), 1 , NULL ) ) active_publisher,
			SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_bandwidth , NULL ) ) count_bandwidth,
			SUM(IF(date_id BETWEEN {FROM_DATE_ID} AND {TO_DATE_ID}, aggr_partner.count_storage , NULL ) ) count_storage
		FROM 
			kalturadw.dwh_aggr_partner aggr_partner
		WHERE 
			aggr_partner.date_id BETWEEN {TIME_SLOT_180} AND {TO_DATE_ID} 
		GROUP BY 
			aggr_partner.partner_id 
	) aggr_single_partner
		JOIN kalturadw.dwh_dim_partners dim_partner ON aggr_single_partner.partner_id=dim_partner.partner_id
		JOIN kalturadw.dwh_dim_partner_type dim_partner_type ON dim_partner.partner_type_id=dim_partner_type.partner_type_id
	WHERE
		dim_partner.created_date_id <= {TO_DATE_ID}
	GROUP BY 
		IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
	ORDER BY 
		IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
) aggr_p ,
/* second part is for the grand total of the bandwidth */
(
        SELECT
            IF(dim_partner.partner_package >1,0,dim_partner.partner_type_id) partner_type_id,
            IF(dim_partner.partner_package >1,100,1) partner_package,
           	SUM(aggr_single_partner.count_bandwidth_all_time) count_bandwidth,
			SUM(aggr_single_partner.count_video_all_time)  count_video_all_time,
			SUM(aggr_single_partner.count_image_all_time)  count_image_all_time,
			SUM(aggr_single_partner.count_audio_all_time)  count_audio_all_time,
			SUM(billing_bandwidth_greater_10_gb) billing_bandwidth_greater_10_gb
        FROM
		(
			SELECT
				aggr_partner.partner_id,
				SUM(aggr_partner.count_bandwidth) count_bandwidth_all_time,
				SUM(aggr_partner.count_video ) count_video_all_time,
				SUM(aggr_partner.count_image ) count_image_all_time,
				SUM(aggr_partner.count_audio ) count_audio_all_time,
				/* FLOOR(aggr_partner.date_id/100)=FLOOR({TO_DATE_ID}/100) - same calendary month as the TO-DATA*/
				IF(SUM(IF(FLOOR(aggr_partner.date_id/100)=FLOOR({TO_DATE_ID}/100),aggr_partner.count_bandwidth,NULL))+SUM(aggr_partner.count_storage*1024)>10*1024*1024,1,NULL) billing_bandwidth_greater_10_gb /* compare all data in KB */
			FROM 
				kalturadw.dwh_aggr_partner aggr_partner
			WHERE 
				aggr_partner.date_id <={TO_DATE_ID}
			GROUP BY 
				aggr_partner.partner_id
        ) aggr_single_partner
				JOIN kalturadw.dwh_dim_partners dim_partner ON aggr_single_partner.partner_id=dim_partner.partner_id
                JOIN kalturadw.dwh_dim_partner_type dim_partner_type ON dim_partner.partner_type_id=dim_partner_type.partner_type_id
		WHERE
			dim_partner.created_date_id <= {TO_DATE_ID}                
        GROUP BY
                IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
        ORDER BY
                IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id                
) all_time_aggr
WHERE aggr_p.partner_type_id = all_time_aggr.partner_type_id AND aggr_p.partner_package = all_time_aggr.partner_package