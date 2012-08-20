SELECT 
	running_report.partner_id,
	running_report.partner_name,
	SUM(IF ( running_report.yearmonth = "200901" ,bandwith_for_month_gb,NULL)) "jan traffic",
	SUM(IF ( running_report.yearmonth = "200902" ,bandwith_for_month_gb,NULL)) "feb traffic",
	SUM(IF ( running_report.yearmonth = "200903" ,bandwith_for_month_gb,NULL)) "mar traffic",
	SUM(IF ( running_report.yearmonth = "200904" ,bandwith_for_month_gb,NULL)) "apr traffic",
	SUM(IF ( running_report.yearmonth = "200905" ,bandwith_for_month_gb,NULL)) "may traffic",
	SUM(IF ( running_report.yearmonth = "200906" ,bandwith_for_month_gb,NULL)) "jun traffic",
	SUM(IF ( running_report.yearmonth = "200907" ,bandwith_for_month_gb,NULL)) "jul traffic",
	SUM(IF ( running_report.yearmonth = "200908" ,bandwith_for_month_gb,NULL)) "aug traffic",
	SUM(IF ( running_report.yearmonth = "200909" ,bandwith_for_month_gb,NULL)) "sep traffic",
	SUM(IF ( running_report.yearmonth = "200910" ,bandwith_for_month_gb,NULL)) "oct traffic",
	SUM(IF ( running_report.yearmonth = "200911" ,bandwith_for_month_gb,NULL)) "nov traffic",
	SUM(IF ( running_report.yearmonth = "200912" ,bandwith_for_month_gb,NULL)) "dec traffic",
	SUM(IF ( running_report.yearmonth = "200901" ,storage_all_time_gb,NULL)) "jan storage",
	SUM(IF ( running_report.yearmonth = "200902" ,storage_all_time_gb,NULL)) "feb storage",
	SUM(IF ( running_report.yearmonth = "200903" ,storage_all_time_gb,NULL)) "mar storage",
	SUM(IF ( running_report.yearmonth = "200904" ,storage_all_time_gb,NULL)) "apr storage",
	SUM(IF ( running_report.yearmonth = "200905" ,storage_all_time_gb,NULL)) "may storage",
	SUM(IF ( running_report.yearmonth = "200906" ,storage_all_time_gb,NULL)) "jun storage",
	SUM(IF ( running_report.yearmonth = "200907" ,storage_all_time_gb,NULL)) "jul storage",
	SUM(IF ( running_report.yearmonth = "200908" ,storage_all_time_gb,NULL)) "aug storage",
	SUM(IF ( running_report.yearmonth = "200909" ,storage_all_time_gb,NULL)) "sep storage",
	SUM(IF ( running_report.yearmonth = "200910" ,storage_all_time_gb,NULL)) "oct storage",
	SUM(IF ( running_report.yearmonth = "200911" ,storage_all_time_gb,NULL)) "nov storage",
	SUM(IF ( running_report.yearmonth = "200912" ,storage_all_time_gb,NULL)) "dec storage",
	SUM(IF ( running_report.yearmonth = "200901" ,billing_bandwidth_gb,NULL)) "jan storage",
	SUM(IF ( running_report.yearmonth = "200902" ,billing_bandwidth_gb,NULL)) "feb billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200903" ,billing_bandwidth_gb,NULL)) "mar billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200904" ,billing_bandwidth_gb,NULL)) "apr billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200905" ,billing_bandwidth_gb,NULL)) "may billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200906" ,billing_bandwidth_gb,NULL)) "jun billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200907" ,billing_bandwidth_gb,NULL)) "jul billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200908" ,billing_bandwidth_gb,NULL)) "aug billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200909" ,billing_bandwidth_gb,NULL)) "sep billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200910" ,billing_bandwidth_gb,NULL)) "oct billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200911" ,billing_bandwidth_gb,NULL)) "nov billing bandwidth",
	SUM(IF ( running_report.yearmonth = "200912" ,billing_bandwidth_gb,NULL)) "dec billing bandwidth"
FROM
(
SELECT 
	aggr_single_partner_monthly.yearmonth, 
	aggr_single_partner_monthly.partner_id ,
	aggr_single_partner_monthly.partner_name,
	aggr_single_partner_monthly.partner_package "pkg",
	aggr_single_partner_monthly.count_bandwith_for_month_kb/1024/1024 bandwith_for_month_gb ,
	aggr_single_partner_to_date.count_storage_all_time_mb/1024 storage_all_time_gb,
	aggr_single_partner_monthly.count_bandwith_for_month_kb/1024/1024 + aggr_single_partner_to_date.count_storage_all_time_mb/1024 billing_bandwidth_gb
	
FROM
(
	SELECT
		FLOOR(aggr_partner.date_id /100) yearmonth,
		aggr_partner.partner_id,
		dim_partner.partner_name partner_name,
		dim_partner.partner_package partner_package,
		SUM(aggr_partner.count_bandwidth) count_bandwith_for_month_kb
	FROM 
		kalturadw.dwh_aggr_partner aggr_partner,kalturadw.dwh_dim_partners dim_partner
	WHERE 
		aggr_partner.date_id BETWEEN 20090101 AND 20091231
		AND aggr_partner.partner_id=dim_partner.partner_id
		AND dim_partner.partner_package>1
#		AND dim_partner.partner_id IN (300,2217)
	GROUP BY 
		aggr_partner.partner_id,FLOOR(aggr_partner.date_id /100)
) aggr_single_partner_monthly ,
(
	SELECT
		a.yearmonth,
		a.partner_id,
		SUM(b.count_storage_monthly_time_mb) count_storage_all_time_mb
	FROM 
	(
		SELECT
			FLOOR(aggr_partner.date_id /100) yearmonth,
			aggr_partner.partner_id
		FROM 
			kalturadw.dwh_aggr_partner aggr_partner,kalturadw.dwh_dim_partners dim_partner
		WHERE 
			aggr_partner.date_id <= 20091231
			AND aggr_partner.partner_id=dim_partner.partner_id
			AND dim_partner.partner_package>1
#			AND dim_partner.partner_id IN (300,2217)
		GROUP BY 
			aggr_partner.partner_id,FLOOR(aggr_partner.date_id /100)
	) a,
	(
		SELECT
			FLOOR(aggr_partner.date_id /100) yearmonth,
			aggr_partner.partner_id,
			SUM(aggr_partner.count_storage) count_storage_monthly_time_mb
		FROM 
			kalturadw.dwh_aggr_partner aggr_partner,kalturadw.dwh_dim_partners dim_partner
		WHERE 
			aggr_partner.date_id <= 20091231
			AND aggr_partner.partner_id=dim_partner.partner_id
			AND dim_partner.partner_package>1
#			AND dim_partner.partner_id IN (300,2217)
		GROUP BY 
			aggr_partner.partner_id,FLOOR(aggr_partner.date_id /100)
	) b
	WHERE a.partner_id=b.partner_id AND a.yearmonth>=b.yearmonth
	GROUP BY 
		a.partner_id,a.yearmonth
	
) aggr_single_partner_to_date ,kalturadw.dwh_dim_partner_type pt
WHERE
	aggr_single_partner_monthly.yearmonth = aggr_single_partner_to_date.yearmonth
	AND aggr_single_partner_monthly.partner_id = aggr_single_partner_to_date.partner_id
GROUP BY 
	aggr_single_partner_to_date.partner_id,aggr_single_partner_to_date.yearmonth
) running_report
GROUP BY 
	running_report.partner_id
	