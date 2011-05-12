SELECT a.country_id,c.country,
	SUM(time_viewed) sum_time_viewed,
	SUM(count_plays) count_plays,
	SUM(count_plays_25) count_plays_25,
	SUM(count_plays_50) count_plays_50,
	SUM(count_plays_75) count_plays_75,
	SUM(count_plays_100) count_plays_100,
	SUM(count_loads) count_loads
FROM (
	SELECT country_id,session_id,
		MAX(IF(event_type_id IN(4,5,6,7),current_point,NULL))/60000  time_viewed,
		COUNT(IF(event_type_id = 3, 1,NULL)) count_plays,
		COUNT(IF(event_type_id = 4, 1,NULL)) count_plays_25,
		COUNT(IF(event_type_id = 5, 1,NULL)) count_plays_50,
		COUNT(IF(event_type_id = 6, 1,NULL)) count_plays_75,
		COUNT(IF(event_type_id = 7, 1,NULL)) count_plays_100,
		COUNT(IF(event_type_id = 2, 1,NULL)) count_loads
	FROM kalturadw.dwh_fact_events  ev 
	WHERE ev.partner_id = {PARTNER_ID} /* PARTNER_ID*/
	AND event_time BETWEEN '{FROM_TIME}' /*FROM_TIME*/ 
		AND '{TO_TIME}' /*TO_TIME*/
		AND event_type_id IN (2,3,4,5,6,7) /*event types %*/
	GROUP BY country_id,session_id
) AS a, dwh_dim_locations c
WHERE a.country_id = c.location_id
	AND c.location_type_name = 'COUNTRY'
GROUP BY a.country_id,c.country
ORDER BY count_plays DESC
LIMIT 0,365  /* pagination  */ 
	