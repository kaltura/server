<?php

class kKavaRealtimeReports extends kKavaReportsMgr
{

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
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
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
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region', 'city'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
			),
		),

		ReportType::PLATFORMS_REALTIME => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_VIEW),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DEVICE,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'os' => self::DIMENSION_OS
			),
		),

		ReportType::USERS_OVERVIEW_REALTIME => array(
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
			),
		),

		ReportType::QOS_OVERVIEW_REALTIME => array(
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
			),
		),

		ReportType::DISCOVERY_REALTIME => array(
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_VIEW_UNIQUE_AUDIENCE,
				self::METRIC_VIEW_UNIQUE_ENGAGED_USERS,
				self::METRIC_VIEW_UNIQUE_BUFFERING_USERS,
				self::METRIC_AVG_VIEW_DOWNSTREAM_BANDWIDTH,
				self::METRIC_VIEW_UNIQUE_AUDIENCE_DVR,
				self::METRIC_AVG_VIEW_BITRATE,
				self::METRIC_VIEW_PLAY_TIME_SEC,
				self::METRIC_AVG_VIEW_LATENCY,
				self::METRIC_AVG_VIEW_DROPPED_FRAMES_RATIO,
			),
		),

	);

	protected static function initTransformTimeDimensions()
	{
		self::$transform_time_dimensions = array(
			self::GRANULARITY_HOUR => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_DAY => array('kKavaReportsMgr', 'timestampToUnixDate'),
//			self::GRANULARITY_MONTH => array('kKavaReportsMgr', 'timestampToMonthId'),
			self::GRANULARITY_TEN_SECOND => array('kKavaReportsMgr', 'timestampToUnixtime'),
			self::GRANULARITY_MINUTE => array('kKavaReportsMgr', 'timestampToUnixtime'),
		);
	}

	public static function getReportDef($report_type)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		self::initTransformTimeDimensions();

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
