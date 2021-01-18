<?php

class kKavaRealtimeReports extends kKavaReportsMgr
{
	const REALTIME_QUERY_CACHE_EXPIRATION = 30;

	protected static $reports_def = array(

		ReportType::MAP_OVERLAY_COUNTRY_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' =>  self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY
			),
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::EVENT_TYPE_VIEW,
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
			)
		),

		ReportType::MAP_OVERLAY_REGION_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'coordinates' => self::DIMENSION_LOCATION_REGION
			),
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::EVENT_TYPE_VIEW,
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
		),

		ReportType::MAP_OVERLAY_CITY_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'city' => self::DIMENSION_LOCATION_CITY,
				'coordinates' => self::DIMENSION_LOCATION_CITY,
			),
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::EVENT_TYPE_VIEW,
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
		),

		ReportType::PLATFORMS_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE
			),
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_PLAY_TIME_SEC,
			),
		),

		ReportType::USERS_OVERVIEW_REALTIME => array(
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::EVENT_TYPE_VIEW,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
			),
		),

		ReportType::QOS_OVERVIEW_REALTIME => array(
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::METRIC_AVG_VIEW_BITRATE,
			),
		),

		ReportType::DISCOVERY_REALTIME => array(
			self::REPORT_GRANULARITY => self::GRANULARITY_DYNAMIC,
			self::REPORT_SKIP_TOTAL_FROM_GRAPH => true,
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_VIEW_UNIQUE_AUDIENCE_DVR,
				self::METRIC_AVG_VIEW_BITRATE,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_LIVE_LATENCY,
				self::METRIC_AVG_VIEW_DROPPED_FRAMES_RATIO,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_AVG_VIEW_DVR,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
			),
			self::REPORT_TOTAL_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_VIEW_UNIQUE_AUDIENCE_DVR,
				self::METRIC_AVG_VIEW_BITRATE,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_LIVE_LATENCY,
				self::METRIC_AVG_VIEW_DROPPED_FRAMES_RATIO,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_AVG_VIEW_DVR,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
			)
		),

		ReportType::ENTRY_LEVEL_USERS_DISCOVERY_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'user_name' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'user_name'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)'),
					'peer' => 'kuserPeer',
				)
			),
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_LIVE_PLAY_TIME_SEC,
				self::METRIC_VIEW_DVR_PLAY_TIME_SEC,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_FLAVOR_PARAMS_VIEW_COUNT,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
			),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addFlavorParamColumn',
			self::REPORT_TOTAL_FINALIZE_FUNC => 'self::addFlavorParamTotalColumn',
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_TABLE_MAP => array(
				'sum_view_time_live' => self::METRIC_VIEW_LIVE_PLAY_TIME_SEC,
				'sum_view_time_dvr' => self::METRIC_VIEW_DVR_PLAY_TIME_SEC,
				'sum_view_time' => self::METRIC_VIEW_PLAY_TIME_SEC,
				'avg_view_buffering' => self::METRIC_AVG_VIEW_BUFFERING,
				'avg_view_engagement' => self::METRIC_AVG_VIEW_ENGAGEMENT,
				'known_flavor_params_view_count' => self::METRIC_FLAVOR_PARAMS_VIEW_COUNT,
				'view_buffer_time_ratio' => self::METRIC_VIEW_BUFFER_TIME_RATIO,
			),
			self::REPORT_TOTAL_MAP => array(
				'sum_view_time_live' => self::METRIC_VIEW_LIVE_PLAY_TIME_SEC,
				'sum_view_time_dvr' => self::METRIC_VIEW_DVR_PLAY_TIME_SEC,
				'sum_view_time' => self::METRIC_VIEW_PLAY_TIME_SEC,
				'avg_view_buffering' => self::METRIC_AVG_VIEW_BUFFERING,
				'avg_view_engagement' => self::METRIC_AVG_VIEW_ENGAGEMENT,
				'known_flavor_params_view_count' => self::METRIC_FLAVOR_PARAMS_VIEW_COUNT,
				'view_buffer_time_ratio' => self::METRIC_VIEW_BUFFER_TIME_RATIO,
			),
		),

		ReportType::ENTRY_LEVEL_USERS_STATUS_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'playback_type' => self::DIMENSION_PLAYBACK_TYPE,
			),
			self::REPORT_METRICS => array(),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID'),
					'peer' => 'kuserPeer',
				)
			)
		),

		ReportType::PLATFORMS_DISCOVERY_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE
			),
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::METRIC_FLAVOR_PARAMS_VIEW_COUNT,
			),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addFlavorParamColumn',
			self::REPORT_TOTAL_FINALIZE_FUNC => 'self::addFlavorParamTotalColumn',
			self::REPORT_TABLE_MAP => array(
				'view_unique_audience' => self::METRIC_VIEW_UNIQUE_AUDIENCE,
				'view_unique_engaged_users' => self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				'view_unique_buffering_users' => self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				'sum_view_time' => self::METRIC_VIEW_PLAY_TIME_SEC,
				'avg_view_buffering' => self::METRIC_AVG_VIEW_BUFFERING,
				'avg_view_engagement' => self::METRIC_AVG_VIEW_ENGAGEMENT,
				'view_buffer_time_ratio' => self::METRIC_VIEW_BUFFER_TIME_RATIO,
				'known_flavor_params_view_count' => self::METRIC_FLAVOR_PARAMS_VIEW_COUNT,
			),
			self::REPORT_TOTAL_MAP => array(
				'view_unique_audience' => self::METRIC_VIEW_UNIQUE_AUDIENCE,
				'view_unique_engaged_users' => self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				'view_unique_buffering_users' => self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				'sum_view_time' => self::METRIC_VIEW_PLAY_TIME_SEC,
				'avg_view_buffering' => self::METRIC_AVG_VIEW_BUFFERING,
				'avg_view_engagement' => self::METRIC_AVG_VIEW_ENGAGEMENT,
				'view_buffer_time_ratio' => self::METRIC_VIEW_BUFFER_TIME_RATIO,
				'known_flavor_params_view_count' => self::METRIC_FLAVOR_PARAMS_VIEW_COUNT,
			),
		),

		ReportType::PLAYBACK_TYPE_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'playback_type' => self::DIMENSION_PLAYBACK_TYPE,
			),
			self::REPORT_METRICS => array(self::METRIC_VIEW_PLAY_TIME_SEC)
		),

		ReportType::CONTENT_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'entry_id' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_METRICS => array(
				self::METRIC_AVG_VIEW_ENGAGEMENT,
				self::METRIC_AVG_VIEW_BUFFERING,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
			),
		),

		ReportType::DISCOVERY_VIEW_REALTIME => array(
			self::REPORT_GRANULARITY => self::GRANULARITY_DYNAMIC,
			self::REPORT_SKIP_TOTAL_FROM_GRAPH => true,
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_DYNAMIC_VIEWERS,
				self::METRIC_DYNAMIC_VIEWERS_BUFFERING,
				self::METRIC_DYNAMIC_VIEWERS_ENGAGEMENT,
				self::METRIC_DYNAMIC_VIEWERS_DVR,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_AVG_VIEW_BITRATE,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_LIVE_LATENCY,
				self::METRIC_AVG_VIEW_DROPPED_FRAMES_RATIO,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
			),
			self::REPORT_TOTAL_METRICS => array(
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_AVG_VIEW_BITRATE,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_LIVE_LATENCY,
				self::METRIC_AVG_VIEW_DROPPED_FRAMES_RATIO,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
			),
		),

		ReportType::TOP_LIVE_NOW_ENTRIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'entry_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'creator_name' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name', 'creator_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('NAME', 'KUSER_ID'),
						'peer' => 'entryPeer',
					)
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('creator_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)'),
						'peer' => 'kuserPeer',
					)
				),
			),
			self::REPORT_EDIT_FILTER_FUNC => 'self::includeOnlyLiveNowEntriesEditFilter',
			self::REPORT_METRICS => array(self::EVENT_TYPE_VIEW, self::METRIC_AVG_VIEW_ENGAGEMENT, self::METRIC_AVG_VIEW_BUFFERING, self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH)
		),

		ReportType::TOP_ENDED_BROADCAST_ENTRIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'entry_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name',
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('NAME'),
					'peer' => 'entryPeer',
				)
			),
			self::REPORT_EDIT_FILTER_FUNC => 'self::excludeLiveNowEntriesEditFilter',
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_REALTIME,
					self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR),
					self::REPORT_METRICS => array(self::METRIC_AVG_VIEW_ENGAGEMENT, self::METRIC_AVG_VIEW_BUFFERING, self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH),
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_REALTIME,
					self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR),
					self::REPORT_GRANULARITY => self::GRANULARITY_DYNAMIC,
					self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
					self::REPORT_METRICS => array(self::METRIC_DYNAMIC_VIEWERS),
					self::REPORT_TABLE_FINALIZE_FUNC => 'self::getPeakViewers'
				),
			),
			self::REPORT_TABLE_MAP => array(
				'viewers' => self::METRIC_DYNAMIC_VIEWERS,
				'avg_view_engagement' => self::METRIC_AVG_VIEW_ENGAGEMENT,
				'avg_view_buffering' => self::METRIC_AVG_VIEW_BUFFERING,
				'avg_view_downstream_bandwidth' => self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
			)
		),
	);

	protected static function initTransformTimeDimensions()
	{
		self::$transform_time_dimensions = array(
			self::GRANULARITY_HOUR => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_DAY => array('kKavaReportsMgr', 'timestampToUnixDate'),
			self::GRANULARITY_MONTH => array('kKavaReportsMgr', 'timestampToMonthId'),
			self::GRANULARITY_TEN_SECOND => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_MINUTE => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_DYNAMIC => array('kKavaReportsMgr', 'timestampToUnixtime'),
		);
	}

	protected static function initQueryCache()
	{
		self::$query_cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_DRUID_QUERIES);
		self::$query_cache_expiration = self::REALTIME_QUERY_CACHE_EXPIRATION;
	}

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		self::initTransformTimeDimensions();
		self::initQueryCache();

		//default datasource
		if (!isset($report_def[self::REPORT_JOIN_GRAPHS]) && !isset($report_def[self::REPORT_DATA_SOURCE]))
		{
			$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_REALTIME;
		}

		//filter on playback types
		if (!isset($report_def[self::REPORT_PLAYBACK_TYPES]))
		{
			$report_def[self::REPORT_PLAYBACK_TYPES] = array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR);
		}

		return $report_def;
	}

}
