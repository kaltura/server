<?php

class kKavaWebcastReports extends kKavaReportsMgr
{

	protected static $reports_def = array(

		ReportType::HIGHLIGHTS_WEBCAST => array(
			self::REPORT_GRAPH_METRICS => array(
				self::EVENT_TYPE_PLAY,
				self::METRIC_UNIQUE_VIEWERS,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME,
			),
			self::REPORT_TOTAL_METRICS => array(
				self::EVENT_TYPE_PLAY,
				self::METRIC_UNIQUE_VIEWERS,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME,
			),
			// add partner id as filter dimension (objectIds param) since we are using this report for salesforce sync integration
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
		),

		ReportType::ENGAGEMENT_WEBCAST => array(
			self::REPORT_TOTAL_METRICS => array(
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO,
				self::EVENT_TYPE_REGISTERED,
			),
		),

		ReportType::QUALITY_WEBCAST => array(
			self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR),
			self::REPORT_TOTAL_METRICS => array(
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_AVG_BITRATE,
			),
		),

		ReportType::MAP_OVERLAY_COUNTRY_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' =>  self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_PLAY,
				self::METRIC_UNIQUE_VIEWERS,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
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

		ReportType::MAP_OVERLAY_REGION_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'coordinates' => self::DIMENSION_LOCATION_REGION
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_PLAY,
				self::METRIC_UNIQUE_VIEWERS,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
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

		ReportType::MAP_OVERLAY_CITY_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'city' => self::DIMENSION_LOCATION_CITY,
				'coordinates' => self::DIMENSION_LOCATION_CITY,
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_PLAY,
				self::METRIC_UNIQUE_VIEWERS,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
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

		ReportType::PLATFORMS_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE,
			),
			self::REPORT_METRICS => array(
				self::METRIC_VOD_PLAYS_COUNT,
				self::METRIC_LIVE_PLAYS_COUNT,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
			),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DEVICE,
		),

		ReportType::TOP_DOMAINS_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_DOMAIN,
				'domain_name' => self::DIMENSION_DOMAIN
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_PLAY,
				self::METRIC_UNIQUE_VIEWERS,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_VOD_UNIQUE_PERCENTILES_RATIO,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
			),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DOMAIN,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'referrer' => self::DIMENSION_URL,
			),
		),

		ReportType::TOP_USERS_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'user_name' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'user_name'),
				self::REPORT_ENRICH_FUNC => 'self::getUserIdAndFullNameWithFallback',
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_REGISTERED,
				self::EVENT_TYPE_PLAYER_IMPRESSION,
				self::EVENT_TYPE_PLAY,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_TOTAL_UNIQUE_PERCENTILES,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
			),
			self::REPORT_TOTAL_METRICS => array(
				self::EVENT_TYPE_REGISTERED,
				self::EVENT_TYPE_PLAYER_IMPRESSION,
				self::EVENT_TYPE_PLAY,
				self::METRIC_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME,
				self::METRIC_LIVE_BUFFER_TIME_RATIO,
				self::METRIC_LIVE_ENGAGED_USERS_PLAY_TIME_RATIO,
			),
			self::REPORT_FORCE_TOTAL_COUNT => true,
		),

		ReportType::ENGAGEMENT_BREAKDOWN_WEBCAST => array(
			self::REPORT_METRICS => array(
				self::METRIC_LIVE_NO_ENGAGEMENT_RATIO,
				self::METRIC_LIVE_LOW_ENGAGEMENT_RATIO,
				self::METRIC_LIVE_FAIR_ENGAGEMENT_RATIO,
				self::METRIC_LIVE_GOOD_ENGAGEMENT_RATIO,
				self::METRIC_LIVE_HIGH_ENGAGEMENT_RATIO,
			),
		),

		ReportType::ENGAGMENT_TIMELINE_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'position' => self::DIMENSION_POSITION,
			),
			self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR),
			self::REPORT_METRICS => array(self::METRIC_LIVE_ENGAGED_USERS_RATIO),
			self::REPORT_EDIT_FILTER_FUNC => 'self::editWebcastEngagementTimelineFilter',
			self::REPORT_TABLE_FINALIZE_FUNC => "self::addZeroMinutes",
		),

		ReportType::ENGAGEMENT_TOOLS_WEBCAST => array(
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_ADD_TO_CALENDAR_CLICKED,
				self::EVENT_TYPE_REACTION_CLICKED,
			),
		),

		ReportType::REACTIONS_BREAKDOWN_WEBCAST => array(
			self::REPORT_DIMENSION_MAP => array(
				'reaction' => self::DIMENSION_EVENT_VAR1,
			),
			self::REPORT_FILTER => array(
				array(
					self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
					self::DRUID_VALUES => array(self::EVENT_TYPE_REACTION_CLICKED)
				)
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_REACTION_CLICKED,
			),
		),

	);

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_HISTORICAL;
		if (!isset($report_def[self::REPORT_PLAYBACK_TYPES]))
		{
			$report_def[self::REPORT_PLAYBACK_TYPES] = array(self::PLAYBACK_TYPE_VOD, self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR);
		}

		self::initTransformTimeDimensions();

		return $report_def;
	}

}
