<?php

class kKavaVeRegistrationReports extends kKavaReportsMgr
{
	const VE_REGISTRATION_QUERY_CACHE_EXPIRATION = 30;

	protected static $reports_def = array(

		ReportType::VE_HIGHLIGHTS => array(
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::EVENT_TYPE_VE_CONFIRMED,
				self::EVENT_TYPE_VE_ATTENDED,
				self::EVENT_TYPE_VE_PARTICIPATED,
				self::EVENT_TYPE_VE_BLOCKED,
				self::EVENT_TYPE_VE_UNREGISTERED,
				self::EVENT_TYPE_VE_INVITED,
				self::EVENT_TYPE_VE_CREATED,
			),
			self::REPORT_GRAPH_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::EVENT_TYPE_VE_CONFIRMED,
				self::EVENT_TYPE_VE_ATTENDED,
				self::EVENT_TYPE_VE_PARTICIPATED,
				self::EVENT_TYPE_VE_BLOCKED,
				self::EVENT_TYPE_VE_UNREGISTERED,
				self::EVENT_TYPE_VE_INVITED,
				self::EVENT_TYPE_VE_CREATED,
			)
		),

		ReportType::VE_REGISTERED_PLATFORMS => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::METRIC_REGISTERED_UNIQUE_USERS,
			),
		),

		ReportType::VE_REGISTERED_INDUSTRY => array(
			self::REPORT_DIMENSION_MAP => array(
				'industry' => self::DIMENSION_INDUSTRY,
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::METRIC_REGISTERED_UNIQUE_USERS,
			),
		),

		ReportType::VE_REGISTERED_ROLES => array(
			self::REPORT_DIMENSION_MAP => array(
				'role' => self::DIMENSION_ROLE,
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::METRIC_REGISTERED_UNIQUE_USERS,
			),
		),

		ReportType::VE_REGISTERED_COUNTRIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' =>  self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::METRIC_REGISTERED_UNIQUE_USERS,
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

		ReportType::VE_REGISTERED_WORLD_REGIONS => array(
			self::REPORT_DIMENSION_MAP => array(
				'world_region' => self::DIMENSION_LOCATION_WORLD_REGION,
			),
			self::REPORT_METRICS => array(
				self::EVENT_TYPE_VE_REGISTERED,
				self::METRIC_REGISTERED_UNIQUE_USERS,
			),
		),

		ReportType::VE_USER_HIGHLIGHTS => array(
			self::REPORT_METRICS => array(
				self::METRIC_REGISTERED_UNIQUE_USERS,
			),
			self::REPORT_GRAPH_METRICS => array(
				self::METRIC_REGISTERED_UNIQUE_USERS,
			)
		),
	);

	protected static function initQueryCache()
	{
		self::$query_cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_DRUID_QUERIES);
		self::$query_cache_expiration = self::VE_REGISTRATION_QUERY_CACHE_EXPIRATION;
	}

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_VE_REGISTRATION;

		self::initTransformTimeDimensions();
		self::initQueryCache();

		return $report_def;
	}
}
