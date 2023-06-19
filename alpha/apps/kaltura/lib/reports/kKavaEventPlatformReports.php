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
					self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_AVG_PLAY_TIME),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_COMBINED_LIVE_AVG_PLAY_TIME),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_COMBINED_LIVE_AVG_PLAY_TIME)
				),
			)
		),

		ReportType::EP_WEBCAST_UNIQUE_USERS => array(
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS)
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS, self::METRIC_UNIQUE_VOD_LIVE_VIEWERS),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS, self::METRIC_UNIQUE_VOD_LIVE_VIEWERS),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS, self::METRIC_UNIQUE_VOD_LIVE_VIEWERS)
				),
			)
		),

		ReportType::EP_WEBCAST_ENGAGEMENT => array(
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_UNION_DATA_SOURCES =>  array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO)
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_REACTION_CLICKED_USER_RATIO),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_REACTION_CLICKED_USER_RATIO),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_REACTION_CLICKED_USER_RATIO)
				),
			)
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
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
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
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS),
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS),
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
					self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toLongMappingName',
				),
			),
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS),
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS),
				),
			)
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
					self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toLongMappingName',
				),
			),
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS),
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS),
				),
			)
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
					self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toLongMappingName',
				),
			),
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_VOD_VIEWERS),
				),
				array(
					self::REPORT_UNION_DATA_SOURCES => array(self::DATASOURCE_HISTORICAL, self::DATASOURCE_MEETING_HISTORICAL),
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_COMBINED_LIVE_VIEWERS),
				),
			)
		),

		ReportType::EP_WEBCAST_LIVE_USER_ENGAGEMENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'user_name' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'user_name'),
				self::REPORT_ENRICH_FUNC => 'self::getUserIdAndFullNameWithFallback',
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
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addCombinedLiveVodColumn',
			self::REPORT_TOTAL_FINALIZE_FUNC => 'self::addTotalCombinedLiveVodColumn',
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
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addOfflineMinutes'
		),
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