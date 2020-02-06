<?php

class kKavaQoeReports extends kKavaReportsMgr
{

	//base report defs
	const PLATFORMS_BASE = 'platforms_base';
	const COUNTRY_BASE = 'country_base';
	const REGION_BASE = 'region_base';
	const CITY_BASE = 'city_base';
	const BROWSERS_FAMILIES_BASE = 'browsers_families_base';
	const BROWSERS_BASE = 'browsers_base';
	const OPERATING_SYSTEM_FAMILIES_BASE = 'operating_system_families_base';
	const OPERATING_SYSTEM_BASE = 'operating_system_base';
	const PLAYER_VERSION_BASE = 'player_version_base';
	const ENTRY_BASE = 'entry_base';
	const ISP_BASE = 'isp_base';
	const ERROR_TRACKING_BASE = 'error_tracking_base';
	const CUSTOM_VAR1_BASE = 'custom_var1_base';
	const CUSTOM_VAR2_BASE = 'custom_var2_base';
	const CUSTOM_VAR3_BASE = 'custom_var3_base';
	const APPLICATION_VER_BASE = 'application_ver_base';

	// general
	const DYNAMIC_DATASOURCE_INTERVAL = 21600; //6 hours

	// metrics map - realtime metric => historical metric
	protected static $realtime_to_historical_metrics_map = array(
		self::METRIC_VIEW_BUFFER_TIME_RATIO => self::METRIC_BUFFER_TIME_RATIO,
		self::METRIC_AVG_VIEW_SESSION_ERROR_RATE => self::METRIC_AVG_SESSION_ERROR_RATE,
		self::METRIC_VIEW_UNIQUE_SESSIONS => self::METRIC_VIEW_PERIOD_UNIQUE_SESSIONS,
		self::METRIC_AVG_VIEW_PLAY_TIME_SEC => self::METRIC_AVG_VIEW_PERIOD_PLAY_TIME_SEC,
		self::METRIC_AVG_VIEW_BITRATE => self::METRIC_AVG_BITRATE,
		self::EVENT_TYPE_BUFFER_START => self::METRIC_VIEW_PERIOD_BUFFER_STARTS,
		self::EVENT_TYPE_FLAVOR_SWITCH => self::METRIC_VIEW_PERIOD_FLAVOR_SWITCHES,
		//self map
		self::METRIC_AVG_JOIN_TIME => self::METRIC_AVG_JOIN_TIME,
		self::METRIC_EBVS_RATIO => self::METRIC_EBVS_RATIO,
		self::METRIC_ERROR_UNKNOWN_POSITION_COUNT => self::METRIC_ERROR_UNKNOWN_POSITION_COUNT,
		self::METRIC_ERROR_POSITION_COUNT => self::METRIC_ERROR_POSITION_COUNT,
		self::EVENT_TYPE_ERROR => self::EVENT_TYPE_ERROR,
		self::METRIC_ERROR_SESSION_COUNT => self::METRIC_ERROR_SESSION_COUNT,
	);

	protected static $reports_def_base = array(

		self::PLATFORMS_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE,
			),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_DATE_ID,
		),

		self::COUNTRY_BASE => array(
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
				)
			)
		),

		self::REGION_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'coordinates' => self::DIMENSION_LOCATION_REGION
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
			),
		),

		self::CITY_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'city' => self::DIMENSION_LOCATION_CITY,
				'coordinates' => self::DIMENSION_LOCATION_CITY,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region', 'city'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
			),
		),

		self::BROWSERS_FAMILIES_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'browser_family' => self::DIMENSION_BROWSER_FAMILY
			),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
		),

		self::BROWSERS_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'browser' => self::DIMENSION_BROWSER
			),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
		),

		self::OPERATING_SYSTEM_FAMILIES_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'os_family' => self::DIMENSION_OS_FAMILY
			),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
		),

		self::OPERATING_SYSTEM_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'os' => self::DIMENSION_OS
			),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
		),

		self::PLAYER_VERSION_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'player_version' => self::DIMENSION_PLAYER_VERSION
			),
		),

		self::ENTRY_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'entry_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'peer' => 'entryPeer',
						'columns' => array('NAME'),
					)
				),
			),
		),

		self::ISP_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'isp' => self::DIMENSION_LOCATION_ISP,
			),
		),

		self::ERROR_TRACKING_BASE => array(
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_ERROR,
				self::METRIC_ERROR_SESSION_COUNT,
			),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array(self::EVENT_TYPE_ERROR)
			),
		),

		self::CUSTOM_VAR1_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'custom_var1' => self::DIMENSION_CUSTOM_VAR1
			),
		),

		self::CUSTOM_VAR2_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'custom_var2' => self::DIMENSION_CUSTOM_VAR2
			),
		),

		self::CUSTOM_VAR3_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'custom_var3' => self::DIMENSION_CUSTOM_VAR3
			),
		),

		self::APPLICATION_VER_BASE => array(
			self::REPORT_DIMENSION_MAP => array(
				'application_version' => self::DIMENSION_APPLICATION_VER
			),
		),

	);

	protected static $reports_def = array(

		ReportType::QOE_OVERVIEW => array(
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_AVG_JOIN_TIME,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::METRIC_AVG_VIEW_SESSION_ERROR_RATE,
				self::METRIC_VIEW_UNIQUE_SESSIONS,
				self::METRIC_AVG_VIEW_PLAY_TIME_SEC,
			),
		),

		//EXPERIENCE

		ReportType::QOE_EXPERIENCE => array(
			self::REPORT_METRICS => array(
				self::METRIC_AVG_JOIN_TIME,
				self::EVENT_TYPE_BUFFER_START,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::METRIC_AVG_VIEW_BITRATE,
			),
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_AVG_JOIN_TIME,
				self::EVENT_TYPE_BUFFER_START,
				self::METRIC_VIEW_BUFFER_TIME_RATIO,
				self::METRIC_AVG_VIEW_BITRATE,
			)
		),

		ReportType::QOE_EXPERIENCE_PLATFORMS => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::PLATFORMS_BASE,
			),
		),
		
		ReportType::QOE_EXPERIENCE_COUNTRY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::COUNTRY_BASE,
			),
		),

		ReportType::QOE_EXPERIENCE_REGION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::REGION_BASE,
			),
		),

		ReportType::QOE_EXPERIENCE_CITY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::CITY_BASE
			),
		),

		ReportType::QOE_EXPERIENCE_BROWSERS_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::BROWSERS_FAMILIES_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_BROWSERS => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::BROWSERS_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_OPERATING_SYSTEM_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::OPERATING_SYSTEM_FAMILIES_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_OPERATING_SYSTEM => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::OPERATING_SYSTEM_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_PLAYER_VERSION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::PLAYER_VERSION_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_ENTRY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::ENTRY_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_ISP => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::ISP_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_CUSTOM_VAR1 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::CUSTOM_VAR1_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_CUSTOM_VAR2 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::CUSTOM_VAR2_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_CUSTOM_VAR3 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::CUSTOM_VAR3_BASE,
			)
		),

		ReportType::QOE_EXPERIENCE_APPLICATION_VERSION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_EXPERIENCE,
				self::APPLICATION_VER_BASE,
			)
		),

		//ENGAGEMENT

		ReportType::QOE_ENGAGEMENT => array(
			self::REPORT_METRICS => array(
				self::METRIC_VIEW_UNIQUE_SESSIONS,
				self::METRIC_AVG_VIEW_PLAY_TIME_SEC,
				self::METRIC_EBVS_RATIO,
			),
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_SESSIONS,
				self::METRIC_AVG_VIEW_PLAY_TIME_SEC,
				self::METRIC_EBVS_RATIO,
			)
		),

		ReportType::QOE_ENGAGEMENT_PLATFORMS => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::PLATFORMS_BASE,
			),
		),

		ReportType::QOE_ENGAGEMENT_COUNTRY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::COUNTRY_BASE,
			),
		),

		ReportType::QOE_ENGAGEMENT_REGION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::REGION_BASE,
			),
		),

		ReportType::QOE_ENGAGEMENT_CITY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::CITY_BASE
			),
		),

		ReportType::QOE_ENGAGEMENT_BROWSERS_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::BROWSERS_FAMILIES_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_BROWSERS => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::BROWSERS_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_OPERATING_SYSTEM_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::OPERATING_SYSTEM_FAMILIES_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_OPERATING_SYSTEM => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::OPERATING_SYSTEM_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_PLAYER_VERSION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::PLAYER_VERSION_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_ENTRY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::ENTRY_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_ISP => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::ISP_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_CUSTOM_VAR1 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::CUSTOM_VAR1_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_CUSTOM_VAR2 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::CUSTOM_VAR2_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_CUSTOM_VAR3 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::CUSTOM_VAR3_BASE,
			)
		),

		ReportType::QOE_ENGAGEMENT_APPLICATION_VERSION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_ENGAGEMENT,
				self::APPLICATION_VER_BASE,
			)
		),

		//stream quality
		ReportType::QOE_STREAM_QUALITY => array(
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_FLAVOR_SWITCH,
				self::METRIC_AVG_VIEW_BITRATE,
			),
			self::REPORT_GRAPH_METRICS => array(
				self::EVENT_TYPE_FLAVOR_SWITCH,
				self::METRIC_AVG_VIEW_BITRATE,
			),
		),

		ReportType::QOE_STREAM_QUALITY_PLATFORMS => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::PLATFORMS_BASE,
			),
		),

		ReportType::QOE_STREAM_QUALITY_COUNTRY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::COUNTRY_BASE,
			),
		),

		ReportType::QOE_STREAM_QUALITY_REGION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::REGION_BASE,
			),
		),

		ReportType::QOE_STREAM_QUALITY_CITY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::CITY_BASE
			),
		),

		ReportType::QOE_STREAM_QUALITY_BROWSERS_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::BROWSERS_FAMILIES_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_BROWSERS => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::BROWSERS_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_OPERATING_SYSTEM_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::OPERATING_SYSTEM_FAMILIES_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_OPERATING_SYSTEM => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::OPERATING_SYSTEM_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_PLAYER_VERSION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::PLAYER_VERSION_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_ENTRY => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::ENTRY_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_ISP => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::ISP_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_CUSTOM_VAR1 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::CUSTOM_VAR1_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_CUSTOM_VAR2 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::CUSTOM_VAR2_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_CUSTOM_VAR3 => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::CUSTOM_VAR3_BASE,
			)
		),

		ReportType::QOE_STREAM_QUALITY_APPLICATION_VERSION => array(
			self::REPORT_BASE_DEF => array(
				ReportType::QOE_STREAM_QUALITY,
				self::APPLICATION_VER_BASE,
			)
		),

		ReportType::QOE_ERROR_TRACKING => array(
			self::REPORT_METRICS => array(
				self::METRIC_AVG_VIEW_SESSION_ERROR_RATE,
				self::METRIC_ERROR_UNKNOWN_POSITION_COUNT,
				self::METRIC_ERROR_POSITION_COUNT,
			),
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_AVG_VIEW_SESSION_ERROR_RATE,
				self::METRIC_ERROR_UNKNOWN_POSITION_COUNT,
				self::METRIC_ERROR_POSITION_COUNT,
			),
		),

		ReportType::QOE_ERROR_TRACKING_CODES => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
			),
			self::REPORT_DIMENSION_MAP => array(
				'error_code' => self::DIMENSION_EVENT_VAR1,
			),
		),

		ReportType::QOE_ERROR_TRACKING_PLATFORMS => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::PLATFORMS_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_BROWSERS_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::BROWSERS_FAMILIES_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_BROWSERS => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::BROWSERS_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_OPERATING_SYSTEM_FAMILIES => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::OPERATING_SYSTEM_FAMILIES_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_OPERATING_SYSTEM => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::OPERATING_SYSTEM_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_PLAYER_VERSION => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::PLAYER_VERSION_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_ENTRY => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::ENTRY_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_CUSTOM_VAR1 => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::CUSTOM_VAR1_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_CUSTOM_VAR2 => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::CUSTOM_VAR2_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_CUSTOM_VAR3 => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::CUSTOM_VAR3_BASE,
			),
		),

		ReportType::QOE_ERROR_TRACKING_APPLICATION_VERSION => array(
			self::REPORT_BASE_DEF => array(
				self::ERROR_TRACKING_BASE,
				self::APPLICATION_VER_BASE,
			),
		),

		ReportType::QOE_VOD_SESSION_FLOW => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
			self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_VOD),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY_REQUESTED, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_ERROR_POSITION_COUNT, self::METRIC_ERROR_UNKNOWN_POSITION_COUNT, self::METRIC_COUNT_EBVS)
		),

		ReportType::QOE_LIVE_SESSION_FLOW => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
			self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY_REQUESTED, self::EVENT_TYPE_PLAY, self::METRIC_ERROR_POSITION_COUNT, self::METRIC_ERROR_UNKNOWN_POSITION_COUNT, self::METRIC_COUNT_EBVS)
		)

	);

	protected static function initTransformTimeDimensions()
	{
		self::$transform_time_dimensions = array(
			self::GRANULARITY_HOUR => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_DAY => array('kKavaReportsMgr', 'timestampToUnixDate'),
			self::GRANULARITY_MONTH => array('kKavaReportsMgr', 'timestampToMonthId'),
			self::GRANULARITY_TEN_SECOND => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_MINUTE => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_TEN_MINUTE => array('kKavaReportsMgr', 'timestampToUnixtime'),
		);
	}

	protected static function shouldQueryHistorical($input_filter)
	{
		return ($input_filter && (($input_filter->from_day && $input_filter->to_day) ||
			($input_filter->from_date && $input_filter->to_date &&
			($input_filter->to_date - $input_filter->from_date >= self::DYNAMIC_DATASOURCE_INTERVAL))));
	}

	protected static function replaceMetricsToHistorical($metrics)
	{
		$historical_metrics = array();
		$column_map = array();
		foreach ($metrics as $metric)
		{
			if (!isset(self::$realtime_to_historical_metrics_map[$metric]))
			{
				throw new Exception('Undefined realtime to historical metric map');
			}
			$cur_metric = self::$realtime_to_historical_metrics_map[$metric];
			$historical_metrics[] = $cur_metric;
			$header = isset(self::$metrics_to_headers[$metric]) ? self::$metrics_to_headers[$metric] : $metric;
			$column_map[$header] = $cur_metric;
		}
		return array($historical_metrics, $column_map);
	}

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		if (isset($report_def[self::REPORT_BASE_DEF]))
		{
			$base_defs = $report_def[self::REPORT_BASE_DEF];
			$base_defs = is_array($base_defs) ? $base_defs : array($base_defs);
			foreach ($base_defs as $base_def)
			{
				if (isset(self::$reports_def_base[$base_def]))
				{
					$report_def = array_merge(self::$reports_def_base[$base_def], $report_def);
				}
				elseif (isset(self::$reports_def[$base_def]))
				{
					$report_def = array_merge(self::$reports_def[$base_def], $report_def);
				}
			}
		}

		self::initTransformTimeDimensions();

		if (isset($report_def[self::REPORT_DATA_SOURCE]))
		{
			return $report_def;
		}

		if (!self::shouldQueryHistorical($input_filter))
		{
			$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_REALTIME;
			return $report_def;
		}

		// query historical
		$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_HISTORICAL;
		$column_map = array();
		$metrics_defs = array(self::REPORT_METRICS, self::REPORT_GRAPH_METRICS);
		foreach ($metrics_defs as $metrics_def)
		{
			if (!isset($report_def[$metrics_def]))
			{
				continue;
			}
			list($metrics, $column_map) = self::replaceMetricsToHistorical($report_def[$metrics_def]);
			$report_def[$metrics_def] = $metrics;
		}

		// assuming that REPORT_METRICS = REPORT_GRAPH_METRICS
		// need to change if REPORT_METRICS != REPORT_GRAPH_METRICS
		if ($column_map)
		{
			$report_def[self::REPORT_COLUMN_MAP] = $column_map;
		}
		return $report_def;
	}

}
