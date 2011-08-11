
SELECT 
	par_stats.month_id "month",
/*	par_stats.partner_type_id,*/
	IF(par_stats.partner_type_id=0,"Paying",IF(ISNULL(par_stats.partner_type_name),par_stats.partner_type_id,par_stats.partner_type_name)) "partner type",
	COUNT(DISTINCT par_stats.partner_id) "partners",
	SUM(par_stats.sum_plays) "plays" ,
	SUM(par_stats.sum_loads) "loads" ,
	SUM(par_stats.sum_count_videos) "videos", 
	SUM(par_stats.sum_count_audios) "audios",
	SUM(par_stats.sum_count_images) "images",
	SUM(sum_active_partner) "active publishers"
FROM 
(
	SELECT 
		FLOOR(aggr_pa.date_id/100) month_id,
		aggr_pa.partner_id  partner_id,
		IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id partner_type_id,
		dim_partner_type.partner_type_name partner_type_name,
		SUM(IF(ISNULL(aggr_pa.count_plays),0,aggr_pa.count_plays)) sum_plays ,
		SUM(IF(ISNULL(aggr_pa.count_loads),0,aggr_pa.count_loads)) sum_loads ,	
		SUM(IF(ISNULL(aggr_pa.count_video),0,aggr_pa.count_video)) sum_count_videos ,
		SUM(IF(ISNULL(aggr_pa.count_audio),0,aggr_pa.count_audio)) sum_count_audios ,
		SUM(IF(ISNULL(aggr_pa.count_image),0,aggr_pa.count_image)) sum_count_images ,
		SUM(IF(ISNULL(aggr_pa.count_widgets),0,aggr_pa.count_widgets)) sum_count_widgets,
		IF(SUM(aggr_pa.count_video)+SUM(aggr_pa.count_audio)+SUM(aggr_pa.count_image)+SUM(aggr_pa.count_widgets)>0,1,0) sum_active_partner
		
	FROM 
		kalturadw.dwh_aggr_partner aggr_pa
			RIGHT OUTER JOIN kalturadw.dwh_dim_partners dim_partner ON aggr_pa.partner_id=dim_partner.partner_id
			LEFT OUTER JOIN kalturadw.dwh_dim_partner_type dim_partner_type ON dim_partner.partner_type_id=dim_partner_type.partner_type_id
	WHERE 
		aggr_pa.date_id BETWEEN 20090101 AND 20090931
	GROUP BY 	
		FLOOR(aggr_pa.date_id/100),aggr_pa.partner_id	
	ORDER BY 
		IF(dim_partner.partner_package>1,0,100)*dim_partner.partner_type_id
) par_stats 
/*where
	par_stats.date_id BETWEEN 20090801 AND 20090901 */
GROUP BY 	
	par_stats.month_id ,par_stats.partner_type_id