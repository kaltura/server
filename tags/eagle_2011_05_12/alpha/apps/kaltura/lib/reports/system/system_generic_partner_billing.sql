SELECT 
	aggr_single_partner.partner_id "partner_id",
	dim_partner.partner_name,
	dim_partner.partner_package,
	dim_partner.partner_type_id,
	aggr_single_partner.count_bandwidth_all_time,
	aggr_single_partner.count_storage_all_time,
	aggr_single_partner.count_bandwith_for_month_aggr,
	aggr_single_partner.billing_bandwidth_greater_10_gb,
	aggr_single_partner.billing_bandwidth,
	aggr_partner_activity.count_bandwidth_www,
	aggr_partner_activity.count_bandwidth_limelite,
	aggr_partner_activity.count_bandwidth_l3,
	aggr_partner_activity.count_bandwidth_akamai,
	aggr_partner_activity.count_bandwith_for_month_pa

FROM	
(
	SELECT
		aggr_partner.partner_id,
		SUM(aggr_partner.count_bandwidth) count_bandwidth_all_time,
		SUM(aggr_partner.count_storage*1024) count_storage_all_time,
		SUM(IF(FLOOR(aggr_partner.date_id/100)=FLOOR({TO_DATE_ID}/100),aggr_partner.count_bandwidth,NULL)) count_bandwith_for_month_aggr,
		/* FLOOR(aggr_partner.date_id/100)=FLOOR({TO_DATE_ID}/100) - same calendary month as the TO-DATA*/
		IF(SUM(IF(FLOOR(aggr_partner.date_id/100)=FLOOR({TO_DATE_ID}/100),aggr_partner.count_bandwidth,NULL))+SUM(aggr_partner.count_storage*1024)>10*1024*1024,1,NULL) billing_bandwidth_greater_10_gb ,/* compare all data in KB */
		SUM(IF(FLOOR(aggr_partner.date_id/100)=FLOOR({TO_DATE_ID}/100),aggr_partner.count_bandwidth,NULL))+SUM(aggr_partner.count_storage*1024) billing_bandwidth /* compare all data in KB */
	FROM 
		kalturadw.dwh_aggr_partner aggr_partner
	WHERE 
		aggr_partner.date_id <={TO_DATE_ID}
	GROUP BY 
		aggr_partner.partner_id
) aggr_single_partner,
(
	SELECT
		p_act.partner_id,
		SUM(IF(p_act.partner_sub_activity_id=1,p_act.amount,NULL)) count_bandwidth_www,
		SUM(IF(p_act.partner_sub_activity_id=2,p_act.amount,NULL)) count_bandwidth_limelite,
		SUM(IF(p_act.partner_sub_activity_id=3,p_act.amount,NULL)) count_bandwidth_l3,
		SUM(IF(p_act.partner_sub_activity_id=4,p_act.amount,NULL)) count_bandwidth_akamai,
		SUM(p_act.amount) count_bandwith_for_month_pa
	FROM 
		kalturadw.dwh_fact_partner_activities p_act
	WHERE 
		FLOOR(p_act.activity_date_id/100)=FLOOR({TO_DATE_ID}/100)
		AND p_act.partner_activity_id IN (1) 
	GROUP BY 
		p_act.partner_id
) aggr_partner_activity,
	kalturadw.dwh_dim_partners dim_partner
WHERE
	aggr_partner_activity.partner_id=aggr_single_partner.partner_id
	AND aggr_single_partner.partner_id=dim_partner.partner_id
GROUP BY 
	aggr_single_partner.partner_id
ORDER BY 
	count_storage_all_time DESC 
LIMIT 100	