<?php

class kKavaEventPlatformReports extends kKavaReportsMgr
{
	protected static $reports_def = array(

		ReportType::EP_WEBCAST_HIGHLIGHTS => array(
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_VOD_AVG_PLAY_TIME),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_VOD_AVG_PLAY_TIME),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_VOD_AVG_PLAY_TIME)
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_AVG_PLAY_TIME, self::METRIC_VOD_LIVE_AVG_VIEW_TIME),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_COMBINED_LIVE_AVG_PLAY_TIME, self::METRIC_VOD_LIVE_AVG_VIEW_TIME),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_COMBINED_LIVE_AVG_PLAY_TIME, self::METRIC_VOD_LIVE_AVG_VIEW_TIME)
				),
			)
		),

		ReportType::EP_WEBCAST_UNIQUE_USERS => array(
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS)
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS, self::METRIC_UNIQUE_VOD_LIVE_VIEW_PERIOD_USERS),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS, self::METRIC_UNIQUE_VOD_LIVE_VIEW_PERIOD_USERS),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS, self::METRIC_UNIQUE_VOD_LIVE_VIEW_PERIOD_USERS)
				),
			)
		),

		ReportType::EP_WEBCAST_ENGAGEMENT => array(
			self::REPORT_UNION_DATA_SOURCES =>  array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO, self::METRIC_REACTION_CLICKED_UNIQUE_USERS, self::METRIC_DOWNLOAD_ATTACHMENT_UNIQUE_USERS, self::EVENT_TYPE_REACTION_CLICKED, self::EVENT_TYPE_DOWNLOAD_ATTACHMENT_CLICKED, self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO, self::METRIC_REACTION_CLICKED_UNIQUE_USERS, self::METRIC_DOWNLOAD_ATTACHMENT_UNIQUE_USERS, self::EVENT_TYPE_REACTION_CLICKED, self::EVENT_TYPE_DOWNLOAD_ATTACHMENT_CLICKED, self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO, self::METRIC_REACTION_CLICKED_UNIQUE_USERS, self::METRIC_DOWNLOAD_ATTACHMENT_UNIQUE_USERS, self::EVENT_TYPE_REACTION_CLICKED, self::EVENT_TYPE_DOWNLOAD_ATTACHMENT_CLICKED, self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS)
		),

		ReportType::EP_WEBCAST_ENGAGEMENT_TIMELINE => array(
			self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_DIMENSION_MAP => array(
				'position' => self::DIMENSION_POSITION,
			),
			self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_VIEW_PERIOD_COUNT, self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO,
				self::METRIC_REACTION_CLAP_COUNT, self::METRIC_REACTION_HEART_COUNT, self::METRIC_REACTION_THINK_COUNT, self::METRIC_REACTION_WOW_COUNT, self::METRIC_REACTION_SMILE_COUNT),
			self::REPORT_TABLE_FINALIZE_FUNC => "self::addZeroMinutes",
		),

		ReportType::EP_WEBCAST_TOP_RECORDING => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
			self::REPORT_DIMENSION_MAP => array(
				'entry_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name',
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_UNIQUE_VIEWERS, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
			self::REPORT_TOTAL_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_UNIQUE_VIEWERS, self::METRIC_UNIQUE_PERCENTILES_RATIO),
		),

		ReportType::EP_WEBCAST_TOP_PLATFORMS => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE
			),
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS),
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS),
				),
			)
		),

		ReportType::EP_WEBCAST_MAP_OVERLAY_COUNTRY => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => 'object_id',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toShortName',
				),
				array(
					self::REPORT_ENRICH_INPUT =>  array('country'),
					self::REPORT_ENRICH_OUTPUT => 'coordinates',
					self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getCoordinates',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toLongMappingName',
				),
			),
			self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS, self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS),
		),

		ReportType::EP_WEBCAST_MAP_OVERLAY_REGION => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_INPUT =>  array('country', 'region'),
					self::REPORT_ENRICH_OUTPUT => 'coordinates',
					self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getCoordinates',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toLongMappingName',
				),
			),
			self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS, self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS),
		),

		ReportType::EP_WEBCAST_MAP_OVERLAY_CITY => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'city' => self::DIMENSION_LOCATION_CITY,
				'coordinates' => self::DIMENSION_LOCATION_CITY,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_INPUT =>  array('country', 'region', 'city'),
					self::REPORT_ENRICH_OUTPUT => 'coordinates',
					self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getCoordinates',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toLongMappingName',
				),
			),
			self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS, self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS),
		),

		ReportType::EP_WEBCAST_LIVE_USER_ENGAGEMENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'user_name' => self::DIMENSION_KUSER_ID,
				'email' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'user_name', 'email'),
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getUserIdAndFullNameWithFallback',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('EMAIL'),
				)
			),
			self::REPORT_JOIN_REPORTS => array(
				// player events - live
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME, self::EVENT_TYPE_REACTION_CLICKED),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME, self::EVENT_TYPE_REACTION_CLICKED),
				),
				// kme
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_MEETING_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_MEETING_VIEW_TIME, self::EVENT_TYPE_MEETING_RAISE_HAND),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_MEETING_VIEW_TIME, self::EVENT_TYPE_MEETING_RAISE_HAND),
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO),
				)

			),
			self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_VIEW_TIME),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_COMBINED_LIVE_VIEW_TIME),
			self::REPORT_TABLE_FINALIZE_FUNC => 'kKavaReportsMgr::addCombinedLiveVodColumn',
			self::REPORT_TOTAL_FINALIZE_FUNC => 'kKavaReportsMgr::addTotalCombinedLiveVodColumn',
			self::REPORT_COLUMN_MAP => array(
				'live_view_time' => self::METRIC_COMBINED_LIVE_VIEW_TIME,
				'count_reaction_clicked' => self::EVENT_TYPE_REACTION_CLICKED,
				'count_raise_hand_clicked' => self::EVENT_TYPE_MEETING_RAISE_HAND,
				'combined_live_engaged_users_play_time_ratio' => self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO
			),
		),

		ReportType::EP_WEBCAST_LIVE_USER_ENGAGEMENT_LEVEL => array(
			self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_DIMENSION_MAP => array(
				'position' => self::DIMENSION_POSITION,
				'user_engagement' => self::DIMENSION_USER_ENGAGEMENT,
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_VIEW_PERIOD),
			self::REPORT_TABLE_FINALIZE_FUNC => 'kKavaReportsMgr::addOfflineMinutes'
		),

		ReportType::EP_WEBCAST_VOD_USER_TOP_CONTENT => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID,
				'full_name' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('name', 'full_name'),
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getUsersInfo',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME))'),
				)
			),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_VOD_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_VOD_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_VOD_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_PERCENTILES_RATIO),
		),

		ReportType::EP_WEBCAST_VOD_LIVE_USERS_ENGAGEMENT => array(
			self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_DIMENSION_MAP => array(
				'first_name' => self::DIMENSION_KUSER_ID,
				'last_name' => self::DIMENSION_KUSER_ID,
				'title' => self::DIMENSION_KUSER_ID,
				'company' => self::DIMENSION_KUSER_ID,
				'email' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('first_name', 'last_name', 'title', 'company', 'email'),
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getUsersInfo',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('FIRST_NAME', 'LAST_NAME', 'CUSTOM_DATA.title', 'CUSTOM_DATA.company', 'EMAIL'),
				),
			),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_MEETING_VIEW_TIME, self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO, self::EVENT_TYPE_REACTION_CLICKED, self::EVENT_TYPE_MEETING_RAISE_HAND, self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_MEETING_VIEW_TIME, self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO, self::EVENT_TYPE_REACTION_CLICKED, self::EVENT_TYPE_MEETING_RAISE_HAND, self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO),
			self::REPORT_TABLE_FINALIZE_FUNC => 'kKavaReportsMgr::addCombinedLiveVodColumn',
			self::REPORT_TOTAL_FINALIZE_FUNC => 'kKavaReportsMgr::addTotalCombinedLiveVodColumn',
			self::REPORT_COLUMN_MAP => array(
				'vod_view_time' => self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME,
				'live_view_time' => self::METRIC_COMBINED_LIVE_VIEW_TIME,
				'total_view_time' => self::METRIC_COMBINED_VOD_LIVE_VIEW_TIME,
				'avg_completion_rate' => self::METRIC_UNIQUE_PERCENTILES_RATIO,
				'engagement_rate' => self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO,
				'count_reaction_clicked' => self::EVENT_TYPE_REACTION_CLICKED,
				'count_raise_hand_clicked' => self::EVENT_TYPE_MEETING_RAISE_HAND,
				'combined_live_engaged_users_play_time_ratio' => self::METRIC_COMBINED_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO
			),
		),

		ReportType::EP_ATTENDEES => array(
			self::REPORT_UNION_DATA_SOURCES =>  array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL, self::DATASOURCE_APPLICATION_EVENTS, self::DATASOURCE_CNC_EVENTS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_ATTENDEES),
		),

		ReportType::EP_VIEWTIME => array(
			self::REPORT_UNION_DATA_SOURCES =>  array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_VIEW_PERIOD_PLAY_TIME),
		),

		ReportType::EP_TOP_MOMENTS => array(
			self::REPORT_UNION_DATA_SOURCES =>  array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_DIMENSION_MAP => array(
				'entry_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'position' => self::DIMENSION_POSITION,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name',
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getEntriesNames'
			),
			self::REPORT_FILTER => array(
				self::DRUID_TYPE => self::DRUID_NOT,
				self::DRUID_FILTER => array(
					self::DRUID_DIMENSION => self::DIMENSION_POSITION,
					self::DRUID_VALUES => array(self::VALUE_UNKNOWN, "0", "")
				)
			),
			self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_VIEW_PERIOD_COUNT)
		),

		ReportType::EP_TOP_SESSIONS => array(
			self::REPORT_UNION_DATA_SOURCES =>  array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
			self::REPORT_DIMENSION_MAP => array(
				'event_session_context_id' => self::DIMENSION_EVENT_SESSION_CONTEXT_ID,
				'name' => self::DIMENSION_EVENT_SESSION_CONTEXT_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::getEntriesNames'
			),
			self::REPORT_METRICS => array(
				self::METRIC_UNIQUE_COMBINED_LIVE_VIEW_PERIOD_USERS,
				self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO,
				self::METRIC_UNIQUE_VOD_VIEW_PERIOD_USERS,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO
			)
		)

	);

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		self::initTransformTimeDimensions();

		return $report_def;
	}

}