/* Unique Known Users |  Unique Videos |  Plays | Minutes Viewed | Avg. View time | player impressions  | Impression to play ratio */
SELECT COUNT(DISTINCT user_id) unique_known_users,
COUNT(DISTINCT entry_id) unique_videos,
SUM(count_plays) count_plays,
SUM(sum_time_viewed) sum_time_viewed,
SUM(sum_time_viewed)/SUM(count_plays) avg_time_viewed,
(SUM(IFNULL(count_plays_25,0)) + SUM(IFNULL(count_plays_50,0)) + SUM(IFNULL(count_plays_75,0)) + SUM(IFNULL(count_plays_100,0)))/4/SUM(count_plays) avg_view_drop_off,
SUM(count_loads) count_loads,
(SUM(count_plays) / SUM(count_loads)) load_play_ratio

FROM 
	dwh_hourly_events_context_entry_user_app ev, dwh_dim_pusers us
WHERE 	{OBJ_ID_CLAUSE}
    AND {CAT_ID_CLAUSE}
	AND ev.partner_id =  {PARTNER_ID} # PARTNER_ID
	AND ev.partner_id = us.partner_id
	AND us.name IN {PUSER_ID}
	AND ev.user_id = us.puser_id
	AND date_id BETWEEN IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID})  
    			AND     IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID})
			AND hour_id >= IF (date_id = IF({TIME_SHIFT}>0,(DATE({FROM_DATE_ID}) - INTERVAL 1 DAY)*1, {FROM_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 0)
			AND hour_id < IF (date_id = IF({TIME_SHIFT}<=0,(DATE({TO_DATE_ID}) + INTERVAL 1 DAY)*1, {TO_DATE_ID}), IF({TIME_SHIFT}>0, 24 - {TIME_SHIFT}, ABS({TIME_SHIFT})), 24)
	AND 
		( count_time_viewed > 0 OR
		  count_plays > 0 OR
		  count_loads > 0 OR 
		  sum_time_viewed > 0 )