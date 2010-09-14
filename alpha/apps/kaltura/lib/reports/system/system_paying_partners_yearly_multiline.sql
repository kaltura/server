SELECT 
	column_subject.report_subject,
	running_report.partner_id,
	running_report.partner_name,
	SUM(IF ( running_report.yearmonth = "200901" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) jan,
	SUM(IF ( running_report.yearmonth = "200902" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) feb,
	SUM(IF ( running_report.yearmonth = "200903" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) mar,
	SUM(IF ( running_report.yearmonth = "200904" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) apr,
	SUM(IF ( running_report.yearmonth = "200905" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) may,
	SUM(IF ( running_report.yearmonth = "200906" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) jun,
	SUM(IF ( running_report.yearmonth = "200907" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) jul,
	SUM(IF ( running_report.yearmonth = "200908" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) aug,
	SUM(IF ( running_report.yearmonth = "200909" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) sep,
	SUM(IF ( running_report.yearmonth = "200910" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) "oct",
	SUM(IF ( running_report.yearmonth = "200911" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) nov,
	SUM(IF ( running_report.yearmonth = "200912" ,IF(column_subject.report_subject_id=1,bandwith_for_month_gb,IF(column_subject.report_subject_id=2,running_report.storage_all_time_gb,billing_bandwidth_gb)),NULL)) "dec"
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
) running_report,
(
	SELECT report_subject,report_subject_id
	FROM dwh_report_subject
) column_subject
GROUP BY 
	running_report.partner_id,column_subject.report_subject_id
	