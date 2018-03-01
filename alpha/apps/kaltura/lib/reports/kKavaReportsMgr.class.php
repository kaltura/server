<?php

class kKavaReportsMgr extends kKavaBase
{
	// metrics
	const METRIC_TOTAL_PLAY_TIME = 'playTimeSumMin';
	const METRIC_TOTAL_PLAY_TIME_SEC = 'playTimeSum';
	const METRIC_AVG_PLAY_TIME = 'playTimeAvg';
	const METRIC_PLAYER_IMPRESSION_RATIO = 'playerImpressionRatio';
	const METRIC_AVG_DROP_OFF = 'avgDropOffRatio';
	const METRIC_PLAYTHROUGH_RATIO = 'playThroughRatio';
	const METRIC_TOTAL_ENTRIES = 'totalEntries';
	const METRIC_UNIQUE_USERS = 'uniqueUsers';
	const METRIC_UNIQUE_USER_IDS = 'uniqueUserIds';
	const METRIC_PLAYTHROUGH = 'playThrough';
	const METRIC_TOTAL_COUNT = 'total_count';
	const METRIC_COUNT_UGC = 'count_ugc';
	const METRIC_COUNT_ADMIN = 'count_admin';
	
	// report settings
	const REPORT_DATA_SOURCE = 'report_data_source';
	const REPORT_FILTER = 'report_filter';
	const REPORT_DIMENSION = 'report_dimension';
	const REPORT_METRICS = 'report_metrics';
	const REPORT_DETAIL_DIM_HEADERS = 'report_detail_dimensions_headers';
	const REPORT_GRAPH_METRICS = 'report_graph_metrics';
	const REPORT_ENRICH_DEF = 'report_enrich_definition';
	const REPORT_GRANULARITY = 'report_granularity';
	const REPORT_ENRICH_FIELD = 'field';
	const REPORT_ENRICH_FUNC = 'func';
	const REPORT_ENRICH_CONTEXT = 'context';
	const REPORT_TOTAL_METRICS = 'report_total_metrics';
	const REPORT_DRILLDOWN_GRANULARITY = 'report_drilldown_granularity';
	const REPORT_DRILLDOWN_DIMENSION = 'report_drilldown_dimension';
	const REPORT_DRILLDOWN_METRICS = 'report_drilldown_metrics';
	const REPORT_DRILLDOWN_DETAIL_DIM_HEADERS = 'report_drilldown_detail_dimensions_headers';
	const REPORT_CARDINALITY_METRIC = 'report_cardinality_metric';
	const REPORT_PLAYBACK_TYPES = 'report_playback_types';
	const REPORT_OBJECT_IDS_TRANSFORM = 'report_object_ids_transform';
	const REPORT_FILTER_DIMENSION = 'report_filter_dimension';

	const REPORT_LEGACY = 'report_legacy';
	const REPORT_LEGACY_MAPPING = 'legacy_mapping';
	const REPORT_LEGACY_JOIN_FIELD = 'join_field';
	
	// limits
	const MAX_RESULT_SIZE = 12000;
	const MAX_CSV_RESULT_SIZE = 60000;
	const MAX_CUSTOM_REPORT_RESULT_SIZE = 100000;
	const MIN_THRESHOLD = 500;
	
	const ENRICH_CHUNK_SIZE = 10000;
	const CLIENT_TAG_PRIORITY = 5;
	
	static $reports_def = array(
		myReportsMgr::REPORT_TYPE_TOP_CONTENT => array(
			self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'entry_name'),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_FIELD => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF => array(
			self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
			self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'entry_name'),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_FIELD => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_CARDINALITY_METRIC => self::EVENT_TYPE_PLAY
		),

		myReportsMgr::REPORT_TYPE_CONTENT_INTERACTIONS => array(
			self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'entry_name'),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_FIELD => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
		),

		myReportsMgr::REPORT_TYPE_MAP_OVERLAY => array(
			self::REPORT_DIMENSION => self::DIMENSION_LOCATION_COUNTRY,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'country'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_LOCATION_COUNTRY,
			self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_LOCATION_REGION,
			self::REPORT_DRILLDOWN_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array('object_id', 'location_name'),
		),

		myReportsMgr::REPORT_TYPE_TOP_SYNDICATION => array(
			self::REPORT_DIMENSION => self::DIMENSION_DOMAIN,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'domain_name'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DOMAIN,
			self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_URL,
			self::REPORT_DRILLDOWN_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array('referrer'),
		),

		myReportsMgr::REPORT_TYPE_USER_ENGAGEMENT => array(
			self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('name'),
			self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
		),

		myReportsMgr::REPORT_TYPE_SPECIFIC_USER_ENGAGEMENT => array(
			self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('entry_name'),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_FIELD => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'),
			self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
		),

		myReportsMgr::REPORT_TYPE_USER_TOP_CONTENT => array(
			self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('name'),
			self::REPORT_METRICS => array(self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_TOTAL_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
		),

		myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF => array(
			self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
			self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('name'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_CARDINALITY_METRIC => self::EVENT_TYPE_PLAY
		),

		myReportsMgr::REPORT_TYPE_USER_CONTENT_INTERACTIONS => array(
			self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('name'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
		),

		myReportsMgr::REPORT_TYPE_APPLICATIONS => array(
			self::REPORT_DIMENSION => self::DIMENSION_APPLICATION,
			self::REPORT_DETAIL_DIM_HEADERS => array('name'),
			self::REPORT_METRICS => array(),
		),

		myReportsMgr::REPORT_TYPE_PLATFORMS => array(
			self::REPORT_DIMENSION => self::DIMENSION_DEVICE,
			self::REPORT_DETAIL_DIM_HEADERS => array('device'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DEVICE,
			self::REPORT_OBJECT_IDS_TRANSFORM => array('kKavaReportsMgr', 'fromSafeId'),
			self::REPORT_DRILLDOWN_GRANULARITY => self::DRUID_GRANULARITY_ALL,
			self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_OS,
			self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array('os'),
		),

		myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM => array(
			self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
			self::REPORT_DIMENSION => self::DIMENSION_OS,
			self::REPORT_DETAIL_DIM_HEADERS => array('os'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_DRILLDOWN_DIMENSION => self::DIMENSION_BROWSER,
			self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS => array('browser')
		),

		myReportsMgr::REPORT_TYPE_BROWSERS => array(
			self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_ALL,
			self::REPORT_DIMENSION => self::DIMENSION_BROWSER,
			self::REPORT_DETAIL_DIM_HEADERS => array('browser'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_LIVE => array(
			self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_HOUR,
			self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR), 
			self::REPORT_DIMENSION => self::DIMENSION_ENTRY_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'entry_name'),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_FIELD => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
		),

		myReportsMgr::REPORT_TYPE_TOP_PLAYBACK_CONTEXT => array(
			self::REPORT_DIMENSION => self::DIMENSION_PLAYBACK_CONTEXT,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'name'),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_FIELD => 'name', 
				self::REPORT_ENRICH_FUNC => 'self::getCategoriesNames'),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self:: METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_VPAAS_USAGE => array(
			self::REPORT_GRANULARITY => self::DRUID_GRANULARITY_MONTH,
			self::REPORT_DIMENSION => self::DIMENSION_PARTNER_ID,
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY),
			self::REPORT_LEGACY => array(
				self::REPORT_LEGACY_MAPPING => array('total_plays' => 'count_plays'),
				self::REPORT_LEGACY_JOIN_FIELD => 'month_id'),
		),

		myReportsMgr::REPORT_TYPE_ADMIN_CONSOLE => array(
			self::REPORT_DIMENSION => self::DIMENSION_PARTNER_ID,
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_LEGACY => array(
				self::REPORT_LEGACY_MAPPING => array('count loads' => 'count_loads',
				'count plays' => 'count_plays'),
			self::REPORT_LEGACY_JOIN_FIELD =>'id'),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
		),

		myReportsMgr::REPORT_TYPE_TOP_CONTRIBUTORS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION => self::DIMENSION_USER_ID,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'name'),
			self::REPORT_METRICS => array(self::METRIC_COUNT, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_COUNT, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_SHOW),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_COUNT, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array('entryCreated')),
		),
			
		myReportsMgr::REPORT_TYPE_CONTENT_CONTRIBUTIONS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION => self::DIMENSION_SOURCE_TYPE,
			self::REPORT_DETAIL_DIM_HEADERS => array('object_id', 'entry_media_source_name'),
			self::REPORT_METRICS => array(self::METRIC_COUNT, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_COUNT, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array('entryCreated')),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_SOURCE_TYPE,
		),
	);
	
	private static $event_type_count_aggrs = array(
		self::EVENT_TYPE_PLAY,
		self::EVENT_TYPE_PLAYER_IMPRESSION,
		self::EVENT_TYPE_PLAY_END,
		self::EVENT_TYPE_PLAYTHROUGH_25,
		self::EVENT_TYPE_PLAYTHROUGH_50,
		self::EVENT_TYPE_PLAYTHROUGH_75,
		self::EVENT_TYPE_PLAYTHROUGH_100,
		self::EVENT_TYPE_EDIT_CLICKED,
		self::EVENT_TYPE_SHARE_CLICKED,
		self::EVENT_TYPE_DOWNLOAD_CLICKED,
		self::EVENT_TYPE_REPORT_CLICKED
	);

	private static $media_type_count_aggrs = array(
		self::MEDIA_TYPE_VIDEO,
		self::MEDIA_TYPE_AUDIO,
		self::MEDIA_TYPE_IMAGE,
		self::MEDIA_TYPE_SHOW,
	);
	
	private static $playthrough_event_types = array(
		self::EVENT_TYPE_PLAYTHROUGH_25,
		self::EVENT_TYPE_PLAYTHROUGH_50,
		self::EVENT_TYPE_PLAYTHROUGH_75,
		self::EVENT_TYPE_PLAYTHROUGH_100
	);
	
	static $metrics_to_headers = array(
		self::DIMENSION_DEVICE => 'device',
		self::DIMENSION_OS => 'os',
		self::DIMENSION_BROWSER => 'browser',
		self::DIMENSION_LOCATION_COUNTRY => 'country',
		self::DIMENSION_LOCATION_REGION => 'location_name',
		self::DIMENSION_SOURCE_TYPE => 'entry_media_source_name',
		self::EVENT_TYPE_PLAY => 'count_plays',
		self::EVENT_TYPE_PLAYER_IMPRESSION => 'count_loads',
		self::EVENT_TYPE_PLAYTHROUGH_25 => 'count_plays_25',
		self::EVENT_TYPE_PLAYTHROUGH_50 => 'count_plays_50',
		self::EVENT_TYPE_PLAYTHROUGH_75 => 'count_plays_75',
		self::EVENT_TYPE_PLAYTHROUGH_100 => 'count_plays_100',
		self::EVENT_TYPE_REPORT_CLICKED => 'count_report',
		self::EVENT_TYPE_DOWNLOAD_CLICKED => 'count_download',
		self::EVENT_TYPE_SHARE_CLICKED => 'count_viral',
		self::EVENT_TYPE_EDIT_CLICKED => 'count_edit',
		self::MEDIA_TYPE_VIDEO => 'count_video',
		self::MEDIA_TYPE_AUDIO => 'count_audio',
		self::MEDIA_TYPE_IMAGE => 'count_image',
		self::MEDIA_TYPE_SHOW => 'count_mix',
		self::METRIC_TOTAL_PLAY_TIME => 'sum_time_viewed',
		self::METRIC_AVG_PLAY_TIME => 'avg_time_viewed',
		self::METRIC_PLAYER_IMPRESSION_RATIO => 'load_play_ratio',
		self::METRIC_AVG_DROP_OFF => 'avg_view_drop_off',
		self::METRIC_TOTAL_ENTRIES => 'unique_videos',
		self::METRIC_UNIQUE_USERS => 'unique_known_users',
		self::METRIC_PLAYTHROUGH_RATIO => 'play_through_ratio',
		self::METRIC_COUNT => 'count_total',
		self::METRIC_COUNT_UGC => 'count_ugc',
		self::METRIC_COUNT_ADMIN => 'count_admin',
	);
	
	static $transform_metrics = array(
		self::METRIC_TOTAL_ENTRIES => 'floor',
		self::METRIC_UNIQUE_USERS => 'floor',
		self::DIMENSION_DEVICE => array('kKavaReportsMgr', 'toSafeId'),
		self::DIMENSION_BROWSER => array('kKavaReportsMgr', 'transformBrowserName'),
		self::DIMENSION_OS => array('kKavaReportsMgr', 'transformOperatingSystemName'),
		self::DIMENSION_LOCATION_COUNTRY => array('kKavaCountryCodes', 'toShortName'),
		self::DIMENSION_LOCATION_REGION => 'strtoupper',
		self::DIMENSION_SOURCE_TYPE => array('kKavaReportsMgr', 'toSafeId'),
	);

	static $transform_time_dimensions = array(
		self::DRUID_GRANULARITY_HOUR => array('kKavaReportsMgr', 'timestampToHourId'),
		self::DRUID_GRANULARITY_DAY => array('kKavaReportsMgr', 'timestampToDateId'),
		self::DRUID_GRANULARITY_MONTH => array('kKavaReportsMgr', 'timestampToMonthId')
	);

	static $granularity_mapping = array(
		self::DRUID_GRANULARITY_DAY => 'P1D',
		self::DRUID_GRANULARITY_MONTH => 'P1M',
		self::DRUID_GRANULARITY_HOUR => 'PT1H',
	);

	static $non_linear_metrics = array(
		self::METRIC_AVG_PLAY_TIME => true,
		self::METRIC_PLAYER_IMPRESSION_RATIO => true,
		self::METRIC_PLAYTHROUGH_RATIO => true,
		self::METRIC_AVG_DROP_OFF => true,
		self::METRIC_TOTAL_ENTRIES => true,
		self::METRIC_UNIQUE_USERS => true,
	);

	static $multi_value_dimensions = array(
		self::DIMENSION_CATEGORIES
	);

	static $php_timezone_names = array(
		-840 => 'Pacific/Kiritimati',
		-780 => 'Pacific/Enderbury',
		-765 => 'Pacific/Chatham',
		-720 => 'Pacific/Auckland',
		-690 => 'Pacific/Norfolk',
		-660 => 'Asia/Anadyr',
		-630 => 'Australia/Lord_Howe',
		-600 => 'Australia/Melbourne',
		-570 => 'Australia/Adelaide',
		-540 => 'Asia/Tokyo',
		-525 => 'Australia/Eucla',
		-480 => 'Asia/Brunei',
		-420 => 'Asia/Krasnoyarsk',
		-390 => 'Asia/Rangoon',
		-360 => 'Asia/Almaty',
		-345 => 'Asia/Kathmandu',
		-330 => 'Asia/Colombo',
		-300 => 'Asia/Karachi',
		-270 => 'Asia/Kabul',
		-240 => 'Asia/Dubai',
		-210 => 'Asia/Tehran',
		-180 => 'Europe/Moscow',
		-120 => 'Europe/Helsinki',
		-60  => 'Europe/Paris',
		0    => 'Europe/London',
		60   => 'Atlantic/Azores',
		120  => 'America/Noronha',
		180  => 'America/Sao_Paulo',
		210  => 'America/St_Johns',
		240  => 'America/Halifax',
		270  => 'America/Caracas',
		300  => 'America/New_York',
		360  => 'America/Chicago',
		420  => 'America/Denver',
		480  => 'America/Los_Angeles',
		540  => 'America/Anchorage',
		570  => 'Pacific/Marquesas',
		600  => 'Pacific/Honolulu',
		660  => 'Pacific/Niue',
		720  => 'Pacific/Kwajalein',
	);

	// Note: while technically the druid list could have been the same as the php list,
	//		it seems to be better to use Etc/GMT... with Druid when possible -
	//		https://github.com/druid-io/druid/issues/5200
	
	static $druid_timezone_names = array(
		-840 => 'Etc/GMT-14',
		-780 => 'Etc/GMT-13',
		-765 => 'Pacific/Chatham',
		-720 => 'Etc/GMT-12',
		-690 => 'Pacific/Norfolk',
		-660 => 'Etc/GMT-11',
		-630 => 'Australia/Lord_Howe',
		-600 => 'Etc/GMT-10',
		-570 => 'Australia/Adelaide',
		-540 => 'Etc/GMT-9',
		-525 => 'Australia/Eucla',
		-480 => 'Etc/GMT-8',
		-420 => 'Etc/GMT-7',
		-390 => 'Asia/Rangoon',
		-360 => 'Etc/GMT-6',
		-345 => 'Asia/Kathmandu',
		-330 => 'Asia/Colombo',
		-300 => 'Etc/GMT-5',
		-270 => 'Asia/Kabul',
		-240 => 'Etc/GMT-4',
		-210 => 'Asia/Tehran',
		-180 => 'Etc/GMT-3',
		-120 => 'Etc/GMT-2',
		-60  => 'Etc/GMT-1',
		 0   => 'Etc/GMT',
		 60  => 'Etc/GMT+1',
		 120 => 'Etc/GMT+2',
		 180 => 'Etc/GMT+3',
		 210 => 'America/St_Johns',
		 240 => 'Etc/GMT+4',
		 270 => 'America/Caracas',
		 300 => 'Etc/GMT+5',
		 360 => 'Etc/GMT+6',
		 420 => 'Etc/GMT+7',
		 480 => 'Etc/GMT+8',
		 540 => 'Etc/GMT+9',
		 570 => 'Pacific/Marquesas',
		 600 => 'Etc/GMT+10',
		 660 => 'Etc/GMT+11',
		 720 => 'Etc/GMT+12',
	);
	
	static $aggregations_def = array();
	static $metrics_def = array();
	static $headers_to_metrics = array();
	static $custom_reports = null;
	
	protected static function toSafeId($name)
	{
		$name = strtoupper($name);
		$name = preg_replace('/[^\w]/', '_', $name);
		return $name;
	}

	protected static function fromSafeId($name)
	{
		$name = str_replace('_', ' ', $name);
		$name = ucfirst(strtolower($name));
		return $name;
	}

	protected static function transformBrowserName($name)
	{
		$name = str_replace(array('Internet Explorer', 'Microsoft Edge'), array('IE', 'Edge'), $name);
		$name = preg_replace('/(\w) (\d)/', '$1$2', $name);
		$name = strtoupper($name);
		$name = str_replace(array('(',')'), '', $name);
		$name = preg_replace('/[^\w]/', '_', $name);
		return $name;
	}

	protected static function transformOperatingSystemName($name)
	{
		$name = str_replace(array('Windows ', '.x'), array('Windows_', ''), $name);
		$name = preg_replace('/(\w) (\d)/', '$1$2', $name);
		$name = strtoupper($name);
		$name = str_replace(array('(',')'), '', $name);
		$name = preg_replace('/[^\w]/', '_', $name);
		return $name;
	}

	private static function getFieldRatioAggr($agg_name, $field1, $field2)
	{
		return self::getArithmeticPostAggregator($agg_name, '/', array(
			self::getFieldAccessPostAggregator($field1),
			self::getFieldAccessPostAggregator($field2)));
	}

	private static function init()
	{
		if (self::$metrics_def)
		{
			return;
		}
		
		// count aggregators
		self::$aggregations_def[self::METRIC_COUNT] = 
			self::getLongSumAggregator(self::METRIC_COUNT, self::METRIC_COUNT);
		
		self::$aggregations_def[self::METRIC_PLAYTHROUGH] = self::getFilteredAggregator(
			self::getInFilter(self::DIMENSION_EVENT_TYPE, self::$playthrough_event_types),
			self::getLongSumAggregator(self::METRIC_PLAYTHROUGH, self::METRIC_COUNT));
		
		foreach (self::$event_type_count_aggrs as $event_type)
		{
			self::$aggregations_def[$event_type] = self::getFilteredAggregator(
				self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, $event_type),
				self::getLongSumAggregator($event_type, self::METRIC_COUNT)); 
		}
		
		foreach (self::$media_type_count_aggrs as $media_type)
		{
			self::$aggregations_def[$media_type] = self::getFilteredAggregator(
				self::getSelectorFilter(self::DIMENSION_MEDIA_TYPE, $media_type),
				self::getLongSumAggregator($media_type, self::METRIC_COUNT)); 
		}

		$is_admin_metrics = array(
			self::METRIC_COUNT_UGC => '0', 
			self::METRIC_COUNT_ADMIN => '1');
		foreach ($is_admin_metrics as $metric => $value)
		{
			self::$aggregations_def[$metric] = self::getFilteredAggregator(
				self::getSelectorFilter(self::DIMENSION_USER_IS_ADMIN, $value),
				self::getLongSumAggregator($metric, self::METRIC_COUNT));
		}
		
		// other aggregators
		self::$aggregations_def[self::METRIC_AVG_PLAY_TIME] = self::getFieldRatioAggr(
			self::METRIC_AVG_PLAY_TIME, 
			self::METRIC_TOTAL_PLAY_TIME, 
			self::EVENT_TYPE_PLAY);
		
		self::$aggregations_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = self::getFieldRatioAggr(
			self::METRIC_PLAYER_IMPRESSION_RATIO, 
			self::EVENT_TYPE_PLAY, 
			self::EVENT_TYPE_PLAYER_IMPRESSION);
		
		self::$aggregations_def[self::METRIC_PLAYTHROUGH_RATIO] = self::getFieldRatioAggr(
			self::METRIC_PLAYTHROUGH_RATIO, 
			self::EVENT_TYPE_PLAYTHROUGH_100, 
			self::EVENT_TYPE_PLAY);

		self::$aggregations_def[self::METRIC_TOTAL_PLAY_TIME_SEC] = self::getFilteredAggregator(
			self::getInFilter(self::DIMENSION_EVENT_TYPE, self::$playthrough_event_types), 
			self::getLongSumAggregator(self::METRIC_TOTAL_PLAY_TIME_SEC, self::METRIC_TOTAL_PLAY_TIME_SEC));
		
		self::$aggregations_def[self::METRIC_TOTAL_ENTRIES] = self::getCardinalityAggregator(
			self::METRIC_TOTAL_ENTRIES, 
			array(self::DIMENSION_ENTRY_ID));
		
		self::$aggregations_def[self::METRIC_UNIQUE_USERS] = self::getHyperUniqueAggregator(
			self::METRIC_UNIQUE_USERS, 
			self::METRIC_UNIQUE_USER_IDS);

		self::$aggregations_def[self::METRIC_AVG_DROP_OFF] = self::getArithmeticPostAggregator(
			self::METRIC_AVG_DROP_OFF, '/', array(
				self::getArithmeticPostAggregator('subDropOff', '/', array(
					self::getFieldAccessPostAggregator(self::METRIC_PLAYTHROUGH),
					self::getConstantPostAggregator('quarter', '4'))), 
				self::getFieldAccessPostAggregator(self::EVENT_TYPE_PLAY)));
		
		self::$aggregations_def[self::METRIC_TOTAL_PLAY_TIME] = self::getArithmeticPostAggregator(
			self::METRIC_TOTAL_PLAY_TIME, '/', array(
				self::getFieldAccessPostAggregator(self::METRIC_TOTAL_PLAY_TIME_SEC),
				self::getConstantPostAggregator('seconds', '60')));

		// metrics
		self::$metrics_def[self::METRIC_TOTAL_PLAY_TIME] = array(
			self::DRUID_AGGR => array(self::METRIC_TOTAL_PLAY_TIME_SEC),
			self::DRUID_POST_AGGR => array(self::METRIC_TOTAL_PLAY_TIME));
		
		self::$metrics_def[self::METRIC_AVG_PLAY_TIME] = array(
			self::DRUID_AGGR => array(self::METRIC_TOTAL_PLAY_TIME_SEC, self::EVENT_TYPE_PLAY),
			self::DRUID_POST_AGGR => array(self::METRIC_TOTAL_PLAY_TIME, self::METRIC_AVG_PLAY_TIME));
		
		self::$metrics_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::DRUID_POST_AGGR => array(self::METRIC_PLAYER_IMPRESSION_RATIO));
		
		self::$metrics_def[self::METRIC_AVG_DROP_OFF] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::METRIC_PLAYTHROUGH),
			self::DRUID_POST_AGGR => array(self::METRIC_AVG_DROP_OFF));
		
		self::$metrics_def[self::METRIC_PLAYTHROUGH_RATIO] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_100),
			self::DRUID_POST_AGGR => array(self::METRIC_PLAYTHROUGH_RATIO));
		
		foreach (self::$metrics_to_headers as $metric => $header)
		{
			self::$headers_to_metrics[$header] = $metric;
		}
	}

	public static function getGraph($partner_id, $report_type, reportsInputFilter $input_filter, $dimension = null, $object_ids = null)
	{
		self::init();

		$start = microtime(true);
		$report_def = self::getReportDef($report_type);
		if (!isset($report_def[self::REPORT_GRAPH_METRICS]))
		{
			throw new Exception('unsupported query - report has no metrics');
		}
		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$metrics = $report_def[self::REPORT_GRAPH_METRICS];
		$intervals = self::getFilterIntervals($report_type, $input_filter);
		$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, $object_ids);

		if ($object_ids && array_key_exists(self::REPORT_DRILLDOWN_GRANULARITY, $report_def))
			$granularity = $report_def[self::REPORT_DRILLDOWN_GRANULARITY];
		else if (array_key_exists(self::REPORT_GRANULARITY, $report_def))
			$granularity = $report_def[self::REPORT_GRANULARITY];
		else
			$granularity = self::DRUID_GRANULARITY_DAY;

		$granularity_def = self::getGranularityDef($granularity, $input_filter->timeZoneOffset);

		switch ($report_type)
		{
			case myReportsMgr::REPORT_TYPE_PLATFORMS:
			case myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM:
			case myReportsMgr::REPORT_TYPE_BROWSERS:
				$dimension = self::getDimension($report_def, $object_ids);
				$query = self::getGroupByReport($data_source, $partner_id, $intervals, $granularity_def, array($dimension), $metrics, $druid_filter);
				break;
			default:
				$query = self::getTimeSeriesReport($data_source, $partner_id, $intervals, $granularity_def, $metrics, $druid_filter);

		}

		$result = self::runQuery($query);
		KalturaLog::log('Druid returned [' . count($result) . '] rows');

		foreach ($metrics as $column)
		{
			$graph_metrics_to_headers[$column] = self::$metrics_to_headers[$column];
		}
		switch ($report_type)
		{
			case myReportsMgr::REPORT_TYPE_PLATFORMS:
				if ($object_ids != NULL && count($object_ids) > 0)
					$res = self::getMultiGraphsByColumnName($result, $graph_metrics_to_headers, self::DIMENSION_OS, self::$transform_metrics[self::DIMENSION_OS]);
				else
					$res = self::getMultiGraphsByDateId($result, self::DIMENSION_DEVICE, $graph_metrics_to_headers, self::$transform_metrics[self::DIMENSION_DEVICE], $input_filter->timeZoneOffset);
				break;
			case myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM:
			case myReportsMgr::REPORT_TYPE_BROWSERS:
				$dimension = $report_def[self::REPORT_DIMENSION];
				$res = self::getMultiGraphsByColumnName($result, $graph_metrics_to_headers, $dimension, self::$transform_metrics[$dimension]);
				break;
			case myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF:
			case myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF:
				$res = self::getGraphsByColumnName($result, $graph_metrics_to_headers, myReportsMgr::$type_map[$report_type]);
				break;
			default:
				$res = self::getGraphsByDateId($result, $graph_metrics_to_headers, $input_filter->timeZoneOffset, self::$transform_time_dimensions[$granularity]);
		}

		$end = microtime(true);
		KalturaLog::log('getGraph took [' . ($end - $start) . ']');

		return $res;
	}

	public static function getTotal($partner_id, $report_type, reportsInputFilter $input_filter, $object_ids = null)
	{
		self::init();

		$start = microtime(true);
		$report_def = self::getReportDef($report_type);
		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$intervals = self::getFilterIntervals($report_type, $input_filter);
		$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, $object_ids);
		if (array_key_exists(self::REPORT_TOTAL_METRICS, $report_def))
		{
			$metrics = $report_def[self::REPORT_TOTAL_METRICS];
		}
		else 
		{
			$metrics = self::getMetrics($report_def);
			if (!$metrics)
			{
				throw new Exception('unsupported query - report has no metrics');
			}
		}

		$granularity = self::DRUID_GRANULARITY_ALL;
		$query = self::getTimeSeriesReport($data_source, $partner_id, $intervals, $granularity, $metrics, $druid_filter);
		$result = self::runQuery($query);
		if (count($result) > 0)
		{
			$row = $result[0];
			$row_data = $row[self::DRUID_RESULT];

			foreach ($metrics as $column)
			{
				$headers[] = self::$metrics_to_headers[$column];
				$data[] = $row_data[$column];
			}

			foreach (self::$transform_metrics as $metric => $func)
			{
				$field_index = array_search(self::$metrics_to_headers[$metric], $headers);
				if (false !== $field_index)
				{
					$data[$field_index] = call_user_func($func, $data[$field_index]);
				}
			}
		}
		else
		{
			foreach ($metrics as $column)
			{
				$headers[] = self::$metrics_to_headers[$column];
				$data[] = '';
			}
		}

		$res = array($headers, $data);
		$end = microtime(true);
		KalturaLog::log('getTotal took ['  . ($end - $start) . ']');

		return $res;
	}

	private static function graphToTable($graphs)
	{
		// get the union of all dates
		$dates = array();
		foreach ($graphs as $graph)
		{
			foreach ($graph as $date => $value)
			{
				$dates[$date] = true;
			}
		}
		ksort($dates);

		// build the table
		$header = array_keys($graphs);
		$data = array();
		foreach (array_keys($dates) as $date)
		{
			$row = array($date);
			foreach ($header as $column)
			{
				$row[] = isset($graphs[$column][$date]) ? $graphs[$column][$date] : 0;
			}
			$data[] = $row;
		}

		return array(array_merge(array('date_id'), $header), $data);
	}

	public static function customReport($id, $params)
	{
		self::$custom_reports = kConf::getMap('custom_reports');
		$report_def = self::$custom_reports[$id];

		// build the filter
		$input_filter = new reportsInputFilter();
		foreach ($report_def['filter'] as $field => $value)
		{
			if (is_string($value) && $value[0] == ':')
			{
				$paramName = substr($value, 1);
				if (!isset($params[$paramName]))
				{
					throw new Exception("missing parameter $paramName");
				}
				$value = $params[$paramName];
			}
			$input_filter->$field = $value;
		}

		$partner_id = $params['partner_id'];
		$report_type = -$id;		// negative report types are used to identify custom reports

		if (isset($report_def[self::REPORT_GRAPH_METRICS]))
		{
			// graph report
			$graphs = self::getGraph($partner_id, $report_type, $input_filter);
			list($header, $data) = self::graphToTable($graphs);
		}
		else if (isset($report_def[self::REPORT_DIMENSION]))
		{
			// table report
			list($header, $data, $totalCount) = self::getTable(
				$partner_id,
				$report_type,
				$input_filter,
				self::MAX_CUSTOM_REPORT_RESULT_SIZE,
				1,
				$report_def['order_by'],
				null,
				true);
		}
		else
		{
			// total report
			list($header, $data) = self::getTotal(
				$partner_id,
				$report_type,
				$input_filter,
				null);
		}

		if (isset($report_def['header']))
		{
			$header = explode(',', $report_def['header']);
		}

		return array($header, $data);
	}

	private static function getReportDef($report_type)
	{
		if ($report_type >= 0)
		{
			return self::$reports_def[$report_type];
		}
		else
		{
			return self::$custom_reports[-$report_type];
		}
	}
	
	protected static function getEnrichDefs($report_def)
	{
		if (!isset($report_def[self::REPORT_ENRICH_DEF]))
		{
			return array();
		}
		
		$result = $report_def[self::REPORT_ENRICH_DEF];
		if (isset($result[self::REPORT_ENRICH_FIELD]))
		{
			$result = array($result);
		}
		return $result;
	}
	
	protected static function getEnrichedFields($report_def)
	{		
		$result = array();
		$enrich_defs = self::getEnrichDefs($report_def);
		foreach ($enrich_defs as $enrich_def)
		{
			$cur_fields = $enrich_def[self::REPORT_ENRICH_FIELD];
			if (is_array($cur_fields))
			{
				$result = array_merge($result, $cur_fields);
			}
			else
			{
				$result[] = $cur_fields;
			}
		}
		
		return $result;
	}
	
	protected static function enrichData($report_def, $headers, $partner_id, &$data)
	{
		// get the enrichment specification
		$enrich_specs = array();
		$enrich_defs = self::getEnrichDefs($report_def);
		foreach ($enrich_defs as $enrich_def)
		{
			$enrich_func = $enrich_def[self::REPORT_ENRICH_FUNC];
			$enrich_context = isset($enrich_def[self::REPORT_ENRICH_CONTEXT]) ? $enrich_def[self::REPORT_ENRICH_CONTEXT] : null;
			$cur_fields = $enrich_def[self::REPORT_ENRICH_FIELD];
			if (!is_array($cur_fields))
			{
				$cur_fields = array($cur_fields);
			}
		
			$enriched_indexes = array();
			foreach ($cur_fields as $field)
			{
				$enriched_indexes[] = array_search($field, $headers);
			}
		
			$enrich_specs[] = array($enrich_func, $enrich_context, $enriched_indexes);
		}
		
		// enrich the data in chunks
		$rows_count = count($data);
		$start = 0;
		while ($start < $rows_count)
		{
			$limit = min($start + self::ENRICH_CHUNK_SIZE, $rows_count);
			$dimension_ids = array_map('reset', array_slice($data, $start, $limit - $start));
		
			foreach ($enrich_specs as $enrich_spec)
			{
				list($enrich_func, $enrich_context, $enriched_indexes) = $enrich_spec;
					
				if (is_array($report_def[self::REPORT_DIMENSION]))
				{
					$dimension_ids = array_unique($dimension_ids);
				}

				$entities = call_user_func($enrich_func, $dimension_ids, $partner_id, $enrich_context);
		
				for ($current_row = $start; $current_row < $limit; $current_row++) 
				{
					$entity = $entities[reset($data[$current_row])];
					foreach ($enriched_indexes as $index => $enrich_field) 
					{
						$data[$current_row][$enrich_field] = is_array($entity) ? $entity[$index] : $entity;
					}
				}
			}
			
			$start = $limit;
		}
	}

	public static function getTable($partner_id, $report_type, reportsInputFilter $input_filter,
		$page_size, $page_index, $order_by, $object_ids = null, $isCsv = false)
	{
		self::init();
		$start = microtime (true);
		$total_count = null;

		$report_def = self::getReportDef($report_type);
		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$intervals = self::getFilterIntervals($report_type, $input_filter);
		$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, $object_ids);
		$dimension = self::getDimension($report_def, $object_ids);
		$metrics = self::getMetrics($report_def);

		if (array_key_exists(self::REPORT_LEGACY, $report_def))
		{
			//get the report from the legacy dwh
			list($headers, $data, $total_count) = myReportsMgr::getTable($partner_id, $report_type, $input_filter, $page_size, $page_index, $order_by, $object_ids, null);

			switch ($report_type)
			{
				case myReportsMgr::REPORT_TYPE_VPAAS_USAGE:
					if ($object_ids)
						throw new Exception('objectIds filter is not supported for this report');

					$data = self::getVpaasUsageReport($report_def, $data, $headers, $partner_id, $intervals, $metrics, $druid_filter, $input_filter->timeZoneOffset);
					break;
				case myReportsMgr::REPORT_TYPE_ADMIN_CONSOLE:
					$data = self::getAdminConsoleReport($report_def, $data, $headers, $partner_id, $intervals, $metrics, $dimension, $druid_filter, $object_ids);
					break;
			}
			$res = array($headers, $data, $total_count);
			return $res;
		}

		if (!$metrics)
		{
			$query = self::getSearchReport($data_source, $partner_id, $intervals, array($dimension), $druid_filter);
			$result = self::runQuery($query);

			$data = array();
			if ($result)
			{
				$rows = $result[0][self::DRUID_RESULT];
				KalturaLog::log('Druid returned [' . count($rows) . '] rows');
				foreach ($rows as $row)
				{
					$data[] = array($row[self::DRUID_VALUE]);
				}
			}
			return array($report_def[self::REPORT_DETAIL_DIM_HEADERS], $data, count($data));
		}

		// pager
		if (!$page_size || $page_size < 0)
			$page_size = 10;

		if (!$page_index || $page_index < 0)
			$page_index = 1;

		// Note: max size is already validated externally when $isCsv is true
		$max_result_size = $isCsv ? PHP_INT_MAX : self::MAX_RESULT_SIZE;
		if ($page_index * $page_size > $max_result_size)
		{
			if ($page_index == 1 && !$isCsv)
			{
				$page_size = $max_result_size;
			}
			else
			{
				throw new Exception('result limit is ' . $max_result_size. ' rows');
			}
		}

		// order by
		if (in_array(self::EVENT_TYPE_PLAY, $metrics))
		{
			$default_order = self::EVENT_TYPE_PLAY;
		}
		else
		{
			$default_order = reset($metrics);
		}
		$order_by_dir = '-';
		if (!$order_by)
		{
			$order_by = $default_order;
		}
		else
		{
			if ($order_by[0] === '-' || $order_by[0] === '+')
			{
				$order_by_dir = $order_by[0];
				$order_by = substr($order_by, 1);
			}

			if (isset(self::$headers_to_metrics[$order_by]))
				$order_by = self::$headers_to_metrics[$order_by];
			else
				$order_by = $default_order;

			if (!in_array($order_by, $metrics))
			{
				$order_by = $default_order;
			}
		}

		// Note: using a larger threshold since topN is approximate
		$intervalDays = intval((self::dateIdToUnixtime($input_filter->to_day) - self::dateIdToUnixtime($input_filter->from_day)) / 86400) + 1;
		$threshold = max($intervalDays * 30, $page_size * $page_index * 2);		// 30 ~ 10000 / 365, i.e. an interval of 1Y gets a minimum threshold of 10000
		$threshold = max(self::MIN_THRESHOLD, min($max_result_size, $threshold));

		if (isset(self::$non_linear_metrics[$order_by]) ||
			is_array($dimension))
		{
			// topN works badly for non-linear metrics like avg drop off, since taking the topN per segment
			// does not necessarily yield the combined topN
			$threshold = $page_size * $page_index;

			$query = self::getGroupByReport(
				$data_source,
				$partner_id,
				$intervals,
				self::DRUID_GRANULARITY_ALL,
				is_array($dimension) ? $dimension : array($dimension),
				$metrics,
				$druid_filter);
			$query[self::DRUID_LIMIT_SPEC] = self::getDefaultLimitSpec(
					$threshold,
					array(self::getOrderByColumnSpec(
							$order_by,
							$order_by_dir == '+' ? self::DRUID_ASCENDING : self::DRUID_DESCENDING,
							self::DRUID_NUMERIC)
					));
		}
		else if (!$object_ids &&
			!in_array($dimension, array(self::DIMENSION_LOCATION_COUNTRY, self::DIMENSION_DOMAIN, self::DIMENSION_DEVICE)) &&
			!self::getFilterValues($druid_filter, $dimension) &&
			!$isCsv)
		{
			// get the topN objects first, otherwise the returned metrics can be inaccurate
			$query = self::getTopReport($data_source, $partner_id, $intervals, array($order_by), $dimension, $druid_filter, $order_by, $order_by_dir, $threshold, $metrics);
			$result = self::runQuery($query);
			if (!$result)
			{
				return array(array(), array(), 0);
			}

			$rows = $result[0][self::DRUID_RESULT];
			$rows_count = count($rows);
			KalturaLog::log("Druid returned [$rows_count] rows");

			$rows = array_slice($rows, ($page_index - 1) * $page_size, $page_size);
			if (!$rows)
			{
				return array(array(), array(), $rows_count);
			}

			if ($threshold > $rows_count)
			{
				$total_count = $rows_count;
			}
			else if ((!($input_filter instanceof endUserReportsInputFilter)) || in_array($report_type, myReportsMgr::$end_user_filter_get_count_reports))
			{
				$total_count = self::getTotalTableCount($partner_id, $report_type, $input_filter, $intervals, $druid_filter, $dimension, $object_ids);
			}

			$dimension_ids = array();
			foreach ($rows as $row)
			{
				$dimension_ids[] = $row[$dimension];
			}

			// issue a second topN query
			$page_index = 1;
			$page_size = count($dimension_ids);
			$threshold = $page_size;

			if ($dimension == self::DIMENSION_ENTRY_ID)
			{
				// when the dimension is entry id, we can use a more minimal filter that does not
				// contain the dimensions dependent on entry id - categories + playback type
				// the partner id filter is retained since otherwise sending a bogus event
				// with non-matching entryId + partnerId will open access to the entry
				$druid_filter = array(
					array(self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID,
						self::DRUID_VALUES => array($partner_id)
					),
				);
				self::addEndUserReportsDruidFilters($partner_id, $input_filter, $druid_filter);
			}
			else
			{
				// Note: not passing $dimension_ids as $object_ids since in some reports $object_ids
				//		filters by entries, and not by $dimension
				$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, null);
			}

			$druid_filter[] = array(
				self::DRUID_DIMENSION => $dimension,
				self::DRUID_VALUES => $dimension_ids
			);

			$query = self::getTopReport($data_source, $partner_id, $intervals, $metrics, $dimension, $druid_filter, $order_by, $order_by_dir, $threshold);
		}
		else
		{
			$query = self::getTopReport($data_source, $partner_id, $intervals, $metrics, $dimension, $druid_filter, $order_by, $order_by_dir, $threshold);
		}

		if ($isCsv && isset($query[self::DRUID_CONTEXT][self::DRUID_PRIORITY]))
		{
			$query[self::DRUID_CONTEXT][self::DRUID_PRIORITY] = 0;
		}

		$result = self::runQuery($query);
		if (!$result)
		{
			return array(array(), array(), 0);
		}

		if ($query[self::DRUID_QUERY_TYPE] == self::DRUID_GROUP_BY)
		{
			$rows = $result;
		}
		else
		{
			$rows = $result[0][self::DRUID_RESULT];
		}

		// set to null to free memory as we dont need this var anymore
		$result = null;

		$rows_count = count($rows);
		KalturaLog::log("Druid returned [$rows_count] rows");

		$rows = array_slice($rows, ($page_index - 1) * $page_size, $page_size);
		if (!$rows)
		{
			return array(array(), array(), $rows_count);
		}

		if (is_null($total_count))
		{
			if ($threshold > $rows_count)
			{
				$total_count = $rows_count;
			}
			else if ((!($input_filter instanceof endUserReportsInputFilter)) || in_array($report_type, myReportsMgr::$end_user_filter_get_count_reports) || $isCsv)
			{
				$total_count = self::getTotalTableCount($partner_id, $report_type, $input_filter, $intervals, $druid_filter, $dimension, $object_ids);

				if ($total_count <= 0)
				{
					$end = microtime(true);
					KalturaLog::log('getTable took [' . ($end - $start) . ']');
					return array(array(), array(), 0);
				}
			}
			else
			{
				$total_count = 0;
			}
		}

		$dimension_headers = $report_def[self::REPORT_DETAIL_DIM_HEADERS];
		$dimension = $report_def[self::REPORT_DIMENSION];
		if ($object_ids)
		{
			if (array_key_exists(self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS, $report_def))
				$dimension_headers = $report_def[self::REPORT_DRILLDOWN_DETAIL_DIM_HEADERS];
			if (array_key_exists(self::REPORT_DRILLDOWN_DIMENSION, $report_def))
				$dimension = $report_def[self::REPORT_DRILLDOWN_DIMENSION];
		}

		$headers = $dimension_headers;

		foreach ($metrics as $column)
		{
			$headers[] = self::$metrics_to_headers[$column];
		}

		// build the row mapping
		$enriched_fields = self::getEnrichedFields($report_def); 

		if (!is_array($dimension))
		{
			$dimension = array($dimension);
		}

		$first_dim = reset($dimension);
		$row_mapping = array();
		foreach ($dimension_headers as $dim_header)
		{
			if (in_array($dim_header, $enriched_fields))
			{
				$row_mapping[] = $first_dim;		// a placeholder that will be replaced during enrichment
			}
			else
			{
				$current_dim = current($dimension);
				$row_mapping[] = $current_dim ? $current_dim : $first_dim;
				next($dimension);
			}
		}

		foreach ($metrics as $column)
		{
			$row_mapping[] = $column;
		}

		// map the rows
		foreach ($rows as $index => $row)
		{
			if ($query[self::DRUID_QUERY_TYPE] == self::DRUID_GROUP_BY)
			{
				$row = $row[self::DRUID_EVENT];
			}

			$row_data = array();
			foreach ($row_mapping as $column)
			{
				$row_data[] = $column ? $row[$column] : null;
			}
			$rows[$index] = $row_data;
		}
		$data = $rows;

		// set to null to free memory as we dont need this var anymore
		$rows = null;

		foreach (self::$transform_metrics as $metric => $func)
		{
			$field_index = array_search(self::$metrics_to_headers[$metric], $headers);
			if (false !== $field_index)
			{
				$rows_count = count($data);
				for ($i = 0; $i < $rows_count; $i++)
				{
					$data[$i][$field_index] = call_user_func($func, $data[$i][$field_index]);
				}
			}
		}

		if ($enriched_fields)
		{
			self::enrichData($report_def, $headers, $partner_id, $data);
		}

		$res = array ($headers, $data, $total_count);

		$end = microtime(true);
		KalturaLog::log('getTable took [' . ($end - $start) . ']');

		return $res;
	}

	private static function fixTimeZoneOffset($timezone_offset)
	{
		if (isset(self::$php_timezone_names[$timezone_offset]))
		{
			return $timezone_offset;
		}

		$timezone_offset = min(max($timezone_offset, -14 * 60), 12 * 60);
		return round($timezone_offset / 60) * 60;
	}

	private static function getPhpTimezoneName($timezone_offset)
	{
		// Note: value must be set, since the offset already went through fixTimeZoneOffset
		return self::$php_timezone_names[$timezone_offset];
	}

	private static function getPhpTimezone($timezone_offset)
	{
		$tz_name = self::getPhpTimezoneName($timezone_offset);
		return new DateTimeZone($tz_name);
	}
	
	private static function getDruidTimezoneName($timezone_offset)
	{
		// Note: value must be set, since the offset already went through fixTimeZoneOffset
		return self::$druid_timezone_names[$timezone_offset];
	}
	
	private static function getGranularityDef($granularity, $timezone_offset)
	{
		if (!isset(self::$granularity_mapping[$granularity]))
		{
			return self::DRUID_GRANULARITY_ALL;
		}

		$granularity_def = array(self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
			self::DRUID_GRANULARITY_PERIOD => self::$granularity_mapping[$granularity],
			self::DRUID_TIMEZONE => self::getDruidTimezoneName($timezone_offset)
		);
		return $granularity_def;
	}

	private static function getFilterIntervals($report_type, $input_filter)
	{
		if ($report_type == myReportsMgr::REPORT_TYPE_APPLICATIONS)
		{
			// the applications report does not depend on from_day/to_day, use the last 30 days
			$input_filter->from_day = date('Ymd', time() - 30 * 86400);
			$input_filter->to_day = date('Ymd', time());
			$input_filter->timeZoneOffset = 0;
		}

		$input_filter->timeZoneOffset = self::fixTimeZoneOffset($input_filter->timeZoneOffset);
		$fromDate = self::dateIdToInterval($input_filter->from_day, $input_filter->timeZoneOffset);
		$toDate = self::dateIdToInterval($input_filter->to_day, $input_filter->timeZoneOffset, true);
		if (!$fromDate || !$toDate || strcmp($toDate, $fromDate) < 0)
		{
			$fromDate = $toDate = '2010-01-01T00:00:00+00:00';
		}
		$intervals = array($fromDate . '/' . $toDate);
		return $intervals;
	}

	private static function getDimension($report_def, $object_ids)
	{
		if ($object_ids && array_key_exists(self::REPORT_DRILLDOWN_DIMENSION, $report_def))
			return $report_def[self::REPORT_DRILLDOWN_DIMENSION];

		return $report_def[self::REPORT_DIMENSION];
	}

	private static function getMetrics($report_def)
	{
		return $report_def[self::REPORT_METRICS];
	}

	private static function isDateIdValid($date_id)
	{
		return strlen($date_id) >= 8 && preg_match('/^\d+$/', substr($date_id, 0, 8));
	}

	private static function dateIdToInterval($date_id, $offset, $end_of_the_day = false)
	{
		if (!self::isDateIdValid($date_id))
		{
			return null;
		}

		$year = substr($date_id, 0, 4);
		$month = substr($date_id, 4, 2);
		$day = substr($date_id, 6, 2);

		$timezone_offset = sprintf('%s%02d:%02d', $offset <= 0 ? '+' : '-', intval(abs($offset)/60), abs($offset) % 60);
		$time = $end_of_the_day? 'T23:59:59' : 'T00:00:00';

		return "$year-$month-$day$time$timezone_offset";
	}

	private static function dateIdToUnixtime($date_id)
	{
		if (!self::isDateIdValid($date_id))
		{
			return null;
		}

		$year = substr($date_id, 0, 4);
		$month = substr($date_id, 4, 2);
		$day = substr($date_id, 6, 2);
		return gmmktime(0, 0, 0, $month, $day, $year);
	}

	private static function timestampToDateId($timestamp, $tz)
	{
		$date = new DateTime($timestamp);
		$date->modify('12 hour');			// adding 12H in order to round to the nearest day
		$date->setTimezone($tz);
		return $date->format('Ymd');
	}

	private static function timestampToMonthId($timestamp, $tz)
	{
		$date = new DateTime($timestamp);
		$date->modify('12 hour');			// adding 12H in order to round to the nearest day
		$date->setTimezone($tz);
		return $date->format('Ym');
	}

	// hours are returned from druid query with the right offset so no need to change it
	private static function timestampToHourId($timestamp, $tz)
	{
		$date = new DateTime($timestamp);
		return $date->format('YmdH');
	}

	private static function addEndUserReportsDruidFilters($partner_id, $input_filter, &$druid_filter)
	{
		if (!($input_filter instanceof endUserReportsInputFilter))
		{
			return;
		}

		if ($input_filter->playbackContext || $input_filter->ancestorPlaybackContext)
		{
			if ($input_filter->playbackContext && $input_filter->ancestorPlaybackContext)
			{
				$category_ids = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
			}
			else
			{
				$category_ids = self::getPlaybackContextCategoriesIds($partner_id, $input_filter->playbackContext ?
					$input_filter->playbackContext : $input_filter->ancestorPlaybackContext, isset($input_filter->ancestorPlaybackContext));
			}

			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_CONTEXT,
				self::DRUID_VALUES => $category_ids);
		}

		if ($input_filter->application)
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_APPLICATION,
				self::DRUID_VALUES => explode(',', $input_filter->application)
			);
		}

		if ($input_filter->userIds != null)
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_USER_ID,
				self::DRUID_VALUES => explode(',', $input_filter->userIds)
			);
		}
	}

	private static function getDruidFilter($partner_id, $report_def, $input_filter, $object_ids)
	{
		$druid_filter = array();
		if (!isset($report_def[self::REPORT_DATA_SOURCE]))
		{
			$playback_types = isset($report_def[self::REPORT_PLAYBACK_TYPES]) ? $report_def[self::REPORT_PLAYBACK_TYPES] : array(self::PLAYBACK_TYPE_VOD);
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_TYPE,
				self::DRUID_VALUES => $playback_types
			);
		}
		
		if (isset($report_def[self::REPORT_FILTER]))
		{
			$druid_filter[] = $report_def[self::REPORT_FILTER];
		}
		
		self::addEndUserReportsDruidFilters($partner_id, $input_filter, $druid_filter);

		if ($input_filter->categories)
		{
			$category_ids = self::getCategoriesIds($input_filter->categories, $partner_id);
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_CATEGORIES,
				self::DRUID_VALUES => $category_ids
			);
		}

		if ($input_filter->categoriesIds)
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_CATEGORIES,
				self::DRUID_VALUES => explode(',', $input_filter->categoriesIds)
			);
		}

		if ($input_filter->countries)
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_LOCATION_COUNTRY,
				self::DRUID_VALUES => explode(',', $input_filter->countries)
			);
		}

		$entry_ids_from_db = array();
		if ($input_filter->keywords)
		{
			$entry_filter = new entryFilter();
			$entry_filter->setPartnerSearchScope($partner_id);

			if($input_filter->search_in_tags)
				$entry_filter->set('_free_text', $input_filter->keywords);
			else
				$entry_filter->set('_like_admin_tags', $input_filter->keywords);

			$c = KalturaCriteria::create(entryPeer::OM_CLASS);
			$entry_filter->attachToCriteria($c);
			$c->applyFilters();

			$entry_ids_from_db = $c->getFetchedIds();

			if ($c->getRecordsCount() > count($entry_ids_from_db))
				throw new kCoreException('Search is to general', kCoreException::SEARCH_TOO_GENERAL);

			if (!count($entry_ids_from_db))
				$entry_ids_from_db[] = entry::ENTRY_ID_THAT_DOES_NOT_EXIST;

		}

		if($object_ids)
		{
			$object_ids_arr = explode(',', $object_ids);

			if (isset($report_def[self::REPORT_OBJECT_IDS_TRANSFORM]))
			{
				$object_ids_arr = array_map($report_def[self::REPORT_OBJECT_IDS_TRANSFORM], $object_ids_arr);
			}
			
			if (isset($report_def[self::REPORT_FILTER_DIMENSION]))
			{
				$druid_filter[] = array(
					self::DRUID_DIMENSION => $report_def[self::REPORT_FILTER_DIMENSION],
					self::DRUID_VALUES => $object_ids_arr
				);
			}
			else
			{
				$entry_ids_from_db = array_merge($object_ids_arr, $entry_ids_from_db);
			}
		}

		if (count($entry_ids_from_db))
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_ENTRY_ID,
				self::DRUID_VALUES => $entry_ids_from_db
			);
		}

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_PARTNER_ID,
				self::DRUID_VALUES => array($partner_id)
			);
		}

		return $druid_filter;
	}

	private static function getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, $granularity, $filterMetrics = null)
	{
		$client_tag = kCurrentContext::$client_lang;

		if (kConf::hasParam('kava_top_priority_client_tags'))
		{
			$priority_tags = kConf::get('kava_top_priority_client_tags');
			foreach ($priority_tags as $tag)
			{
				if (strpos($client_tag, $tag) === 0)
				{
					$report_def[self::DRUID_CONTEXT] = array(self::DRUID_PRIORITY => self::CLIENT_TAG_PRIORITY);
					break;
				}
			}
		}

		$report_def[self::DRUID_DATASOURCE] = $data_source ? $data_source : self::DATASOURCE_HISTORICAL;
		$report_def[self::DRUID_INTERVALS] = $intervals;
		$report_def[self::DRUID_GRANULARITY] = $granularity;

		// aggregations
		$report_def[self::DRUID_AGGR] = array();
		$report_def[self::DRUID_POST_AGGR] = array();
		foreach ($metrics as $metric)
		{
			if (array_key_exists($metric, self::$metrics_def))
			{
				$metric_aggr = self::$metrics_def[$metric];
			}
			else
			{
				$metric_aggr = array(self::DRUID_AGGR => array($metric));
			}
			
			foreach ($metric_aggr[self::DRUID_AGGR] as $aggr)
			{
				if (in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_AGGR]))
					continue;

				$report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
			}
			
			if (array_key_exists(self::DRUID_POST_AGGR, $metric_aggr))
			{
				foreach ($metric_aggr[self::DRUID_POST_AGGR] as $aggr)
				{
					if (in_array(self::$aggregations_def[$aggr], $report_def[self::DRUID_POST_AGGR]))
						continue;
					$report_def[self::DRUID_POST_AGGR][] = self::$aggregations_def[$aggr];
				}
			}
		}

		// event types
		$event_types = array();
		if (!$filterMetrics)
		{
			$filterMetrics = $metrics;
		}
		foreach ($filterMetrics as $metric)
		{
			if (array_key_exists($metric, self::$metrics_def))
			{
		 		$aggrs = self::$metrics_def[$metric][self::DRUID_AGGR];
			}
			else
			{
				$aggrs = array($metric);
			}
			
		 	foreach ($aggrs as $aggr)
		 	{
		 		if (!isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_DIMENSION]) ||
		 			self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_DIMENSION] != self::DIMENSION_EVENT_TYPE)
		 		{
		 			continue;
		 		}
		 		
				if (isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUE]))
					$event_types[] = self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUE];
				else if (isset(self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUES]))
					$event_types = array_merge($event_types, self::$aggregations_def[$aggr][self::DRUID_FILTER][self::DRUID_VALUES]);
		 	}
		}

		if (count($event_types))
			$filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array_values(array_unique($event_types)));

		// filter
		$filter_def = self::buildFilter($filter);
		$report_def[self::DRUID_FILTER] = array(self::DRUID_TYPE => 'and',
			self::DRUID_FIELDS => $filter_def);

		return $report_def;
	}

	private static function getFilterValues($filter, $dimension)
	{
		foreach ($filter as $cur_filter)
		{
			if ($cur_filter[self::DRUID_DIMENSION] == $dimension)
			{
				return $cur_filter[self::DRUID_VALUES];
			}
		}

		return null;
	}

	private static function getTopReport($data_source, $partner_id, $intervals, $metrics, $dimensions, $filter, $order_by, $order_dir, $threshold, $filterMetrics = null)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, self::DRUID_GRANULARITY_ALL, $filterMetrics);

		if (in_array($dimensions, self::$multi_value_dimensions))
		{
			$values = self::getFilterValues($filter, $dimensions);
			if ($values)
			{
				// use a list filtered dimension, otherwise we may get values that don't match the filter
				$dimensions = array(
					self::DRUID_TYPE => self::DRUID_LIST_FILTERED,
					self::DRUID_DELEGATE => array(
							self::DRUID_TYPE => self::DRUID_DEFAULT,
							self::DRUID_DIMENSION => $dimensions,
							self::DRUID_OUTPUT_NAME => $dimensions,
					),
					self::DRUID_VALUES => $values,
				);
			}
		}

		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TOPN;
		$report_def[self::DRUID_DIMENSION] = $dimensions;
		$order_type = $order_dir === '+' ? self::DRUID_INVERTED : self::DRUID_NUMERIC;
		$report_def[self::DRUID_METRIC] = array(self::DRUID_TYPE => $order_type,
												self::DRUID_METRIC => $order_by);
		$report_def[self::DRUID_THRESHOLD] = $threshold;

		return $report_def;
	}

	private static function getSearchReport($data_source, $partner_id, $intervals, $dimensions, $filter)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, array(), $filter, self::DRUID_GRANULARITY_ALL);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_SEARCH;
		$report_def[self::DRUID_SEARCH_DIMENSIONS] = $dimensions;
		$report_def[self::DRUID_QUERY] = array(
			self::DRUID_TYPE => self::DRUID_CONTAINS,
			self::DRUID_CASE_SENSITIVE => true,
			self::DRUID_VALUE => ''
		);

		return $report_def;
	}

	private static function buildFilter($filters)
	{
		$filters_def = array();
		foreach ($filters as $filter)
		{
			if (count($filter[self::DRUID_VALUES]) == 1)
				$filters_def[] = array(self::DRUID_TYPE => self::DRUID_SELECTOR_FILTER,
										self::DRUID_DIMENSION => $filter[self::DRUID_DIMENSION],
										self::DRUID_VALUE => $filter[self::DRUID_VALUES][0]
				);

			else
				$filters_def[] = array(self::DRUID_TYPE => self::DRUID_IN_FILTER,
					self::DRUID_DIMENSION => $filter[self::DRUID_DIMENSION],
					self::DRUID_VALUES => $filter[self::DRUID_VALUES]
				);
		}
		return $filters_def;
	}

	private static function getTimeSeriesReport($data_source, $partner_id, $intervals, $granularity, $metrics, $filter)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, $granularity);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TIMESERIES;
		if (!isset($report_def[self::DRUID_CONTEXT]))
			$report_def[self::DRUID_CONTEXT] = array();
		$report_def[self::DRUID_CONTEXT][self::DRUID_SKIP_EMPTY_BUCKETS] = 'true';
		return $report_def;
	}

	private static function getDimCardinalityReport($data_source, $partner_id, $intervals, $dimension, $filter, $event_type)
	{
		if (!$filter)
			$filter = array();
		$filter[] = array(self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						 self::DRUID_VALUES => array($event_type));

		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, array(), $filter, self::DRUID_GRANULARITY_ALL);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TIMESERIES;

		$report_def[self::DRUID_AGGR][] = array(self::DRUID_TYPE => self::DRUID_CARDINALITY,
												self::DRUID_NAME => self::METRIC_TOTAL_COUNT,
												self::DRUID_FIELDS => is_array($dimension) ? $dimension : array($dimension));

		return $report_def;
	}

	private static function getGroupByReport($data_source, $partner_id, $intervals, $granularity, $dimensions, $metrics, $filter, $pageSize = 0)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, $granularity);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_GROUP_BY;
		$report_def[self::DRUID_DIMENSIONS] = $dimensions;

		return $report_def;
	}

	public static function getGraphsByDateId($result, $graph_metrics_to_headers, $tz_offset, $transform)
	{
		$tz = self::getPhpTimezone($tz_offset);

		$graphs = array();

		foreach ($graph_metrics_to_headers as $column => $header)
		{
			$graphs[$header] = array();
		}

		foreach ($result as $row)
		{
			$row_data = $row[self::DRUID_RESULT];

			$date = call_user_func($transform, $row[self::DRUID_TIMESTAMP], $tz);

			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graphs[$header][$date] = $row_data[$column];
			}
		}
		return $graphs;
	}

	public static function getMultiGraphsByDateId ($result, $multiline_column, $graph_metrics_to_headers, $transform, $tz_offset)
	{
		$tz = self::getPhpTimezone($tz_offset);

		$graphs = array();

		unset($graph_metrics_to_headers[$multiline_column]);
		foreach ($graph_metrics_to_headers as $column => $header)
		{
			$graphs[$header] = array();
		}

		foreach ($result as $row)
		{
			$row_data = $row[self::DRUID_EVENT];

			$date = self::timestampToDateId($row[self::DRUID_TIMESTAMP], $tz);
			$multiline_val = call_user_func($transform, $row_data[$multiline_column]);
			foreach ($graph_metrics_to_headers as $column => $header)
			{
				if (isset($graphs[$header][$date]))
					$graphs[$header][$date] .=	',';
				else
					$graphs[$header][$date] = '';

				$graphs[$header][$date] .= $multiline_val . ':' . $row_data[$column];

			}
		}
		return $graphs;
	}

	public static function getMultiGraphsByColumnName ($result, $graph_metrics_to_headers, $dimension, $transform)
	{
		$graphs = array();

		foreach ($graph_metrics_to_headers as $column => $header)
		{
			$graphs[$header] = array();
		}

		foreach ($result as $row)
		{
			$row_data = $row[self::DRUID_EVENT];

			$dim_value = call_user_func($transform, $row_data[$dimension]);

			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graphs[$header][$dim_value] = $row_data[$column];
			}
		}
		return $graphs;
	}

	public static function getGraphsByColumnName($result, $graph_metrics_to_headers, $type_str)
	{
		$graph = array();
		$row = $result[0];
		if (isset($row))
		{
			$row_data = $row[self::DRUID_RESULT];
			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graph[$header] = $row_data[$column];
			}
		}
		else
		{
			$graph = array_combine(
				array_values($graph_metrics_to_headers),
				array_fill(0, count($graph_metrics_to_headers), 0));
		}

		return array($type_str => $graph);
	}


	private static function getEntriesNames($ids, $partner_id)
	{
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		$c->addSelectColumn(entryPeer::ID);
		$c->addSelectColumn(entryPeer::NAME);

		$c->add(entryPeer::PARTNER_ID, $partner_id);
		$c->add(entryPeer::ID, $ids, Criteria::IN);

		entryPeer::setUseCriteriaFilter(false);
		$stmt = entryPeer::doSelectStmt($c);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		entryPeer::setUseCriteriaFilter(true);

		$entries_names = array();
		foreach ($rows as $row)
		{
			$id = $row['ID'];
			$name = $row['NAME'];
			$entries_names[$id] = $name;
		}
		return $entries_names;
	}

	private static function getQuotedEntriesNames($ids, $partner_id)
	{
		$result = self::getEntriesNames($ids, $partner_id);
		foreach ($result as &$name)
		{
			$name = '"' . str_replace('"', '""', $name) . '"';
		}
		return $result;
	}

	private static function getEntriesUserIdsAndNames($ids, $partner_id)
	{
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		$c->addSelectColumn(entryPeer::ID);
		$c->addSelectColumn(entryPeer::NAME);
		$c->addSelectColumn(entryPeer::PUSER_ID);

		$c->add(entryPeer::PARTNER_ID, $partner_id);
		$c->add(entryPeer::ID, $ids, Criteria::IN);

		entryPeer::setUseCriteriaFilter(false);
		$stmt = entryPeer::doSelectStmt($c);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		entryPeer::setUseCriteriaFilter(true);

		$result = array();
		foreach ($rows as $row)
		{
			$id = $row['ID'];
			$puserId = $row['PUSER_ID'];
			$name = $row['NAME'];
			$result[$id] = array($puserId, '"' . $name . '"');
		}
		return $result;
	}

	private static function getCategoriesNames($ids, $partner_id)
	{
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);

		$c->addSelectColumn(categoryPeer::ID);
		$c->addSelectColumn(categoryPeer::NAME);

		$c->add(categoryPeer::PARTNER_ID, $partner_id);
		$c->add(categoryPeer::ID, $ids, Criteria::IN);

		categoryPeer::setUseCriteriaFilter(false);
		$stmt = categoryPeer::doSelectStmt($c);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		categoryPeer::setUseCriteriaFilter(true);

		$categories_names = array();
		foreach ($rows as $row)
		{
			$id = $row['ID'];
			$name = $row['NAME'];
			$categories_names[$id] = $name;
		}
		return $categories_names;
	}

	private static function genericQueryEnrich($ids, $partner_id, $context)
	{
		$peer = $context['peer'];
		$columns = $context['columns'];
		$dim_column = isset($context['dim_column']) ? $context['dim_column'] : 'ID';
		$partner_id_column = isset($context['partner_id_column']) ? $context['partner_id_column'] : 'PARTNER_ID';
		$custom_crit = isset($context['custom_criterion']) ? $context['custom_criterion'] : null;

		$c = KalturaCriteria::create($peer::OM_CLASS);

		$table_name = $peer::TABLE_NAME;
		$c->addSelectColumn($table_name . '.' . $dim_column);

		$quoted_columns = array();
		foreach ($columns as $index => $column)
		{
			if ($column[0] == '"')
			{
				$column = trim($column, '"');
				$columns[$index] = $column;
				$quoted_columns[$column] = true;
			}
			$exploded_column = explode('.', $column);
			$c->addSelectColumn($table_name . '.' . $exploded_column[0]);
		}

		$c->add($table_name . '.' . $partner_id_column, $partner_id);
		$c->add($table_name . '.' . $dim_column, $ids, Criteria::IN);

		if ($custom_crit)
		{
			$c->addAnd($c->getNewCriterion($custom_crit['column'], $custom_crit['value'], Criteria::CUSTOM));
		}

		$peer::setUseCriteriaFilter(false);
		$stmt = $peer::doSelectStmt($c);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$peer::setUseCriteriaFilter(true);

		$result = array();
		foreach ($rows as $row)
		{
			$output_row = array();
			foreach ($columns as $column)
			{
				$quote = isset($quoted_columns[$column]);

				$exploded_column = explode('.', $column);
				if (count($exploded_column) > 1)
				{
					list($column, $field) = $exploded_column;
					$value = @unserialize($row[$column]);
					$value = isset($value[$field]) ? $value[$field] : '';
				}
				else
				{
					$value = $row[$column];
				}

				if ($quote)
				{
					$value = '"' . str_replace('"', '""', $value) . '"';
				}

				$output_row[] = $value;
			}

			$id = $row[$dim_column];
			$result[$id] = $output_row;
		}
		return $result;
	}

	private static function getCategoriesIds($categories, $partner_id)
	{
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);

		$c->addSelectColumn(categoryPeer::ID);

		$c->add(categoryPeer::PARTNER_ID, $partner_id);
		$c->add(categoryPeer::FULL_NAME, explode(',', $categories), Criteria::IN);

		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$stmt = categoryPeer::doSelectStmt($c);
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		if (count($rows))
		{
			$category_ids = array();
			foreach ($rows as $row)
			{
				$category_ids[] = $row['ID'];
			}
		}
		else
		{
			$category_ids = array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}
		return $category_ids;

	}

	private static function getPlaybackContextCategoriesIds($partner_id, $playback_context, $is_ancestor)
	{
		$category_filter = new categoryFilter();

		if ($is_ancestor)
			$category_filter->set('_matchor_likex_full_name', $playback_context);
		else
			$category_filter->set('_in_full_name', $playback_context);

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$category_filter->attachToCriteria($c);
		$category_filter->setPartnerSearchScope($partner_id);
		$c->applyFilters();

		$category_ids_from_db = $c->getFetchedIds();

		if (count($category_ids_from_db))
			return $category_ids_from_db;
		else
			return array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
	}


	private static function getTotalTableCount($partner_id, $report_type, reportsInputFilter $input_filter, $intervals, $druid_filter, $dimension, $object_ids = null)
	{
		$cache_key = 'reportCount-' . md5("$partner_id|$report_type|$object_ids|".serialize($input_filter));

		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_REPORTS_COUNT);
		if ($cache)
		{
			$total_count = $cache->get($cache_key);
			if ($total_count)
			{
				KalturaLog::log("count from cache: [$total_count]");
				return $total_count;
			}
		}

		$report_def = self::$reports_def[$report_type];
		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$event_type = array_key_exists(self::REPORT_CARDINALITY_METRIC, $report_def) ?
		$report_def[self::REPORT_CARDINALITY_METRIC] : self::EVENT_TYPE_PLAYER_IMPRESSION;

		$query = self::getDimCardinalityReport($data_source, $partner_id, $intervals, $dimension, $druid_filter, $event_type);

		$total_count_arr = self::runQuery($query);
		if (isset($total_count_arr[0][self::DRUID_RESULT][self::METRIC_TOTAL_COUNT]))
		{
			$total_count = floor($total_count_arr[0][self::DRUID_RESULT][self::METRIC_TOTAL_COUNT]);
		}
		else
		{
			$total_count = 0;
		}
		KalturaLog::log("count: [$total_count]");

		if ($cache)
			$cache->set($cache_key, $total_count, myReportsMgr::REPORTS_COUNT_CACHE); // store in the cache for next time

		return $total_count;
	}

	static function getCsvData(
		$partner_id,
		$report_title, $report_text, $headers,
		$report_type,
		reportsInputFilter $input_filter,
		$dimension = null,
		$object_ids = null,
		$page_size =10, $page_index =0, $order_by)
	{
		$csv = new myCsvWrapper();

		$arr = array();

		if (!in_array($report_type, myReportsMgr::$reports_without_graph))
		{
			$arr = self::getGraph(
				$partner_id,
				$report_type,
				$input_filter,
				$dimension,
				$object_ids);
		}


		if (!in_array($report_type, myReportsMgr::$reports_without_totals))
			list($total_header, $total_data) = self::getTotal(
				$partner_id,
				$report_type,
				$input_filter,
				$object_ids);


		if ($page_index * $page_size > self::MAX_CSV_RESULT_SIZE)
		{
			throw new kCoreException('Exceeded max query size: ' . self::MAX_CSV_RESULT_SIZE, kCoreException::SEARCH_TOO_GENERAL);
		}

		if (!in_array($report_type, myReportsMgr::$reports_without_table))
		{
			list($table_header, $table_data, $table_total_count) = self::getTable(
				$partner_id,
				$report_type,
				$input_filter,
				$page_size, $page_index,
				$order_by, $object_ids, true);
		}

		$csv = myCsvReport::createReport($report_title, $report_text, $headers,
			$report_type, $input_filter, $dimension,
			$arr, $total_header, $total_data, $table_header, $table_data, $table_total_count, $csv);

		return $csv->getData();

	}

	/**
	 * will store the content of the report on disk and return the Url for the file
	 *
	 * @param string $partner_id
	 * @param string $report_title
	 * @param string $report_text
	 * @param string $headers
	 * @param int $report_type
	 * @param reportsInputFilter $input_filter
	 * @param string $dimension
	 * @param string $object_ids
	 * @param int $page_size
	 * @param int $page_index
	 * @param string $order_by
	 */
	public static function getUrlForReportAsCsv(
		$partner_id,
		$report_title, $report_text, $headers,
		$report_type,
		reportsInputFilter $input_filter,
		$dimension = null,
		$object_ids = null,
		$page_size =10, $page_index =0, $order_by)
	{
		list($file_path, $file_name) = myReportsMgr::createFileName($partner_id, $report_type, $input_filter, $dimension, $object_ids, $page_size, $page_index, $order_by);

		$data = self::getCsvData($partner_id,
			$report_title, $report_text, $headers,
			$report_type,
			$input_filter,
			$dimension,
			$object_ids,
			$page_size, $page_index, $order_by);

		kFile::fullMkfileDir(dirname($file_path), 0777);

		//adding BOM for fixing problem in open .csv file with special chars using excel.
		$BOM = "\xEF\xBB\xBF";
		$f = @fopen($file_path, 'w');
		fwrite($f, $BOM);
		fwrite($f, $data);
		fclose($f);

		$url = myReportsMgr::createUrl($partner_id, $file_name);
		return $url;
	}

	private static function getAdminConsoleReport($report_def, $data, $headers, $partner_id, $intervals, $metrics, $dimension, $druid_filter, $object_ids)
	{
		$query = self::getTopReport(null, $partner_id, $intervals, $metrics, $dimension, $druid_filter, $metrics[0], '-', count(explode(',', $object_ids)));
		$result = self::runQuery($query);
		$key_field = $report_def[self::REPORT_DIMENSION];
		$rows = $result[0][self::DRUID_RESULT];
		$druid_rows = array();
		foreach ($rows as $row)
		{
			$druid_rows[$row[$key_field]] = $row;
		}
		$mapping = $report_def[self::REPORT_LEGACY][self::REPORT_LEGACY_MAPPING];
		$mapping_by_index = array();
		foreach ($mapping as $legacy_field => $druid_field)
		{
			$legacy_field_index = array_search($legacy_field, $headers);
			$mapping_by_index[$legacy_field_index] = self::$headers_to_metrics[$druid_field];
		}
		$key_index = array_search($report_def[self::REPORT_LEGACY][self::REPORT_LEGACY_JOIN_FIELD], $headers);

		foreach ($data as &$row)
		{
			foreach ($mapping_by_index as $index => $druid_field)
				if (isset($druid_rows[$row[$key_index]]))
					$row[$index] = $druid_rows[$row[$key_index]][$druid_field];
				else
					$row[$index] = 0;
		}
		return $data;
	}

	private static function getVpaasUsageReport($report_def, $data, $headers, $partner_id, $intervals, $metrics, $druid_filter, $timezone_offset)
	{
		$granularity = $report_def[self::REPORT_GRANULARITY];
		$granularity_def = self::getGranularityDef($granularity, $timezone_offset);
		$query = self::getTimeSeriesReport(null, $partner_id, $intervals, $granularity_def, $metrics, $druid_filter);
		$result = self::runQuery($query);
		$report_metrics_to_headers = array();
		foreach ($metrics as $column)
		{
			$report_metrics_to_headers[$column] = self::$metrics_to_headers[$column];
		}
		$druid_result = self::getGraphsByDateId($result, $report_metrics_to_headers, $timezone_offset, self::$transform_time_dimensions[$granularity]);
		$mapping = $report_def[self::REPORT_LEGACY][self::REPORT_LEGACY_MAPPING];
		$druid_field = reset($mapping);
		$legacy_field = key($mapping);
		$legacy_field_index = array_search($legacy_field, $headers);

		$key_index = array_search($report_def[self::REPORT_LEGACY][self::REPORT_LEGACY_JOIN_FIELD], $headers);
		if (false !== $legacy_field_index)
		{
			foreach ($data as &$row)
			{
				$row[$legacy_field_index] = isset($druid_result[$druid_field][$row[$key_index]]) ? $druid_result[$druid_field][$row[$key_index]] : 0;
			}
		}
		return $data;
	}
}
