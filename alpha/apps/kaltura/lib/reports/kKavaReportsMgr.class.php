<?php

class kKavaReportsMgr extends kKavaBase
{
	// dimensions
	const DIMENSION_TIME = '__time';

	/// metrics
	// druid predefined metrics
	const METRIC_DELTA = 'delta';
	const METRIC_SIZE_BYTES = 'size';
	const METRIC_DURATION_SEC = 'duration';
	const METRIC_FLAVOR_SIZE_BYTES = 'flavorSize';
	const METRIC_PLAY_TIME_SUM = 'playTimeSum';
	const METRIC_BUFFER_TIME_SEC = 'bufferTimeSum';
	const METRIC_BITRATE_SUM = 'bitrateSum';
	const METRIC_BITRATE_COUNT = 'bitrateCount';
	const METRIC_UNIQUE_USER_IDS = 'uniqueUserIds';
	const METRIC_SUM_PRICE = 'price';

	// druid calculated metrics
	const METRIC_QUARTILE_PLAY_TIME = 'sum_time_viewed';
	const METRIC_VIEW_PERIOD_PLAY_TIME = 'sum_view_period';
	const METRIC_AVG_PLAY_TIME = 'avg_time_viewed';
	const METRIC_PLAYER_IMPRESSION_RATIO = 'load_play_ratio';
	const METRIC_AVG_DROP_OFF = 'avg_view_drop_off';
	const METRIC_PLAYTHROUGH_RATIO = 'play_through_ratio';
	const METRIC_UNIQUE_ENTRIES = 'unique_videos';
	const METRIC_UNIQUE_USERS = 'unique_known_users';
	const METRIC_CARDINALITY = 'cardinality';
	const METRIC_COUNT_UGC = 'count_ugc';
	const METRIC_COUNT_ADMIN = 'count_admin';
	const METRIC_COUNT_TOTAL = 'count_total';
	const METRIC_COUNT_TOTAL_ALL_TIME = 'count_total_all_time';
	const METRIC_BANDWIDTH_SIZE_MB = 'bandwidth_consumption';
	const METRIC_BANDWIDTH_SIZE_KB = 'bandwidth_consumption_kb';
	const METRIC_TRANSCODING_SIZE_MB = 'transcoding_consumption';
	const METRIC_STORAGE_ADDED_MB = 'added_storage';
	const METRIC_STORAGE_DELETED_MB = 'deleted_storage';
	const METRIC_STORAGE_TOTAL_MB = 'total_storage_mb';
	const METRIC_ENTRIES_ADDED = 'added_entries';
	const METRIC_ENTRIES_DELETED = 'deleted_entries';
	const METRIC_ENTRIES_TOTAL = 'total_entries';
	const METRIC_USERS_ADDED = 'added_users';
	const METRIC_USERS_DELETED = 'deleted_users';
	const METRIC_USERS_TOTAL = 'total_users';
	const METRIC_DURATION_ADDED_MSEC = 'added_msecs';
	const METRIC_DURATION_DELETED_MSEC = 'deleted_msecs';
	const METRIC_DURATION_TOTAL_MSEC = 'total_msecs';
	const METRIC_BUFFER_TIME_RATIO = 'avg_buffer_time';
	const METRIC_AVG_BITRATE = 'avg_bitrate';
	const METRIC_ORIGIN_BANDWIDTH_SIZE_MB = 'origin_bandwidth_consumption';
	const METRIC_UNIQUE_CONTRIBUTORS = 'unique_contributors';
	
	// druid intermediate metrics
	const METRIC_PLAYTHROUGH = 'play_through';
	const METRIC_SIZE_ADDED_BYTES = 'size_added';
	const METRIC_SIZE_DELETED_BYTES = 'size_deleted';
	const METRIC_DURATION_ADDED_SEC = 'duration_added';
	const METRIC_DURATION_DELETED_SEC = 'duration_deleted';
	const METRIC_BANDWIDTH_SIZE_BYTES = 'bandwidth_size';
	const METRIC_STORAGE_SIZE_BYTES = 'total_storage';
	const METRIC_QUARTILE_PLAY_TIME_SEC = 'quartile_play_time';
	const METRIC_VIEW_PERIOD_PLAY_TIME_SEC = 'view_period_play_time';
	const METRIC_VIEW_BUFFER_TIME_SEC = 'view_buffer_time';
	const METRIC_ORIGIN_BANDWIDTH_SIZE_BYTES = 'origin_bandwidth_size';

	// non druid metrics
	const METRIC_BANDWIDTH_STORAGE_MB = 'combined_bandwidth_storage';
	const METRIC_AVERAGE_STORAGE_AGGR_MONTHLY_MB = 'aggregated_monthly_avg_storage';
	const METRIC_BANDWIDTH_STORAGE_AGGR_MONTHLY_MB = 'combined_bandwidth_aggregated_storage';
	const METRIC_PEAK_STORAGE_MB = 'peak_storage';
	const METRIC_AVERAGE_STORAGE_MB = 'average_storage';
	const METRIC_LATEST_STORAGE_MB = 'latest_storage';
	const METRIC_PEAK_ENTRIES = 'peak_entries';
	const METRIC_AVERAGE_ENTRIES = 'average_entries';
	const METRIC_LATEST_ENTRIES = 'latest_entries';
	const METRIC_PEAK_USERS = 'peak_users';
	const METRIC_AVERAGE_USERS = 'average_users';
	const METRIC_LATEST_USERS = 'latest_users';
	const METRIC_PEAK_DURATION_MSEC = 'peak_msecs';
	const METRIC_AVERAGE_DURATION_MSEC = 'average_msecs';
	const METRIC_LATEST_DURATION_MSEC = 'latest_msecs';
	
	// player-events-realtime specific metrics
	const METRIC_VIEW_PLAY_TIME_SEC = 'sum_view_time';
	const METRIC_VIEW_BITRATE_COUNT = 'view_bitrate_count';
	const METRIC_AVG_VIEW_BITRATE = 'avg_view_bitrate';

	/// report settings
	// report settings - common
	const REPORT_DATA_SOURCE = 'report_data_source';
	const REPORT_INTERVAL = 'report_interval';
	const REPORT_JOIN_REPORTS = 'report_join_reports';
	const REPORT_COLUMN_MAP = 'report_column_map';

	// report settings - filter
	const REPORT_FILTER = 'report_filter';
	const REPORT_FILTER_DIMENSION = 'report_filter_dimension';
	const REPORT_PLAYBACK_TYPES = 'report_playback_types';
	const REPORT_OBJECT_IDS_TRANSFORM = 'report_object_ids_transform';
	const REPORT_SKIP_PARTNER_FILTER = 'report_skip_partner_filter';

	// report settings - table
	const REPORT_DIMENSION = 'report_dimension';
	const REPORT_DIMENSION_MAP = 'report_dimension_map';
	const REPORT_DIMENSION_HEADERS = 'report_detail_dimensions_headers';
	const REPORT_DRILLDOWN_DIMENSION = 'report_drilldown_dimension';
	const REPORT_DRILLDOWN_DIMENSION_MAP = 'report_drilldown_dimension_map';
	const REPORT_DRILLDOWN_DIMENSION_HEADERS = 'report_drilldown_detail_dimensions_headers';
	const REPORT_ENRICH_DEF = 'report_enrich_definition';
	const REPORT_ENRICH_FUNC = 'func';
	const REPORT_ENRICH_CONTEXT = 'context';
	const REPORT_ENRICH_INPUT = 'input';
	const REPORT_ENRICH_OUTPUT = 'field';
	const REPORT_METRICS = 'report_metrics';
	const REPORT_FORCE_TOTAL_COUNT = 'report_force_total_count';
	const REPORT_TABLE_MAP = 'report_table_map';
	const REPORT_TABLE_FINALIZE_FUNC = 'report_table_finalize_func';
	const REPORT_EDIT_FILTER_FUNC = 'report_edit_filter_func';

	// report settings - graph
	const REPORT_GRANULARITY = 'report_granularity';
	const REPORT_GRAPH_TYPE = 'report_graph_type';
	const REPORT_GRAPH_NAME = 'report_graph_name';
	const REPORT_GRAPH_METRICS = 'report_graph_metrics';
	const REPORT_GRAPH_MAP = 'report_graph_map';
	const REPORT_GRAPH_ACCUMULATE_FUNC = 'report_graph_accumulate_func';
	const REPORT_GRAPH_AGGR_FUNC = 'report_graph_aggr_func';
	const REPORT_GRAPH_FINALIZE_FUNC = 'report_graph_finalize_func';
	const REPORT_JOIN_GRAPHS = 'report_join_graphs';
	
	// report settings - total
	const REPORT_TOTAL_METRICS = 'report_total_metrics';
	const REPORT_TOTAL_MAP = 'report_total_map';
	const REPORT_TOTAL_FROM_TABLE_FUNC = 'report_total_from_table_func';

	// report settings - custom reports
	const REPORT_CUSTOM_PARAM = 'custom_param';
	const REPORT_CUSTOM_PARAM_FUNC = 'func';
			
	// graph types
	const GRAPH_BY_DATE_ID = 'by_date_id';
	const GRAPH_BY_NAME = 'by_name';
	const GRAPH_ASSOC_MULTI_BY_DATE_ID = 'assoc_multi_by_date_id';
	const GRAPH_MULTI_BY_DATE_ID = 'multi_by_date_id';
	const GRAPH_MULTI_BY_NAME = 'multi_by_name';

	// granularities
	const GRANULARITY_MINUTE = 'minute';
	const GRANULARITY_THIRTY_MINUTE = 'thirty_minute';
	const GRANULARITY_HOUR = 'hour';
	const GRANULARITY_DAY = 'day';
	const GRANULARITY_MONTH = 'month';
	
	// report intervals
	const INTERVAL_START_TO_END = 'start_to_end';
	const INTERVAL_BASE_TO_START = 'base_to_start';
	const INTERVAL_BASE_TO_END = 'base_to_end';
	
	// aggregation intervals
	const INTERVAL_DAYS = 'days';
	const INTERVAL_MONTHS = 'months';
	const INTERVAL_ALL = 'all';
		
	const DAY_START_TIME = 'T00:00:00';
	const DAY_END_TIME = 'T23:59:59';
	const BASE_DATE_ID = '2013-12-01';	// the date from which to start aggragating usage data
	const BASE_TIMESTAMP = '2013-12-01T00:00:00Z';
	
	// limits
	const MAX_RESULT_SIZE = 12000;
	const MAX_CSV_RESULT_SIZE = 60000;
	const MAX_CUSTOM_REPORT_RESULT_SIZE = 100000;
	const MIN_THRESHOLD = 500;
	
	const ENRICH_CHUNK_SIZE = 10000;
	const ENRICH_DIM_DELIMITER = '|';
	const ENRICH_FOREACH_KEYS_FUNC = 'self::forEachKeys';
	const CLIENT_TAG_PRIORITY = 5;

	const GET_TABLE_FLAG_IS_CSV = 0x01;
	const GET_TABLE_FLAG_IDS_ONLY = 0x02;
	
	const COLUMN_FORMAT_QUOTE = 'quote';
	const COLUMN_FORMAT_UNIXTIME = 'unixtime';
		
	protected static $reports_def = array(
		myReportsMgr::REPORT_TYPE_TOP_CONTENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name',
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_CONTENT_DROPOFF => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_GRAPH_TYPE => self::GRAPH_BY_NAME,
			self::REPORT_GRAPH_NAME => 'content_dropoff',
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
		),

		myReportsMgr::REPORT_TYPE_CONTENT_INTERACTIONS => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
		),

		myReportsMgr::REPORT_TYPE_MAP_OVERLAY => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_LOCATION_COUNTRY,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_LOCATION_REGION,
				'location_name' => self::DIMENSION_LOCATION_REGION
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => 'country',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toShortName',
				),
				array(
					self::REPORT_ENRICH_OUTPUT => 'location_name',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'strtoupper',
				)
			)
		),

		myReportsMgr::REPORT_TYPE_TOP_SYNDICATION => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_DOMAIN,
				'domain_name' => self::DIMENSION_DOMAIN
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_AVG_DROP_OFF),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DOMAIN,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'referrer' => self::DIMENSION_URL,
			),
		),

		myReportsMgr::REPORT_TYPE_USER_ENGAGEMENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
		),

		myReportsMgr::REPORT_TYPE_SPECIFIC_USER_ENGAGEMENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'entry_name' => self::DIMENSION_ENTRY_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
		),

		myReportsMgr::REPORT_TYPE_USER_TOP_CONTENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
		),

		myReportsMgr::REPORT_TYPE_USER_CONTENT_DROPOFF => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_GRAPH_TYPE => self::GRAPH_BY_NAME,
			self::REPORT_GRAPH_NAME => 'user_content_dropoff',
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO),
		),

		myReportsMgr::REPORT_TYPE_USER_CONTENT_INTERACTIONS => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_CLICKED),
		),

		myReportsMgr::REPORT_TYPE_APPLICATIONS => array(
			self::REPORT_INTERVAL => '-30/0',
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_APPLICATION
			),
			self::REPORT_METRICS => array(),
		),

		myReportsMgr::REPORT_TYPE_PLATFORMS => array(
			self::REPORT_DIMENSION_MAP => array(
				'device' => self::DIMENSION_DEVICE
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_DATE_ID,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DEVICE,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'os' => self::DIMENSION_OS
			),
		),

		myReportsMgr::REPORT_TYPE_OPERATING_SYSTEM => array(
			self::REPORT_DIMENSION_MAP => array(
				'os' => self::DIMENSION_OS
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_OS,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'browser' => self::DIMENSION_BROWSER
			),
		),

		myReportsMgr::REPORT_TYPE_BROWSERS => array(
			self::REPORT_DIMENSION_MAP => array(
				'browser' => self::DIMENSION_BROWSER
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
		),

		myReportsMgr::REPORT_TYPE_OPERATING_SYSTEMS_FAMILIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'os_family' => self::DIMENSION_OS_FAMILY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
		),

		myReportsMgr::REPORT_TYPE_BROWSERS_FAMILIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'browser_family' => self::DIMENSION_BROWSER_FAMILY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
		),

		myReportsMgr::REPORT_TYPE_LIVE => array(
			self::REPORT_GRANULARITY => self::GRANULARITY_HOUR,
			self::REPORT_PLAYBACK_TYPES => array(self::PLAYBACK_TYPE_LIVE, self::PLAYBACK_TYPE_DVR),
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name', 
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
		),

		myReportsMgr::REPORT_TYPE_TOP_PLAYBACK_CONTEXT => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_PLAYBACK_CONTEXT,
				'name' => self::DIMENSION_PLAYBACK_CONTEXT
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name', 
				self::REPORT_ENRICH_FUNC => 'self::getCategoriesNames'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self:: METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_VPAAS_USAGE => array(
			self::REPORT_JOIN_GRAPHS => array(
				// bandwidth
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_BANDWIDTH_SIZE_MB, self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB),
				),

				// transcoding
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_TRANSCODING_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_TRANSCODING_SIZE_MB),
				),

				// storage
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
				),

				// media entries
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
						self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_LIVE_STREAM)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_ENTRIES_DELETED),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
						self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_LIVE_STREAM)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_TOTAL),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedEntriesGraphs',
					),

				// named users
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_USER_LIFECYCLE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_USERS_ADDED, self::METRIC_USERS_DELETED),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_USER_LIFECYCLE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_USERS_TOTAL),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedUsersGraphs',
					),

				// plays
				array(
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
				),
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
			self::REPORT_COLUMN_MAP => array(
				'total_plays' => 'count_plays',
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
				'total_media_entries' => self::METRIC_PEAK_ENTRIES,
				'total_end_users' => self::METRIC_PEAK_USERS,
				'total_views' => 'count_loads',
				'origin_bandwidth_consumption' => self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB
			),
		),

		myReportsMgr::REPORT_TYPE_TOP_CONTRIBUTORS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_KUSER_ID,
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUserScreenNameWithFallback'
			),
			self::REPORT_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_SHOW),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD)
			),
		),
			
		myReportsMgr::REPORT_TYPE_CONTENT_CONTRIBUTIONS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_SOURCE_TYPE,
				'entry_media_source_name' => self::DIMENSION_SOURCE_TYPE
			),
			self::REPORT_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_COUNT_TOTAL, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD)
			),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_SOURCE_TYPE,
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_media_source_name',
				self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
				self::REPORT_ENRICH_CONTEXT => 'self::toSafeId',
			),
		),
			
		myReportsMgr::REPORT_TYPE_TOP_CREATORS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'user_screen_name' => self::DIMENSION_KUSER_ID,
				'user_full_name' => self::DIMENSION_KUSER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'user_screen_name', 'user_full_name'),
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'SCREEN_NAME', 'FULL_NAME'),
					'hash' => false,
				)),
			self::REPORT_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_SHOW),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_SHOW, self::METRIC_COUNT_UGC, self::METRIC_COUNT_ADMIN),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD)
			),
		),
		
		myReportsMgr::REPORT_TYPE_USER_USAGE => array(
			self::REPORT_DIMENSION_MAP => array(
				'kuser_id' => self::DIMENSION_KUSER_ID,
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo',
				self::REPORT_ENRICH_CONTEXT => array(
					'hash' => false,
				)
			),
			self::REPORT_JOIN_REPORTS => array(
				// entries added / deleted
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_ENTRIES_DELETED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_DURATION_DELETED_MSEC),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_ENTRIES_DELETED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_DURATION_DELETED_MSEC),
				),

				// storage added / deleted
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),
								
				// entries total
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_END,
					self::REPORT_METRICS => array(self::METRIC_ENTRIES_TOTAL, self::METRIC_DURATION_TOTAL_MSEC),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_TOTAL, self::METRIC_DURATION_TOTAL_MSEC),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedEntriesGraphsBaseToEnd',
				),

				// storage total
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_END,
					self::REPORT_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphsBaseToEnd',
				),
			),
			self::REPORT_TABLE_MAP => array(
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'total_entries' => self::METRIC_ENTRIES_TOTAL,
				'added_storage_mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage_mb' => self::METRIC_STORAGE_DELETED_MB,
				'total_storage_mb' => self::METRIC_STORAGE_TOTAL_MB,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'deleted_msecs' => self::METRIC_DURATION_DELETED_MSEC,
				'total_msecs' => self::METRIC_DURATION_TOTAL_MSEC,
			),
			self::REPORT_TOTAL_MAP => array(
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'total_entries' => self::METRIC_ENTRIES_TOTAL,
				'added_storage_mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage_mb' => self::METRIC_STORAGE_DELETED_MB,
				'total_storage_mb' => self::METRIC_STORAGE_TOTAL_MB,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'deleted_msecs' => self::METRIC_DURATION_DELETED_MSEC,
				'total_msecs' => self::METRIC_DURATION_TOTAL_MSEC,
			),
			self::REPORT_GRAPH_MAP => array(
				'added_storage_mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage_mb' => self::METRIC_STORAGE_DELETED_MB,
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'deleted_msecs' => self::METRIC_DURATION_DELETED_MSEC,
				'total_entries' => self::METRIC_LATEST_ENTRIES,
				'total_storage_mb' => self::METRIC_LATEST_STORAGE_MB,
				'total_msecs' => self::METRIC_LATEST_DURATION_MSEC,
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
		),

		myReportsMgr::REPORT_TYPE_SPECIFIC_USER_USAGE => array(
			self::REPORT_JOIN_GRAPHS => array(
				// entries added / deleted
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_ENTRIES_DELETED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_DURATION_DELETED_MSEC),
				),
			
				// storage added / deleted
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),

				// entries total
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_END,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_TOTAL, self::METRIC_DURATION_TOTAL_MSEC),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedEntriesGraphsBaseToEnd',
				),
			
				// storage total
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_END,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphsBaseToEnd',
				),
			),
				
			self::REPORT_TABLE_MAP => array(
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'added_storage_mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage_mb' => self::METRIC_STORAGE_DELETED_MB,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'deleted_msecs' => self::METRIC_DURATION_DELETED_MSEC,
				'total_entries' => self::METRIC_LATEST_ENTRIES,
				'total_storage_mb' => self::METRIC_LATEST_STORAGE_MB,
				'total_msecs' => self::METRIC_LATEST_DURATION_MSEC,
			),
			self::REPORT_TOTAL_MAP => array(
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'total_entries' => self::METRIC_LATEST_ENTRIES,
				'added_storage_mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage_mb' => self::METRIC_STORAGE_DELETED_MB,
				'total_storage_mb' => self::METRIC_LATEST_STORAGE_MB,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'deleted_msecs' => self::METRIC_DURATION_DELETED_MSEC,
				'total_msecs' => self::METRIC_LATEST_DURATION_MSEC,
			),
			self::REPORT_GRAPH_MAP => array(
				'added_storage_mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage_mb' => self::METRIC_STORAGE_DELETED_MB,
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'deleted_msecs' => self::METRIC_DURATION_DELETED_MSEC,
				'total_entries' => self::METRIC_LATEST_ENTRIES,
				'total_storage_mb' => self::METRIC_LATEST_STORAGE_MB,
				'total_msecs' => self::METRIC_LATEST_DURATION_MSEC,
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
		),
				
		myReportsMgr::REPORT_TYPE_PARTNER_USAGE => array(
			self::REPORT_SKIP_PARTNER_FILTER => true,		// object_ids contains the partner ids (validated externally)
			self::REPORT_JOIN_GRAPHS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_BANDWIDTH_SIZE_MB),
				),
				
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_TRANSCODING_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_TRANSCODING_SIZE_MB),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),
				
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
				),
			),
			self::REPORT_GRAPH_MAP => array(
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'combined_bandwidth_storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
			),
			self::REPORT_TOTAL_MAP => array(
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'combined_bandwidth_storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
				'aggregated_monthly_avg_storage' => self::METRIC_AVERAGE_STORAGE_AGGR_MONTHLY_MB, 
				'combined_bandwidth_aggregated_storage' => self::METRIC_BANDWIDTH_STORAGE_AGGR_MONTHLY_MB,
			),
			self::REPORT_TABLE_MAP => array(
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'combined_bandwidth_storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
			self::REPORT_GRAPH_FINALIZE_FUNC => 'self::addCombinedUsageGraph',
		),

		myReportsMgr::REPORT_TYPE_ENTRY_USAGE => array(
			self::REPORT_JOIN_GRAPHS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_ENTRIES_DELETED),
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_TOTAL),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedEntriesGraphs',
				),
			),
			self::REPORT_COLUMN_MAP => array(
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'deleted_entries' => self::METRIC_ENTRIES_DELETED,
				'peak_entries' => self::METRIC_PEAK_ENTRIES,
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData'
		),

		myReportsMgr::REPORT_TYPE_ADMIN_CONSOLE => array(
			self::REPORT_SKIP_PARTNER_FILTER => true,
			self::REPORT_DIMENSION_MAP => array(
				'STATUS' => self::DIMENSION_PARTNER_ID,
				'id' => self::DIMENSION_PARTNER_ID,
				'partner name' => self::DIMENSION_PARTNER_ID,
				'created at' => self::DIMENSION_PARTNER_ID,
				'partner package' => self::DIMENSION_PARTNER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('STATUS', 'partner name', 'created at', 'partner package'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'PartnerPeer',
					'int_ids_only' => true,
					'columns' => array('STATUS', 'PARTNER_NAME', '@CREATED_AT', 'PARTNER_PACKAGE'),
				)
			),
			self::REPORT_JOIN_REPORTS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_METRICS => array(self::EVENT_TYPE_PLAYER_IMPRESSION, self::EVENT_TYPE_PLAY),
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_END,
					self::REPORT_METRICS => array(self::METRIC_COUNT_TOTAL_ALL_TIME),
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_METRICS => array(self::METRIC_COUNT_TOTAL, self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_SHOW),
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_METRICS => array(self::METRIC_BANDWIDTH_SIZE_MB),
				),				
				array(
					self::REPORT_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_JOIN_GRAPHS => array(
						array(
							self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
							self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
							self::REPORT_FILTER => array(		// can exclude logical deltas in this report
								self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
								self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
							),
							self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
							self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
						),
		
						array(
							self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
							self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
							self::REPORT_FILTER => array(		// can exclude logical deltas in this report
								self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
								self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
							),
							self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
							self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
							self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
						),
					),
					self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
					self::REPORT_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB, self::METRIC_AVERAGE_STORAGE_MB, self::METRIC_PEAK_STORAGE_MB),
				),
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_TRANSCODING_USAGE,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_METRICS => array(self::METRIC_TRANSCODING_SIZE_MB),
				),
			),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addCombinedUsageColumn',
			self::REPORT_TABLE_MAP => array(
				'count loads' => 'count_loads',
				'count plays' => 'count_plays',
				'count media' => self::METRIC_COUNT_TOTAL, 
				'count media all time' => self::METRIC_COUNT_TOTAL_ALL_TIME,
				'count video' => 'count_video', 
				'count image' => 'count_image', 
				'count audio' => 'count_audio', 
				'count mix' => 'count_mix',
				'count bandwidth mb' => self::METRIC_BANDWIDTH_SIZE_MB,
				'added storage mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted storage mb' => self::METRIC_STORAGE_DELETED_MB,
				'peak storage mb' => self::METRIC_PEAK_STORAGE_MB,
				'average storage mb' => self::METRIC_AVERAGE_STORAGE_MB,
				'combined bandwidth storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding mb' => self::METRIC_TRANSCODING_SIZE_MB,
			),
		),

		myReportsMgr::REPORT_TYPE_VAR_USAGE => array(
			self::REPORT_SKIP_PARTNER_FILTER => true,		// object_ids contains the partner ids (validated externally)
			self::REPORT_DIMENSION_MAP => array(
				'status' => self::DIMENSION_PARTNER_ID,
				'partner_name' => self::DIMENSION_PARTNER_ID,
				'partner_id' => self::DIMENSION_PARTNER_ID,
				'created_at' => self::DIMENSION_PARTNER_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('status', 'partner_name', 'created_at'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'PartnerPeer',
					'int_ids_only' => true,
					'columns' => array('STATUS', 'PARTNER_NAME', '@CREATED_AT'),
				)
			),
			self::REPORT_JOIN_GRAPHS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_TRANSCODING_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_TRANSCODING_SIZE_MB),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_BANDWIDTH_SIZE_MB),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
				),
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addCombinedUsageColumn',
			self::REPORT_GRAPH_FINALIZE_FUNC => 'self::addCombinedUsageGraph',
			self::REPORT_TABLE_MAP => array(
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'combined_bandwidth_storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding_usage' => self::METRIC_TRANSCODING_SIZE_MB,
			),
			self::REPORT_TOTAL_MAP => array(
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'combined_bandwidth_storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
				'aggregated_monthly_avg_storage' => self::METRIC_AVERAGE_STORAGE_AGGR_MONTHLY_MB, 
				'combined_bandwidth_aggregated_storage' => self::METRIC_BANDWIDTH_STORAGE_AGGR_MONTHLY_MB,
			),
			self::REPORT_GRAPH_MAP => array(
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'combined_bandwidth_storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
			),
		),

		myReportsMgr::REPORT_TYPE_PEAK_STORAGE => array(
			self::REPORT_SKIP_PARTNER_FILTER => true,		// object_ids contains the partner ids (validated externally)
			self::REPORT_DIMENSION_MAP => array('partner_id' => self::DIMENSION_PARTNER_ID),
			self::REPORT_JOIN_GRAPHS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),

				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
				),
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
			self::REPORT_TOTAL_FROM_TABLE_FUNC => 'self::getTotalPeakStorageFromTable',
		),
			
		// Note: historically this report returns the bandwidth in kb in table, and in mb in graph
		myReportsMgr::REPORT_TYPE_PARTNER_BANDWIDTH_USAGE => array(
			self::REPORT_EDIT_FILTER_FUNC => 'self::partnerUsageEditFilter',
			self::REPORT_JOIN_GRAPHS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_BANDWIDTH_SIZE_MB, self::METRIC_BANDWIDTH_SIZE_KB),
				),
				
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),
				
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
				),
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
			self::REPORT_GRAPH_MAP => array(
				'bandwidth' => self::METRIC_BANDWIDTH_SIZE_MB,
			),
			self::REPORT_TABLE_MAP => array(
				'avg_continuous_aggr_storage_mb' => self::METRIC_AVERAGE_STORAGE_MB,
				'sum_partner_bandwidth_kb' => self::METRIC_BANDWIDTH_SIZE_KB,
			),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addRollupRow',
		),

		myReportsMgr::REPORT_TYPE_PARTNER_USAGE_DASHBOARD => array(
			self::REPORT_EDIT_FILTER_FUNC => 'self::partnerUsageEditFilter',
			self::REPORT_JOIN_GRAPHS => array(
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_BANDWIDTH_SIZE_KB),
				),
				
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB),
				),
				
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_FILTER => array(		// can exclude logical deltas in this report
						self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
						self::DRUID_VALUES => array(self::EVENT_TYPE_STATUS, self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_PHYSICAL_DELETE)
					),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
					self::REPORT_GRAPH_ACCUMULATE_FUNC => 'self::addAggregatedStorageGraphs',
				),
			),
			self::REPORT_GRAPH_AGGR_FUNC => 'self::aggregateUsageData',
			self::REPORT_TABLE_MAP => array(
				'avg_continuous_aggr_storage_mb' => self::METRIC_AVERAGE_STORAGE_MB,
				'sum_partner_bandwidth_kb' => self::METRIC_BANDWIDTH_SIZE_KB,
			),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addRollupRow',
		),

		myReportsMgr::REPORT_TYPE_REACH_USAGE => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_REACH_USAGE,
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'reachProfileId' => self::DIMENSION_REACH_PROFILE_ID,
				'serviceType' => self::DIMENSION_SERVICE_TYPE,
				'serviceFeature' => self::DIMENSION_SERVICE_FEATURE,
				'turnaroundTime' => self::DIMENSION_TURNAROUND_TIME,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'entry_name',
				self::REPORT_ENRICH_FUNC => 'self::getEntriesNames'
			),
			self::REPORT_METRICS => array(self::METRIC_SUM_PRICE),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_ENTRIES, self::METRIC_SUM_PRICE),
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_STATUS,
				self::DRUID_VALUES => array(self::TASK_READY)
			),
		),

		myReportsMgr::REPORT_TYPE_TOP_CUSTOM_VAR1 => array(
			self::REPORT_DIMENSION_MAP => array('custom_var1' => self::DIMENSION_CUSTOM_VAR1),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_CUSTOM_VAR1,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_MAP_OVERLAY_CITY => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'city' => self::DIMENSION_LOCATION_CITY,
				'coordinates' => self::DIMENSION_LOCATION_CITY,
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region', 'city'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
			),
		),

		myReportsMgr::REPORT_TYPE_USER_ENGAGEMENT_TIMELINE => array(
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF),
		),

		myReportsMgr::REPORT_TYPE_UNIQUE_USERS_PLAY => array(
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array(self::EVENT_TYPE_PLAY)
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_USERS),
		),

		myReportsMgr::REPORT_TYPE_APP_DOMAIN_UNIQUE_ACTIVE_USERS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_API_USAGE,
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
			self::REPORT_DIMENSION_MAP => array(
				'application' => self::DIMENSION_APPLICATION,
				'domain' => self::DIMENSION_DOMAIN
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_USERS)
		),

		myReportsMgr::REPORT_TYPE_MAP_OVERLAY_COUNTRY => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' =>  self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF),
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

		myReportsMgr::REPORT_TYPE_MAP_OVERLAY_REGION => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'coordinates' => self::DIMENSION_LOCATION_REGION
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
			),
		),

		myReportsMgr::REPORT_TYPE_TOP_CONTENT_CREATOR => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'creator_name' => self::DIMENSION_ENTRY_ID,
				'created_at' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name', 'creator_name', 'created_at'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'peer' => 'entryPeer',
						'columns' => array('NAME', 'KUSER_ID', '@CREATED_AT'),
					)
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('creator_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)'),
						'peer' => 'kuserPeer',
						'hash' => false
					)
				)
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		myReportsMgr::REPORT_TYPE_TOP_CONTENT_CONTRIBUTORS => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'creator_name' => self::DIMENSION_KUSER_ID,
				'created_at' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('creator_name', 'created_at'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)','@CREATED_AT'),
					'peer' => 'kuserPeer',
					'hash' => false
				)
			),
			self::REPORT_JOIN_REPORTS => array(
				// plays
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_DIMENSION => self::DIMENSION_ENTRY_OWNER_ID,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
						self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_LIVE_STREAM)
					),
					self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY),
					self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
				),
				// entries & msecs added
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
						self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_IMAGE, self::MEDIA_TYPE_LIVE_STREAM)
					),
					self::REPORT_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_UNIQUE_CONTRIBUTORS),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_UNIQUE_CONTRIBUTORS),
				),
			),
		),

		myReportsMgr::REPORT_TYPE_TOP_SOURCES => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION_MAP => array(
				'source' => self::DIMENSION_SOURCE_TYPE
			),
			self::REPORT_FILTER => array(
				array(self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
				self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_LIVE_STREAM, self::MEDIA_TYPE_LIVE_WIN_MEDIA, self::MEDIA_TYPE_LIVE_REAL_MEDIA, self::MEDIA_TYPE_LIVE_QUICKTIME)),
				array(self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
					self::DRUID_VALUES => array(self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_STATUS)),
			),
			self::REPORT_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_UNIQUE_CONTRIBUTORS),
		),
	);
	
	protected static $event_type_count_aggrs = array(
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
		self::EVENT_TYPE_REPORT_CLICKED,
		self::EVENT_TYPE_VIEW,
	);

	protected static $media_type_count_aggrs = array(
		self::MEDIA_TYPE_VIDEO,
		self::MEDIA_TYPE_AUDIO,
		self::MEDIA_TYPE_IMAGE,
		self::MEDIA_TYPE_SHOW,
	);
	
	protected static $playthrough_event_types = array(
		self::EVENT_TYPE_PLAYTHROUGH_25,
		self::EVENT_TYPE_PLAYTHROUGH_50,
		self::EVENT_TYPE_PLAYTHROUGH_75,
		self::EVENT_TYPE_PLAYTHROUGH_100
	);
	
	protected static $metrics_to_headers = array(
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
		self::EVENT_TYPE_VIEW => 'views',
		self::MEDIA_TYPE_VIDEO => 'count_video',
		self::MEDIA_TYPE_AUDIO => 'count_audio',
		self::MEDIA_TYPE_IMAGE => 'count_image',
		self::MEDIA_TYPE_SHOW => 'count_mix',
		
		// TODO: remove the below - assume metric=header for anything not explicitly set
		self::METRIC_QUARTILE_PLAY_TIME => self::METRIC_QUARTILE_PLAY_TIME,
		self::METRIC_AVG_PLAY_TIME => self::METRIC_AVG_PLAY_TIME,
		self::METRIC_PLAYER_IMPRESSION_RATIO => self::METRIC_PLAYER_IMPRESSION_RATIO,
		self::METRIC_AVG_DROP_OFF => self::METRIC_AVG_DROP_OFF,
		self::METRIC_UNIQUE_ENTRIES => self::METRIC_UNIQUE_ENTRIES,
		self::METRIC_UNIQUE_USERS => self::METRIC_UNIQUE_USERS,
		self::METRIC_PLAYTHROUGH_RATIO => self::METRIC_PLAYTHROUGH_RATIO,
		self::METRIC_COUNT_TOTAL => self::METRIC_COUNT_TOTAL,
		self::METRIC_COUNT_TOTAL_ALL_TIME => self::METRIC_COUNT_TOTAL_ALL_TIME,
		self::METRIC_COUNT_UGC => self::METRIC_COUNT_UGC,
		self::METRIC_COUNT_ADMIN => self::METRIC_COUNT_ADMIN,
		self::METRIC_STORAGE_TOTAL_MB => self::METRIC_STORAGE_TOTAL_MB,
		self::METRIC_BANDWIDTH_SIZE_MB => self::METRIC_BANDWIDTH_SIZE_MB,
		self::METRIC_BANDWIDTH_SIZE_KB => self::METRIC_BANDWIDTH_SIZE_KB,
		self::METRIC_TRANSCODING_SIZE_MB => self::METRIC_TRANSCODING_SIZE_MB,
		self::METRIC_STORAGE_ADDED_MB => self::METRIC_STORAGE_ADDED_MB,
		self::METRIC_STORAGE_DELETED_MB => self::METRIC_STORAGE_DELETED_MB,
		self::METRIC_AVERAGE_STORAGE_MB => self::METRIC_AVERAGE_STORAGE_MB,
		self::METRIC_PEAK_STORAGE_MB => self::METRIC_PEAK_STORAGE_MB,
		self::METRIC_ENTRIES_ADDED => self::METRIC_ENTRIES_ADDED,
		self::METRIC_ENTRIES_DELETED => self::METRIC_ENTRIES_DELETED,
		self::METRIC_ENTRIES_TOTAL => self::METRIC_ENTRIES_TOTAL,
		self::METRIC_DURATION_ADDED_MSEC => self::METRIC_DURATION_ADDED_MSEC,
		self::METRIC_DURATION_DELETED_MSEC => self::METRIC_DURATION_DELETED_MSEC,
		self::METRIC_DURATION_TOTAL_MSEC => self::METRIC_DURATION_TOTAL_MSEC,
		self::METRIC_USERS_ADDED => self::METRIC_USERS_ADDED,
		self::METRIC_USERS_DELETED => self::METRIC_USERS_DELETED,
		self::METRIC_USERS_TOTAL => self::METRIC_USERS_TOTAL,
		self::METRIC_VIEW_PERIOD_PLAY_TIME => self::METRIC_VIEW_PERIOD_PLAY_TIME,
		self::METRIC_BUFFER_TIME_RATIO => self::METRIC_BUFFER_TIME_RATIO,
		self::METRIC_AVG_BITRATE => self::METRIC_AVG_BITRATE,
		self::METRIC_AVG_VIEW_BITRATE => self::METRIC_AVG_VIEW_BITRATE, 
		self::METRIC_SUM_PRICE => self::METRIC_SUM_PRICE,
		self::METRIC_VIEW_BUFFER_TIME_SEC => self::METRIC_VIEW_BUFFER_TIME_SEC,
		self::METRIC_VIEW_PLAY_TIME_SEC => self::METRIC_VIEW_PLAY_TIME_SEC,
		self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB => self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB,
		self::METRIC_UNIQUE_CONTRIBUTORS => self::METRIC_UNIQUE_CONTRIBUTORS,
	);

	//global transform
	protected static $transform_metrics = array(
		self::METRIC_UNIQUE_ENTRIES => 'floor',
		self::METRIC_UNIQUE_USERS => 'floor',
		self::METRIC_UNIQUE_CONTRIBUTORS => 'floor',
	);

	protected static $transform_time_dimensions = array(
		self::GRANULARITY_HOUR => array('kKavaReportsMgr', 'timestampToHourId'),
		self::GRANULARITY_DAY => array('kKavaReportsMgr', 'timestampToDateId'),
		self::GRANULARITY_MONTH => array('kKavaReportsMgr', 'timestampToMonthId')
	);

	protected static $granularity_mapping = array(
		self::GRANULARITY_DAY => 'P1D',
		self::GRANULARITY_MONTH => 'P1M',
		self::GRANULARITY_HOUR => 'PT1H',
		self::GRANULARITY_THIRTY_MINUTE => 'PT30M',
		self::GRANULARITY_MINUTE => 'PT1M',
	);

	protected static $non_linear_metrics = array(
		self::METRIC_AVG_PLAY_TIME => true,
		self::METRIC_PLAYER_IMPRESSION_RATIO => true,
		self::METRIC_PLAYTHROUGH_RATIO => true,
		self::METRIC_AVG_DROP_OFF => true,
		self::METRIC_UNIQUE_ENTRIES => true,
		self::METRIC_UNIQUE_USERS => true,
		self::METRIC_BUFFER_TIME_RATIO => true,
		self::METRIC_AVG_BITRATE => true,
		self::METRIC_UNIQUE_CONTRIBUTORS => true,
	);

	protected static $multi_value_dimensions = array(
		self::DIMENSION_CATEGORIES
	);

	protected static $php_timezone_names = array(
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
	
	protected static $druid_timezone_names = array(
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

	protected static $error_ids = array(
		'Unknown' => true,
		'Error' => true,
	);
	
	protected static $aggregations_def = array();
	protected static $metrics_def = array();
	protected static $headers_to_metrics = array();
	protected static $custom_reports = null;
	
	/// init functions
	protected static function getFieldRatioPostAggr($agg_name, $field1, $field2)
	{
		return self::getArithmeticPostAggregator($agg_name, '/', array(
			self::getFieldAccessPostAggregator($field1),
			self::getFieldAccessPostAggregator($field2)));
	}

	protected static function getConstantRatioPostAggr($agg_name, $field, $const)
	{
		return self::getArithmeticPostAggregator(
			$agg_name, '/', array(
				self::getFieldAccessPostAggregator($field),
				self::getConstantPostAggregator('c', $const)));
	}
	
	protected static function getConstantFactorPostAggr($agg_name, $field, $const)
	{
		return self::getArithmeticPostAggregator(
			$agg_name, '*', array(
				self::getFieldAccessPostAggregator($field),
				self::getConstantPostAggregator('c', $const)));
	}
	
	protected static function init()
	{
		if (self::$metrics_def)
		{
			return;
		}
		
		// count aggregators
		self::$aggregations_def[self::METRIC_PLAYTHROUGH] = self::getFilteredAggregator(
			self::getInFilter(self::DIMENSION_EVENT_TYPE, self::$playthrough_event_types),
			self::getLongSumAggregator(self::METRIC_PLAYTHROUGH, self::METRIC_COUNT));
		
		foreach (self::$event_type_count_aggrs as $event_type)
		{
			self::$aggregations_def[$event_type] = self::getFilteredAggregator(
				self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, $event_type),
				self::getLongSumAggregator($event_type, self::METRIC_COUNT)); 
		}
		
		// delta aggregators
		self::$aggregations_def[self::METRIC_COUNT_TOTAL] = 
			self::getLongSumAggregator(self::METRIC_COUNT_TOTAL, self::METRIC_DELTA);

		self::$aggregations_def[self::METRIC_COUNT_TOTAL_ALL_TIME] = 
			self::getLongSumAggregator(self::METRIC_COUNT_TOTAL_ALL_TIME, self::METRIC_DELTA);

		foreach (self::$media_type_count_aggrs as $media_type)
		{
			self::$aggregations_def[$media_type] = self::getFilteredAggregator(
				self::getSelectorFilter(self::DIMENSION_MEDIA_TYPE, $media_type),
				self::getLongSumAggregator($media_type, self::METRIC_DELTA)); 
		}

		$user_type_metrics = array(
			self::METRIC_COUNT_UGC => 'User', 
			self::METRIC_COUNT_ADMIN => 'Admin');
		foreach ($user_type_metrics as $metric => $value)
		{
			self::$aggregations_def[$metric] = self::getFilteredAggregator(
				self::getSelectorFilter(self::DIMENSION_USER_TYPE, $value),
				self::getLongSumAggregator($metric, self::METRIC_DELTA));
		}
		
		// delta aggregations
		$delta_metrics = array(
			array(self::METRIC_SIZE_BYTES, 	self::METRIC_STORAGE_SIZE_BYTES, 	self::METRIC_SIZE_ADDED_BYTES, 		self::METRIC_SIZE_DELETED_BYTES		),
			array(self::METRIC_DURATION_SEC,self::METRIC_DURATION_SEC, 			self::METRIC_DURATION_ADDED_SEC, 	self::METRIC_DURATION_DELETED_SEC	),
			array(self::METRIC_COUNT, 		self::METRIC_ENTRIES_TOTAL, 		self::METRIC_ENTRIES_ADDED, 		self::METRIC_ENTRIES_DELETED		),
			array(self::METRIC_COUNT, 		self::METRIC_USERS_TOTAL, 			self::METRIC_USERS_ADDED, 			self::METRIC_USERS_DELETED			),
		);

		foreach ($delta_metrics as $metrics)
		{
			list($base_metric, $total_metric, $added_metric, $deleted_metric) = $metrics;

			self::$aggregations_def[$total_metric] = self::getFilteredAggregator(
				self::getInFilter(self::DIMENSION_EVENT_TYPE, array(
					self::EVENT_TYPE_STATUS, 
					self::EVENT_TYPE_PHYSICAL_ADD,
					self::EVENT_TYPE_PHYSICAL_DELETE,
					self::EVENT_TYPE_LOGICAL_ADD,
					self::EVENT_TYPE_LOGICAL_DELETE,
				)),
				self::getLongSumAggregator(
					$total_metric, 
					$base_metric == self::METRIC_COUNT ? self::METRIC_DELTA : $base_metric));

			self::$aggregations_def[$added_metric] = self::getFilteredAggregator(
				self::getInFilter(self::DIMENSION_EVENT_TYPE, array(
					self::EVENT_TYPE_STATUS, 
					self::EVENT_TYPE_PHYSICAL_ADD,
					self::EVENT_TYPE_LOGICAL_ADD
				)),
				self::getLongSumAggregator($added_metric, $base_metric));

			self::$aggregations_def[$deleted_metric] = self::getFilteredAggregator(
				self::getInFilter(self::DIMENSION_EVENT_TYPE, array(
					self::EVENT_TYPE_PHYSICAL_DELETE,
					self::EVENT_TYPE_LOGICAL_DELETE
				)),
				self::getLongSumAggregator($deleted_metric, $base_metric));
		}

		// other aggregators
		self::$aggregations_def[self::METRIC_QUARTILE_PLAY_TIME_SEC] = self::getFilteredAggregator(
			self::getInFilter(self::DIMENSION_EVENT_TYPE, self::$playthrough_event_types), 
			self::getLongSumAggregator(self::METRIC_QUARTILE_PLAY_TIME_SEC, self::METRIC_PLAY_TIME_SUM));

		self::$aggregations_def[self::METRIC_VIEW_PERIOD_PLAY_TIME_SEC] = self::getFilteredAggregator(
			self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW_PERIOD), 
			self::getLongSumAggregator(self::METRIC_VIEW_PERIOD_PLAY_TIME_SEC, self::METRIC_PLAY_TIME_SUM));
		
		self::$aggregations_def[self::METRIC_VIEW_BUFFER_TIME_SEC] = self::getFilteredAggregator(
			self::getInFilter(self::DIMENSION_EVENT_TYPE, array(
				self::EVENT_TYPE_VIEW,				// realtime
				self::EVENT_TYPE_VIEW_PERIOD)),		// historical 
			self::getDoubleSumAggregator(self::METRIC_VIEW_BUFFER_TIME_SEC, self::METRIC_BUFFER_TIME_SEC));

		self::$aggregations_def[self::METRIC_BITRATE_SUM] = self::getFilteredAggregator(
			self::getAndFilter(array(
				self::getInFilter(self::DIMENSION_EVENT_TYPE, array(
					self::EVENT_TYPE_VIEW,				// realtime
					self::EVENT_TYPE_VIEW_PERIOD)),		// historical 
				self::getSelectorFilter(self::DIMENSION_HAS_BITRATE, 1))),
			self::getLongSumAggregator(self::METRIC_BITRATE_SUM, self::METRIC_BITRATE_SUM));

		self::$aggregations_def[self::METRIC_BITRATE_COUNT] = self::getFilteredAggregator(
			self::getAndFilter(array(
				self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW_PERIOD),
				self::getSelectorFilter(self::DIMENSION_HAS_BITRATE, 1))),
			self::getLongSumAggregator(self::METRIC_BITRATE_COUNT, self::METRIC_BITRATE_COUNT));

		self::$aggregations_def[self::METRIC_VIEW_BITRATE_COUNT] = self::getFilteredAggregator(
			self::getAndFilter(array(
				self::getSelectorFilter(self::DIMENSION_EVENT_TYPE, self::EVENT_TYPE_VIEW),
				self::getSelectorFilter(self::DIMENSION_HAS_BITRATE, 1))),
			self::getLongSumAggregator(self::METRIC_VIEW_BITRATE_COUNT, self::METRIC_COUNT));

		self::$aggregations_def[self::METRIC_UNIQUE_ENTRIES] = self::getCardinalityAggregator(
			self::METRIC_UNIQUE_ENTRIES, 
			array(self::DIMENSION_ENTRY_ID));
		
		self::$aggregations_def[self::METRIC_UNIQUE_USERS] = self::getHyperUniqueAggregator(
			self::METRIC_UNIQUE_USERS, 
			self::METRIC_UNIQUE_USER_IDS);

		self::$aggregations_def[self::METRIC_UNIQUE_CONTRIBUTORS] = self::getCardinalityAggregator(
			self::METRIC_UNIQUE_CONTRIBUTORS,
			array(self::DIMENSION_KUSER_ID));

		self::$aggregations_def[self::METRIC_BANDWIDTH_SIZE_BYTES] = self::getLongSumAggregator(
			self::METRIC_BANDWIDTH_SIZE_BYTES, self::METRIC_SIZE_BYTES);
		
		self::$aggregations_def[self::METRIC_FLAVOR_SIZE_BYTES] = self::getFilteredAggregator(
			self::getSelectorFilter(self::DIMENSION_STATUS, 'Success'),
			self::getLongSumAggregator(
				self::METRIC_FLAVOR_SIZE_BYTES, self::METRIC_FLAVOR_SIZE_BYTES));

		self::$aggregations_def[self::METRIC_ORIGIN_BANDWIDTH_SIZE_BYTES] = self::getFilteredAggregator(
			self::getSelectorFilter(self::DIMENSION_TYPE, 'Origin'),
			self::getLongSumAggregator(
				self::METRIC_ORIGIN_BANDWIDTH_SIZE_BYTES, self::METRIC_SIZE_BYTES));

		// Note: metrics that have post aggregations are defined below, any metric that
		//		is not explicitly set on $metrics_def is assumed to be a simple aggregation
		
		// simple factor metrics
		self::$metrics_def[self::METRIC_QUARTILE_PLAY_TIME] = array(
			self::DRUID_AGGR => array(self::METRIC_QUARTILE_PLAY_TIME_SEC),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_QUARTILE_PLAY_TIME_SEC, '60'));

		self::$metrics_def[self::METRIC_VIEW_PERIOD_PLAY_TIME] = array(
			self::DRUID_AGGR => array(self::METRIC_VIEW_PERIOD_PLAY_TIME_SEC),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_VIEW_PERIOD_PLAY_TIME, self::METRIC_VIEW_PERIOD_PLAY_TIME_SEC, '60'));
		
		self::$metrics_def[self::METRIC_DURATION_TOTAL_MSEC] = array(
			self::DRUID_AGGR => array(self::METRIC_DURATION_SEC),
			self::DRUID_POST_AGGR => self::getConstantFactorPostAggr(
				self::METRIC_DURATION_TOTAL_MSEC, self::METRIC_DURATION_SEC, '1000'));
		
		self::$metrics_def[self::METRIC_BANDWIDTH_SIZE_MB] = array(
			self::DRUID_AGGR => array(self::METRIC_BANDWIDTH_SIZE_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_BANDWIDTH_SIZE_MB, self::METRIC_BANDWIDTH_SIZE_BYTES, '1048576'));

		self::$metrics_def[self::METRIC_BANDWIDTH_SIZE_KB] = array(
			self::DRUID_AGGR => array(self::METRIC_BANDWIDTH_SIZE_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_BANDWIDTH_SIZE_KB, self::METRIC_BANDWIDTH_SIZE_BYTES, '1024'));
		
		self::$metrics_def[self::METRIC_TRANSCODING_SIZE_MB] = array(
			self::DRUID_AGGR => array(self::METRIC_FLAVOR_SIZE_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_TRANSCODING_SIZE_MB, self::METRIC_FLAVOR_SIZE_BYTES, '1048576'));

		self::$metrics_def[self::METRIC_STORAGE_TOTAL_MB] = array(
			self::DRUID_AGGR => array(self::METRIC_STORAGE_SIZE_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_STORAGE_TOTAL_MB, self::METRIC_STORAGE_SIZE_BYTES, '1048576'));

		self::$metrics_def[self::METRIC_STORAGE_ADDED_MB] = array(
			self::DRUID_AGGR => array(self::METRIC_SIZE_ADDED_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_STORAGE_ADDED_MB, self::METRIC_SIZE_ADDED_BYTES, '1048576'));

		self::$metrics_def[self::METRIC_STORAGE_DELETED_MB] = array(
			self::DRUID_AGGR => array(self::METRIC_SIZE_DELETED_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
			self::METRIC_STORAGE_DELETED_MB, self::METRIC_SIZE_DELETED_BYTES, '-1048576'));	// size is negative for delete events
				
		self::$metrics_def[self::METRIC_DURATION_ADDED_MSEC] = array(
			self::DRUID_AGGR => array(self::METRIC_DURATION_ADDED_SEC),
			self::DRUID_POST_AGGR => self::getConstantFactorPostAggr(
				self::METRIC_DURATION_ADDED_MSEC, self::METRIC_DURATION_ADDED_SEC, '1000'));

		self::$metrics_def[self::METRIC_DURATION_DELETED_MSEC] = array(
			self::DRUID_AGGR => array(self::METRIC_DURATION_DELETED_SEC),
			self::DRUID_POST_AGGR => self::getConstantFactorPostAggr(
				self::METRIC_DURATION_DELETED_MSEC, self::METRIC_DURATION_DELETED_SEC, '-1000'));	// duration is negative for delete events	

		self::$metrics_def[self::METRIC_VIEW_PLAY_TIME_SEC] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_VIEW),
			self::DRUID_POST_AGGR => self::getConstantFactorPostAggr(
				self::METRIC_VIEW_PLAY_TIME_SEC, self::EVENT_TYPE_VIEW, '10'));	
		
		// field ratio metrics
		self::$metrics_def[self::METRIC_PLAYTHROUGH_RATIO] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_100),
			self::DRUID_POST_AGGR => self::getFieldRatioPostAggr(
				self::METRIC_PLAYTHROUGH_RATIO,
				self::EVENT_TYPE_PLAYTHROUGH_100,
				self::EVENT_TYPE_PLAY));
		
		self::$metrics_def[self::METRIC_PLAYER_IMPRESSION_RATIO] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::DRUID_POST_AGGR => self::getFieldRatioPostAggr(
				self::METRIC_PLAYER_IMPRESSION_RATIO,
				self::EVENT_TYPE_PLAY,
				self::EVENT_TYPE_PLAYER_IMPRESSION));

		self::$metrics_def[self::METRIC_BUFFER_TIME_RATIO] = array(
			self::DRUID_AGGR => array(self::METRIC_VIEW_PERIOD_PLAY_TIME_SEC, self::METRIC_VIEW_BUFFER_TIME_SEC),
			self::DRUID_POST_AGGR => self::getFieldRatioPostAggr(
				self::METRIC_BUFFER_TIME_RATIO,
				self::METRIC_VIEW_BUFFER_TIME_SEC,
				self::METRIC_VIEW_PERIOD_PLAY_TIME_SEC));

		self::$metrics_def[self::METRIC_AVG_BITRATE] = array(
			self::DRUID_AGGR => array(self::METRIC_BITRATE_SUM, self::METRIC_BITRATE_COUNT),
			self::DRUID_POST_AGGR => self::getFieldRatioPostAggr(
				self::METRIC_AVG_BITRATE,
				self::METRIC_BITRATE_SUM,
				self::METRIC_BITRATE_COUNT));

		self::$metrics_def[self::METRIC_AVG_VIEW_BITRATE] = array(
			self::DRUID_AGGR => array(self::METRIC_BITRATE_SUM, self::METRIC_VIEW_BITRATE_COUNT),
			self::DRUID_POST_AGGR => self::getFieldRatioPostAggr(
				self::METRIC_AVG_VIEW_BITRATE,
				self::METRIC_BITRATE_SUM,
				self::METRIC_VIEW_BITRATE_COUNT));

		self::$metrics_def[self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB] = array(
			self::DRUID_AGGR => array(self::METRIC_ORIGIN_BANDWIDTH_SIZE_BYTES),
			self::DRUID_POST_AGGR => self::getConstantRatioPostAggr(
				self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB, self::METRIC_ORIGIN_BANDWIDTH_SIZE_BYTES, '1048576'));

		// complex metrics
		self::$metrics_def[self::METRIC_AVG_PLAY_TIME] = array(
			self::DRUID_AGGR => array(self::METRIC_QUARTILE_PLAY_TIME_SEC, self::EVENT_TYPE_PLAY),
			self::DRUID_POST_AGGR => self::getArithmeticPostAggregator(
				self::METRIC_AVG_PLAY_TIME, '/', array(
					self::getConstantRatioPostAggr('subPlayTime', self::METRIC_QUARTILE_PLAY_TIME_SEC, '60'),
					self::getFieldAccessPostAggregator(self::EVENT_TYPE_PLAY))));
		
		self::$metrics_def[self::METRIC_AVG_DROP_OFF] = array(
			self::DRUID_AGGR => array(self::EVENT_TYPE_PLAY, self::METRIC_PLAYTHROUGH),
			self::DRUID_POST_AGGR => self::getArithmeticPostAggregator(
				self::METRIC_AVG_DROP_OFF, '/', array(
					self::getConstantRatioPostAggr('subDropOff', self::METRIC_PLAYTHROUGH, '4'),
					self::getFieldAccessPostAggregator(self::EVENT_TYPE_PLAY))));
		
		self::$aggregations_def[self::METRIC_SUM_PRICE] = self::getLongSumAggregator(
			self::METRIC_SUM_PRICE, self::METRIC_SUM_PRICE);

		self::$headers_to_metrics = array_flip(self::$metrics_to_headers);
	}
	
	/// time functions
	protected static function fixTimeZoneOffset($timezone_offset)
	{
		$timezone_offset = intval($timezone_offset);
		if (isset(self::$php_timezone_names[$timezone_offset]))
		{
			return $timezone_offset;
		}

		$timezone_offset = min(max($timezone_offset, -14 * 60), 12 * 60);
		return round($timezone_offset / 60) * 60;
	}

	protected static function getPhpTimezoneName($timezone_offset)
	{
		// Note: value must be set, since the offset already went through fixTimeZoneOffset
		return self::$php_timezone_names[$timezone_offset];
	}

	protected static function getPhpTimezone($timezone_offset)
	{
		$tz_name = self::getPhpTimezoneName($timezone_offset);
		return new DateTimeZone($tz_name);
	}
	
	protected static function getDruidTimezoneName($timezone_offset)
	{
		// Note: value must be set, since the offset already went through fixTimeZoneOffset
		return self::$druid_timezone_names[$timezone_offset];
	}
	
	protected static function getRelativeDateTime($days)
	{
		$result = new DateTime();
		if ($days < 0)
		{
			$days = -$days;
			$result->sub(new DateInterval("P{$days}D"));
		}
		else if ($days > 0)
		{ 
			$result->add(new DateInterval("P{$days}D"));
		}
		return $result;
	}

	protected static function isDateIdValid($date_id)
	{
		return strlen($date_id) >= 8 && preg_match('/^\d+$/D', substr($date_id, 0, 8));
	}

	protected static function dateIdToDate($date_id)
	{
		if (!self::isDateIdValid($date_id))
		{
			return null;
		}

		$year = substr($date_id, 0, 4);
		$month = substr($date_id, 4, 2);
		$day = substr($date_id, 6, 2);
		if (!checkdate($month, $day , $year))
		{
			return null;
		}

		return "$year-$month-$day";
	}

	protected static function dateIdToDateTime($date_id)
	{
		$year = substr($date_id, 0, 4);
		$month = substr($date_id, 4, 2);
		$day = substr($date_id, 6, 2);
		return new DateTime("$year-$month-$day");
	}
		
	protected static function dateIdToUnixtime($date_id)
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
	
	protected static function formatUnixtime($time)
	{
		return gmdate('Y-m-d\TH:i:s\Z', $time);
	}

	protected static function timestampToHourId($timestamp, $tz)
	{
		// hours are returned from druid query with the right offset so no need to change it
		$date = new DateTime($timestamp);
		return $date->format('YmdH');
	}

	protected static function timestampToDateId($timestamp, $tz)
	{
		$date = new DateTime($timestamp);
		$date->modify('12 hour');			// adding 12H in order to round to the nearest day
		$date->setTimezone($tz);
		return $date->format('Ymd');
	}

	protected static function timestampToMonthId($timestamp, $tz)
	{
		$date = new DateTime($timestamp);
		$date->modify('12 hour');			// adding 12H in order to round to the nearest day
		$date->setTimezone($tz);
		return $date->format('Ym');
	}

	protected static function getDateIdRange($from_day, $to_day)
	{
		$date = self::dateIdToDateTime($from_day);
		$interval = new DateInterval('P1D');

		$result = array();
		for (;;)
		{
			$cur = $date->format('Ymd');
			if (strcmp($cur, $to_day) > 0 || count($result) >= 5000)
			{
				break;
			}

			$result[] = $cur;
			$date->add($interval);
		}

		return $result;
	}

	protected static function getMonthIdRange($from_day, $to_day)
	{
		$date = self::dateIdToDateTime($from_day);
		$end_date = self::dateIdToDateTime($to_day);
		$end_month = $end_date->format('Ym');
		$interval = new DateInterval('P1M');

		$result = array();
		for (;;)
		{
			$cur = $date->format('Ym');
			if (strcmp($cur, $end_month) > 0 || count($result) >= 120)
			{
				break;
			}

			$result[] = $cur;
			$date->add($interval);
		}

		return $result;
	}

	/// common query functions
	protected static function shouldUseKava($partner_id, $report_type) 
	{
		if ($report_type < 0)	// kava custom reports
		{
			return true;
		}

		if (!isset(self::$reports_def[$report_type]))
		{
			return false;
		}

		return kKavaBase::isPartnerAllowed($partner_id, kKavaBase::VOD_DISABLED_PARTNERS);
	}
		
	protected static function toSafeId($name)
	{
		$name = strtoupper($name);
		$name = preg_replace('/[^\w]/', '_', $name);
		return $name;
	}

	protected static function getReportDef($report_type)
	{
		if ($report_type >= 0)
		{
			$report_def = self::$reports_def[$report_type];
		}
		else
		{
			$report_def = self::$custom_reports[-$report_type];
		}

		if (isset($report_def[self::REPORT_DIMENSION_MAP]))
		{
			$dimension_map = $report_def[self::REPORT_DIMENSION_MAP];
			$dimensions = array_unique($dimension_map);
			$report_def[self::REPORT_DIMENSION] = count($dimensions) == 1 ? reset($dimensions) : array_values($dimensions);
			$report_def[self::REPORT_DIMENSION_HEADERS] = array_keys($dimension_map);
		}

		if (isset($report_def[self::REPORT_DRILLDOWN_DIMENSION_MAP]))
		{
			$drilldown_dimension_map = $report_def[self::REPORT_DRILLDOWN_DIMENSION_MAP];
			$drilldown_dimensions = array_unique($drilldown_dimension_map);
			$report_def[self::REPORT_DRILLDOWN_DIMENSION] = count($drilldown_dimensions) == 1 ? reset($drilldown_dimensions) : array_values($drilldown_dimensions);
			$report_def[self::REPORT_DRILLDOWN_DIMENSION_HEADERS] = array_keys($drilldown_dimension_map);
		}

		if (isset($report_def[self::REPORT_JOIN_REPORTS]) && !isset($report_def[self::REPORT_COLUMN_MAP]) && !isset($report_def[self::REPORT_TABLE_MAP]))
		{
			$report_defs = $report_def[self::REPORT_JOIN_REPORTS];
			$metrics = array();
			foreach ($report_defs as $cur_report_def)
			{
				if (isset($cur_report_def[self::REPORT_METRICS]))
				{
					$metrics = array_merge($cur_report_def[self::REPORT_METRICS], $metrics);
				}
			}
			foreach ($metrics as $metric)
			{
				$report_map[] = self::$metrics_to_headers[$metric];
			}
			if (isset($report_map))
			{
				$report_def[self::REPORT_TABLE_MAP] = array_combine($report_map, $report_map);
			}
		}

		return $report_def;
	}
	
	protected static function getDimension($report_def, $object_ids)
	{
		if ($object_ids && array_key_exists(self::REPORT_DRILLDOWN_DIMENSION, $report_def))
		{
			return $report_def[self::REPORT_DRILLDOWN_DIMENSION];
		}

		return $report_def[self::REPORT_DIMENSION];
	}

	protected static function getMetrics($report_def)
	{
		return $report_def[self::REPORT_METRICS];
	}

	protected static function getFilterValues($filter, $dimension)
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

	protected static function getFilterIntervals($report_def, $input_filter)
	{
		$offset = self::fixTimeZoneOffset($input_filter->timeZoneOffset);
		$input_filter->timeZoneOffset = $offset;

		$report_interval = isset($report_def[self::REPORT_INTERVAL]) ? 
			$report_def[self::REPORT_INTERVAL] : 
			self::INTERVAL_START_TO_END;
		
		if ($input_filter->from_date && $input_filter->to_date &&
			!($input_filter->from_day && $input_filter->to_day))
		{
			switch ($report_interval)
			{
			case self::INTERVAL_START_TO_END:
				$from_date = self::formatUnixtime($input_filter->from_date);
				$to_date = self::formatUnixtime($input_filter->to_date);
				break;

			case self::INTERVAL_BASE_TO_START:
				$from_date = self::BASE_TIMESTAMP;
				$to_date = self::formatUnixtime($input_filter->from_date);
				break;

			case self::INTERVAL_BASE_TO_END:
				$from_date = self::BASE_TIMESTAMP;
				$to_date = self::formatUnixtime($input_filter->to_date);
				break;
			}

			return array($from_date . '/' . $to_date);
		}

		$timezone_offset = sprintf('%s%02d:%02d', 
			$offset <= 0 ? '+' : '-', 
			intval(abs($offset) / 60), abs($offset) % 60);
		
		switch ($report_interval)
		{
		case self::INTERVAL_START_TO_END:
			$from_date = self::dateIdToDate($input_filter->from_day);
			$to_date = self::dateIdToDate($input_filter->to_day);
			break;
			
		case self::INTERVAL_BASE_TO_START:
			$to_date = self::dateIdToDateTime($input_filter->from_day);
			$to_date->sub(new DateInterval('P1D'));
			$to_date = $to_date->format('Y-m-d');
			$from_date = self::BASE_DATE_ID;
			break;

		case self::INTERVAL_BASE_TO_END:
			$from_date = self::BASE_DATE_ID;
			$to_date = self::dateIdToDate($input_filter->to_day);
			break;
					
		default:
			list($from_day, $to_day) = explode('/', $report_interval);
			$from_date = self::getRelativeDateTime($from_day)->format('Y-m-d');
			$to_date = self::getRelativeDateTime($to_day)->format('Y-m-d');
			break;
		}

		if (!$from_date || !$to_date || strcmp($to_date, $from_date) < 0)
		{
			$from_date = $to_date = '2010-01-01T00:00:00+00:00';
		}
		else
		{
			$from_date .= self::DAY_START_TIME . $timezone_offset;
			$to_date .= self::DAY_END_TIME . $timezone_offset;
		}
	
		return array($from_date . '/' . $to_date);
	}

	protected static function getKuserIds($report_def, $puser_ids, $partner_id)
	{
		$result = array();

		// leave error ids as is
		$puser_ids = explode(',', $puser_ids);
		foreach ($puser_ids as $index => $id)
		{
			if (isset(self::$error_ids[$id]))
			{
				unset($puser_ids[$index]);
				$result[] = $id;
			}
		}
		
		// extract ids from hashes
		$hash_conf = array();
		foreach (self::getEnrichDefs($report_def) as $enrich_def)
		{
			if ($enrich_def[self::REPORT_ENRICH_FUNC] == 'self::getUsersInfo' &&
				(!isset($enrich_def[self::REPORT_ENRICH_CONTEXT]['hash']) || $enrich_def[self::REPORT_ENRICH_CONTEXT]['hash']))
			{
				$hash_conf = kConf::get('kava_hash_user_ids', 'local', array());
				break;
			}
		}
		
		if (isset($hash_conf[$partner_id]))
		{
			foreach ($puser_ids as $index => $id)
			{
				$kuser_id = self::getKuserIdFromHash($id);
				if ($kuser_id === false)
				{
					continue;
				}
				
				unset($puser_ids[$index]);
				$result[] = strval($kuser_id);
			}
		}
		
		if (!$puser_ids)
		{
			return $result ? $result : array(kuser::KUSER_ID_THAT_DOES_NOT_EXIST);
		}
		
		// map remaining ids from db
		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);

		$c->addSelectColumn(kuserPeer::ID);
		
		$c->add(kuserPeer::PARTNER_ID, $partner_id);
		$c->add(kuserPeer::PUSER_ID, $puser_ids, Criteria::IN);

		$c->addDescendingOrderByColumn('(' . kuserPeer::STATUS . '=' . KuserStatus::ACTIVE . ')');		// first priority - active user
		$c->addDescendingOrderByColumn(kuserPeer::UPDATED_AT);	// second priority - recently updated
		
		kuserPeer::setUseCriteriaFilter(false);
		$stmt = kuserPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_NUM);
		kuserPeer::setUseCriteriaFilter(true);

		foreach ($rows as $row)
		{
			$result[] = strval($row[0]);
		}
		
		return $result ? $result : array(kuser::KUSER_ID_THAT_DOES_NOT_EXIST); 
	}
	
	protected static function getCategoriesIds($categories, $partner_id)
	{
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);

		$c->addSelectColumn(categoryPeer::ID);

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(categoryPeer::PARTNER_ID, $partner_id);
		}
		$c->add(categoryPeer::FULL_NAME, explode(',', $categories), Criteria::IN);

		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$stmt = categoryPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_NUM);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		if (!count($rows))
		{
			return array(category::CATEGORY_ID_THAT_DOES_NOT_EXIST);
		}

		return array_map('reset', $rows);
	}

	protected static function getDruidFilter($partner_id, $report_def, $input_filter, $object_ids)
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
			$report_filter = $report_def[self::REPORT_FILTER];
			if (isset($report_filter[self::DRUID_DIMENSION]))
			{
				$report_filter = array($report_filter);
			}
			$druid_filter = array_merge($druid_filter, $report_filter);
		}
		
		$input_filter->addReportsDruidFilters($partner_id, $report_def, $druid_filter);
		//Calculating druid filter userIds uses core logic which we don't want to move to the filter
		if ($input_filter instanceof endUserReportsInputFilter && $input_filter->userIds != null)
		{
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_KUSER_ID,
				self::DRUID_VALUES => self::getKuserIds($report_def, $input_filter->userIds, $partner_id),
			);
		}

		if ($input_filter->categories)
		{
			$category_ids = self::getCategoriesIds($input_filter->categories, $partner_id);
			$druid_filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_CATEGORIES,
				self::DRUID_VALUES => $category_ids
			);
		}

		$field_dim_map = array(
			'categoriesIds' => array(self::DRUID_DIMENSION => self::DIMENSION_CATEGORIES),
			'countries' => array(self::DRUID_DIMENSION => self::DIMENSION_LOCATION_COUNTRY),
			'playback_types' => array(self::DRUID_DIMENSION => self::DIMENSION_PLAYBACK_TYPE),
			'server_node_ids' => array(self::DRUID_DIMENSION => self::DIMENSION_SERVER_NODE_IDS),
			'custom_var1' => array(self::DRUID_DIMENSION => self::DIMENSION_CUSTOM_VAR1),
			'custom_var2' => array(self::DRUID_DIMENSION => self::DIMENSION_CUSTOM_VAR2),
			'custom_var3' => array(self::DRUID_DIMENSION => self::DIMENSION_CUSTOM_VAR3),
			'devices' => array(self::DRUID_DIMENSION => self::DIMENSION_DEVICE),
			'regions' => array(self::DRUID_DIMENSION => self::DIMENSION_LOCATION_REGION),
			'os_families' => array(self::DRUID_DIMENSION => self::DIMENSION_OS_FAMILY),
			'browsers_families' => array(self::DRUID_DIMENSION => self::DIMENSION_BROWSER_FAMILY),
			'cities' => array(self::DRUID_DIMENSION => self::DIMENSION_LOCATION_CITY),
			'media_types' => array(self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE),
			'source_types' => array(self::DRUID_DIMENSION => self::DIMENSION_SOURCE_TYPE),
		);

		foreach ($field_dim_map as $field => $field_filter_def)
		{
			$value = $input_filter->$field;
			if (is_null($value))
			{
				continue;
			}

			$values = explode(',', $value);
			$druid_filter[] = array(
				self::DRUID_DIMENSION => $field_filter_def[self::DRUID_DIMENSION],
				self::DRUID_VALUES => $values
			);
		}

		$entry_ids_from_db = array();
		if ($input_filter->keywords)
		{
			$entry_filter = new entryFilter();
			$entry_filter->setPartnerSearchScope($partner_id);

			if($input_filter->search_in_tags)
			{
				$entry_filter->set('_free_text', $input_filter->keywords);
			}
			else
			{
				$entry_filter->set('_like_admin_tags', $input_filter->keywords);
			}

			$c = KalturaCriteria::create(entryPeer::OM_CLASS);
			$entry_filter->attachToCriteria($c);
			$c->applyFilters();

			$entry_ids_from_db = $c->getFetchedIds();

			if ($c->getRecordsCount() > count($entry_ids_from_db))
			{
				throw new kCoreException('Search is to general', kCoreException::SEARCH_TOO_GENERAL);
			}

			if (!count($entry_ids_from_db))
			{
				$entry_ids_from_db[] = entry::ENTRY_ID_THAT_DOES_NOT_EXIST;
			}
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

	protected static function getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, $granularity, $filter_metrics = null)
	{
		$report_def = array(
			self::DRUID_DATASOURCE => $data_source ? $data_source : self::DATASOURCE_HISTORICAL,
			self::DRUID_INTERVALS => $intervals,
			self::DRUID_GRANULARITY => $granularity,
			self::DRUID_AGGR => array(),
			self::DRUID_POST_AGGR => array(),
		);

		if (kConf::hasParam('kava_top_priority_client_tags'))
		{
			$priority_tags = kConf::get('kava_top_priority_client_tags');
			$client_tag = kCurrentContext::$client_lang;
			
			foreach ($priority_tags as $tag)
			{
				if (strpos($client_tag, $tag) === 0)
				{
					$report_def[self::DRUID_CONTEXT] = array(self::DRUID_PRIORITY => self::CLIENT_TAG_PRIORITY);
					break;
				}
			}
		}
		
		// aggregations / post aggregations
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
				{
					continue;
				}

				$report_def[self::DRUID_AGGR][] = self::$aggregations_def[$aggr];
			}
			
			if (array_key_exists(self::DRUID_POST_AGGR, $metric_aggr))
			{
				$metric_post_aggr = $metric_aggr[self::DRUID_POST_AGGR];
				if (!in_array($metric_post_aggr, $report_def[self::DRUID_POST_AGGR]))
				{
					$report_def[self::DRUID_POST_AGGR][] = $metric_post_aggr;
				}
			}
		}

		// event types
		$event_types = array();
		if (!$filter_metrics)
		{
			$filter_metrics = $metrics;
		}
		foreach ($filter_metrics as $metric)
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
		 		if (!isset(self::$aggregations_def[$aggr][self::DRUID_FILTER]))
		 		{
		 			continue;
		 		}
		 		
		 		$aggr_filter = self::$aggregations_def[$aggr][self::DRUID_FILTER];
		 		if (!isset($aggr_filter[self::DRUID_DIMENSION]) ||
		 			$aggr_filter[self::DRUID_DIMENSION] != self::DIMENSION_EVENT_TYPE)
		 		{
		 			continue;
		 		}
		 		
				if (isset($aggr_filter[self::DRUID_VALUE]))
				{
					$event_types[] = $aggr_filter[self::DRUID_VALUE];
				}
				else if (isset($aggr_filter[self::DRUID_VALUES]))
				{
					$event_types = array_merge($event_types, $aggr_filter[self::DRUID_VALUES]);
				}
		 	}
		}

		if (count($event_types))
		{
			$filter[] = array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array_values(array_unique($event_types)));
		}

		$filter_def = array();
		foreach ($filter as $cur_filter)
		{
			$filter_def[] = self::getInFilter(
				$cur_filter[self::DRUID_DIMENSION], 
				$cur_filter[self::DRUID_VALUES]);
		}
		
		$report_def[self::DRUID_FILTER] = array(
			self::DRUID_TYPE => 'and',
			self::DRUID_FIELDS => $filter_def);

		return $report_def;
	}

	protected static function getTopReport($data_source, $partner_id, $intervals, $metrics, $dimensions, $filter, $order_by, $order_dir, $threshold, $filter_metrics = null)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, self::DRUID_GRANULARITY_ALL, $filter_metrics);

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
		$report_def[self::DRUID_METRIC] = array(
			self::DRUID_TYPE => $order_type,
			self::DRUID_METRIC => $order_by);
		$report_def[self::DRUID_THRESHOLD] = $threshold;

		return $report_def;
	}

	protected static function getSearchReport($data_source, $partner_id, $intervals, $dimensions, $filter)
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

	protected static function getTimeSeriesReport($data_source, $partner_id, $intervals, $granularity, $metrics, $filter)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, $granularity);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TIMESERIES;
		if (!isset($report_def[self::DRUID_CONTEXT]))
		{
			$report_def[self::DRUID_CONTEXT] = array();
		}
		$report_def[self::DRUID_CONTEXT][self::DRUID_SKIP_EMPTY_BUCKETS] = 'true';
		return $report_def;
	}

	protected static function getDimCardinalityReport($data_source, $partner_id, $intervals, $dimension, $filter, $filter_metrics)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, array(), $filter, self::DRUID_GRANULARITY_ALL, $filter_metrics);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_TIMESERIES;
		$report_def[self::DRUID_AGGR][] = array(
			self::DRUID_TYPE => self::DRUID_CARDINALITY,
			self::DRUID_NAME => self::METRIC_CARDINALITY,
			self::DRUID_FIELDS => is_array($dimension) ? $dimension : array($dimension));
		return $report_def;
	}

	protected static function getGroupByReport($data_source, $partner_id, $intervals, $granularity, $dimensions, $metrics, $filter, $pageSize = 0)
	{
		$report_def = self::getBaseReportDef($data_source, $partner_id, $intervals, $metrics, $filter, $granularity);
		$report_def[self::DRUID_QUERY_TYPE] = self::DRUID_GROUP_BY;
		$report_def[self::DRUID_DIMENSIONS] = $dimensions;
		return $report_def;
	}

	/// graph functions
	protected static function getGranularityDef($granularity, $timezone_offset)
	{
		if (!isset(self::$granularity_mapping[$granularity]))
		{
			return self::DRUID_GRANULARITY_ALL;
		}

		$granularity_def = array(
			self::DRUID_TYPE => self::DRUID_GRANULARITY_PERIOD,
			self::DRUID_GRANULARITY_PERIOD => self::$granularity_mapping[$granularity],
			self::DRUID_TIMEZONE => self::getDruidTimezoneName($timezone_offset)
		);
		return $granularity_def;
	}
	
	protected static function getGraphsByDateId($result, $graph_metrics_to_headers, $tz_offset, $transform)
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

			$date = $row[self::DRUID_TIMESTAMP];
			if ($transform)
			{
				$date = call_user_func($transform, $date, $tz);
			}

			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graphs[$header][$date] = self::getMetricValue($row_data, $column);
			}
		}
		return $graphs;
	}

	protected static function getAssociativeMultiGraphsByDateId($result, $multiline_column, $graph_metrics_to_headers, $tz_offset)
	{
		$tz = self::getPhpTimezone($tz_offset);

		$graphs = array();

		unset($graph_metrics_to_headers[$multiline_column]);

		foreach ($result as $row)
		{
			$row_data = $row[self::DRUID_EVENT];

			$date = self::timestampToDateId($row[self::DRUID_TIMESTAMP], $tz);
			$multiline_val = $row_data[$multiline_column];
			
			if (!isset($graphs[$multiline_val]))
			{
				$graphs[$multiline_val] = array();
				foreach ($graph_metrics_to_headers as $column => $header)
				{
					$graphs[$multiline_val][$header] = array();
				}
			}
			
			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graphs[$multiline_val][$header][$date] = self::getMetricValue($row_data, $column);
			}
		}
		return $graphs;
	}
	
	protected static function getMultiGraphsByDateId ($result, $multiline_column, $graph_metrics_to_headers, $tz_offset)
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
			$multiline_val = $row_data[$multiline_column];

			foreach ($graph_metrics_to_headers as $column => $header)
			{
				if (isset($graphs[$header][$date]))
				{
					$graphs[$header][$date] .=	',';
				}
				else
				{
					$graphs[$header][$date] = '';
				}

				$graphs[$header][$date] .= $multiline_val . ':' . self::getMetricValue($row_data, $column);
			}
		}
		return $graphs;
	}

	protected static function getMultiGraphsByColumnName ($result, $graph_metrics_to_headers, $dimension)
	{
		$graphs = array();

		foreach ($graph_metrics_to_headers as $column => $header)
		{
			$graphs[$header] = array();
		}

		foreach ($result as $row)
		{
			$row_data = $row[self::DRUID_EVENT];
			$dim_value = $row_data[$dimension];

			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graphs[$header][$dim_value] = self::getMetricValue($row_data, $column);
			}
		}
		return $graphs;
	}

	protected static function getGraphsByColumnName($result, $graph_metrics_to_headers, $type_str)
	{
		$graph = array();
		if (isset($result[0][self::DRUID_RESULT]))
		{
			$row_data = $result[0][self::DRUID_RESULT];
			foreach ($graph_metrics_to_headers as $column => $header)
			{
				$graph[$header] = self::getMetricValue($row_data, $column);
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

	protected static function getMetricValue($event, $column)
	{
		if (isset(self::$transform_metrics[$column]))
		{
			return call_user_func(self::$transform_metrics[$column], $event[$column]);
		}
		return $event[$column];
	}

	protected static function getEnrichDefByHeader($report_def, $header)
	{
		$enrich_defs = self::getEnrichDefs($report_def);
		foreach ($enrich_defs as $enrich_def)
		{
			if (is_array($enrich_def[self::REPORT_ENRICH_OUTPUT]))
			{
				continue;
			}
			if ($header == $enrich_def[self::REPORT_ENRICH_OUTPUT])
			{
				return $enrich_def;
			}
		}
		return null;
	}

	protected static function getSimpleGraphImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids = null)
	{
		if (!isset($report_def[self::REPORT_GRAPH_METRICS]))
		{
			throw new Exception('unsupported query - report has no metrics');
		}
		
		$start = microtime(true);
		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$metrics = $report_def[self::REPORT_GRAPH_METRICS];
		$intervals = self::getFilterIntervals($report_def, $input_filter);
		$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, $object_ids);

		// get the granularity
		$granularity = isset($report_def[self::REPORT_GRANULARITY]) ? 
			$report_def[self::REPORT_GRANULARITY] : self::getGranularityFromFilterInterval($input_filter->interval);
		
		$graph_type = isset($report_def[self::REPORT_GRAPH_TYPE]) ? $report_def[self::REPORT_GRAPH_TYPE] : self::GRAPH_BY_DATE_ID;
		switch ($graph_type)
		{
		case self::GRAPH_MULTI_BY_DATE_ID:
			if (!$object_ids)
			{
				break;
			}
			// fallthrough
			
		case self::GRAPH_BY_NAME:
		case self::GRAPH_MULTI_BY_NAME:
			$granularity = self::DRUID_GRANULARITY_ALL;			
			break;
		}
		
		$granularity_def = self::getGranularityDef($granularity, $input_filter->timeZoneOffset);

		// run the query
		switch ($graph_type)
		{
		case self::GRAPH_ASSOC_MULTI_BY_DATE_ID:
		case self::GRAPH_MULTI_BY_DATE_ID:
		case self::GRAPH_MULTI_BY_NAME:				
			$dimension = self::getDimension($report_def, $object_ids);
			$dimension = is_array($dimension) ? reset($dimension) : $dimension;
			if (isset($report_def[self::REPORT_DIMENSION_HEADERS]))
			{
				$header = reset($report_def[self::REPORT_DIMENSION_HEADERS]);
				$transform_enrich_def = self::getEnrichDefByHeader($report_def, $header);
			}
			else
			{
				$transform_enrich_def = null;
			}
			$query = self::getGroupByReport($data_source, $partner_id, $intervals, $granularity_def, array($dimension), $metrics, $druid_filter);
			break;
				
		default:
			$dimension = null;
			$transform_enrich_def = null;
			$query = self::getTimeSeriesReport($data_source, $partner_id, $intervals, $granularity_def, $metrics, $druid_filter);
			break;
		}
		$result = self::runQuery($query);
		KalturaLog::log('Druid returned [' . count($result) . '] rows');

		// parse the result
		foreach ($metrics as $column)
		{
			$graph_metrics_to_headers[$column] = self::$metrics_to_headers[$column];
		}

		if ($transform_enrich_def)
		{
			//collect dimensions to transform
			$values = array();
			foreach ($result as $row)
			{
				$dim_value = $row[self::DRUID_EVENT][$dimension];
				$values[$dim_value] = true;
			}

			//transform
			$enrich_context = isset($transform_enrich_def[self::REPORT_ENRICH_CONTEXT]) ? $transform_enrich_def[self::REPORT_ENRICH_CONTEXT] : null;
			$transform_map = call_user_func($transform_enrich_def[self::REPORT_ENRICH_FUNC], array_keys($values), $partner_id, $enrich_context);

			//update the result
			foreach ($result as &$row)
			{
				$dim_value = $row[self::DRUID_EVENT][$dimension];
				if (isset($transform_map[$dim_value]))
				{
					$row[self::DRUID_EVENT][$dimension] = $transform_map[$dim_value];
				}
			}
		}

		switch ($graph_type)
		{
		case self::GRAPH_ASSOC_MULTI_BY_DATE_ID:
			$result = self::getAssociativeMultiGraphsByDateId($result, $dimension, $graph_metrics_to_headers, $input_filter->timeZoneOffset);
			break;
			
		case self::GRAPH_MULTI_BY_DATE_ID:
			if (!$object_ids)
			{
				$result = self::getMultiGraphsByDateId($result, $dimension, $graph_metrics_to_headers, $input_filter->timeZoneOffset);
				break;
			}
			// fallthrough
			
		case self::GRAPH_MULTI_BY_NAME:
			$result = self::getMultiGraphsByColumnName($result, $graph_metrics_to_headers, $dimension);
			break;
			
		case self::GRAPH_BY_NAME:
			$result = self::getGraphsByColumnName($result, $graph_metrics_to_headers, $report_def[self::REPORT_GRAPH_NAME]); 
			break;
			
		default:
			$transform = isset(self::$transform_time_dimensions[$granularity]) ? self::$transform_time_dimensions[$granularity] : null;
			$result = self::getGraphsByDateId($result, $graph_metrics_to_headers, $input_filter->timeZoneOffset, $transform);
			break;
		}

		$end = microtime(true);
		KalturaLog::log('getGraph took [' . ($end - $start) . ']');

		return $result;
	}

	protected static function zeroFill(&$graphs, $dates)
	{
		foreach ($graphs as $name => $values)
		{
			$new_values = array();
			foreach ($dates as $date)
			{
				// Note: the != 0 is here to avoid returning '-0' that may come from druid 
				$new_values[$date] = isset($values[$date]) && $values[$date] != 0 ? $values[$date] : 0; 
			}
			
			$graphs[$name] = $new_values;
		}
	}
	
	protected static function getJoinGraphImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids)
	{
		$start = microtime(true);

		$report_defs = isset($report_def[self::REPORT_JOIN_REPORTS]) ?
			$report_def[self::REPORT_JOIN_REPORTS] :
			$report_def[self::REPORT_JOIN_GRAPHS];

		// get the graphs
		$result = array();
		$granularity = null;
		foreach ($report_defs as $cur_report_def)
		{
			if (!isset($cur_report_def[self::REPORT_GRAPH_METRICS]) ||
				isset($cur_report_def[self::REPORT_GRAPH_ACCUMULATE_FUNC]))
			{
				continue;
			}
			
			$cur_result = self::getSimpleGraphImpl(
				$partner_id,
				$cur_report_def,
				$input_filter,
				$object_ids);
			KalturaLog::debug('Graph - ' . print_r($cur_result, true));
			$result = array_merge($result, $cur_result);
			if (isset($cur_report_def[self::REPORT_GRANULARITY]))
			{
				$granularity = $cur_report_def[self::REPORT_GRANULARITY];
			}
		}

		if (!isset($granularity))
		{
			$granularity = self::getGranularityFromFilterInterval($input_filter->interval);
		}


		// zero fill
		if ($granularity == self::GRANULARITY_MONTH)
		{
			$dates = self::getMonthIdRange($input_filter->from_day, $input_filter->to_day);
		}
		else
		{
			$dates = self::getDateIdRange($input_filter->from_day, $input_filter->to_day);
		}
		self::zeroFill($result, $dates);
		
		// add accumulated graphs
		foreach ($report_defs as $cur_report_def)
		{
			if (!isset($cur_report_def[self::REPORT_GRAPH_ACCUMULATE_FUNC]))
			{
				continue;
			}
				
			$cur_report_def[self::REPORT_GRAPH_TYPE] = self::GRAPH_BY_NAME;
			$cur_report_def[self::REPORT_GRAPH_NAME] = 0;
			$cur_report_def[self::REPORT_GRANULARITY] = self::DRUID_GRANULARITY_ALL;
			$base_values = self::getSimpleGraphImpl(
				$partner_id,
				$cur_report_def,
				$input_filter,
				$object_ids);
			$base_values = $base_values[0];
			KalturaLog::debug('Base - ' . print_r($base_values, true));
			
			call_user_func_array($cur_report_def[self::REPORT_GRAPH_ACCUMULATE_FUNC], 
				array(&$result, $base_values, $dates));
		}
			
		// aggregate by the requested period
		if (isset($report_def[self::REPORT_GRAPH_AGGR_FUNC]))
		{
			$result = call_user_func_array($report_def[self::REPORT_GRAPH_AGGR_FUNC], array($input_filter->interval, $result, &$dates));
		}
		
		// add derived graphs
		if (isset($report_def[self::REPORT_GRAPH_FINALIZE_FUNC]))
		{
			call_user_func_array($report_def[self::REPORT_GRAPH_FINALIZE_FUNC], array(&$result, $dates));
		}
				
		KalturaLog::log('Result - ' . print_r($result, true));
				
		$end = microtime(true);
		KalturaLog::log('getGraph took [' . ($end - $start) . ']');
		
		return $result;
	}

	protected static function transposeArray($arr)
	{
		$result = array();
		foreach ($arr as $key => $subarr)
		{
			foreach ($subarr as $subkey => $subvalue)
			{
				if (!isset($result[$subkey]))
				{
					$result[$subkey] = array();
				}

				$result[$subkey][$key] = $subvalue;
			}
		}

		return $result;
	}

	protected static function getKeyedJoinGraphImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids)
	{
		$start = microtime(true);
		
		$report_defs = $report_def[self::REPORT_JOIN_GRAPHS];
		
		// get the graphs
		$result = array();
		$filler_graphs = array();
		foreach ($report_defs as $cur_report_def)
		{
			if (!isset($cur_report_def[self::REPORT_GRAPH_METRICS]) ||
				isset($cur_report_def[self::REPORT_GRAPH_ACCUMULATE_FUNC]))
			{
				continue;
			}
			
			$cur_report_def[self::REPORT_DIMENSION] = $report_def[self::REPORT_DIMENSION];
			$cur_report_def[self::REPORT_GRAPH_TYPE] = self::GRAPH_ASSOC_MULTI_BY_DATE_ID;
			$cur_result = self::getSimpleGraphImpl(
				$partner_id,
				$cur_report_def,
				$input_filter,
				$object_ids);
			KalturaLog::debug('Graph - ' . print_r($cur_result, true));
			
			foreach ($cur_result as $dim => $graphs)
			{
				$result[$dim] = array_merge(
					isset($result[$dim]) ? $result[$dim] : $filler_graphs,
					$graphs);
			}

			$missing_dims = array_diff_key($result, $cur_result);
			foreach ($cur_report_def[self::REPORT_GRAPH_METRICS] as $metric)
			{
				foreach ($missing_dims as $dim => $ignore)
				{
					$result[$dim][$metric] = array();
				}

				$filler_graphs[$metric] = array();
			}
		}

		$dates = self::getDateIdRange($input_filter->from_day, $input_filter->to_day);
		self::zeroFill($filler_graphs, $dates);
		foreach ($result as $dim => $ignore)
		{
			self::zeroFill($result[$dim], $dates);
		}
		
		// add accumulated graphs
		foreach ($report_defs as $cur_report_def)
		{
			if (!isset($cur_report_def[self::REPORT_GRAPH_ACCUMULATE_FUNC]))
			{
				continue;
			}
				
			$cur_report_def[self::REPORT_DIMENSION] = $report_def[self::REPORT_DIMENSION];
			$cur_report_def[self::REPORT_GRAPH_TYPE] = self::GRAPH_MULTI_BY_NAME;
			$cur_report_def[self::REPORT_GRANULARITY] = self::DRUID_GRANULARITY_ALL;
			$base_values = self::getSimpleGraphImpl(
				$partner_id,
				$cur_report_def,
				$input_filter,
				$object_ids);
			KalturaLog::debug('Base - ' . print_r($base_values, true));

			// swap the base values from [graph][dim] to [dim][graph]
			$base_values = self::transposeArray($base_values);

			foreach ($base_values as $dim => $cur_values)
			{
				if (!isset($result[$dim]))
				{
					$result[$dim] = $filler_graphs;
				}
			}

			foreach ($result as $dim => $ignore)
			{
				call_user_func_array($cur_report_def[self::REPORT_GRAPH_ACCUMULATE_FUNC], array(
					&$result[$dim], 
					isset($base_values[$dim]) ? $base_values[$dim] : array(), 
					$dates));
			}
		}
			
		foreach ($result as $dim => $ignore)
		{
			$temp_dates = $dates;
			
			// aggregate by the requested period
			if (isset($report_def[self::REPORT_GRAPH_AGGR_FUNC]))
			{
				$result[$dim] = call_user_func_array($report_def[self::REPORT_GRAPH_AGGR_FUNC], array($input_filter->interval, $result[$dim], &$temp_dates));
			}
			
			// add derived graphs
			if (isset($report_def[self::REPORT_GRAPH_FINALIZE_FUNC]))
			{
				call_user_func_array($report_def[self::REPORT_GRAPH_FINALIZE_FUNC], array(&$result[$dim], $temp_dates));
			}
		}
		
		KalturaLog::log('Result - ' . print_r($result, true));
				
		$end = microtime(true);
		KalturaLog::log('getGraph took [' . ($end - $start) . ']');
		
		return $result;
	}
	
	protected static function getGraphImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids = null)
	{
		if (isset($report_def[self::REPORT_JOIN_REPORTS]) || 
			isset($report_def[self::REPORT_JOIN_GRAPHS]))
		{
			$result = self::getJoinGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
		}
		else
		{
			$result = self::getSimpleGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
		}
		
		return $result;
	}

	protected static function reorderGraphs($map, $input)
	{
		$result = array();
		foreach ($map as $column => $metric)
		{
			if (isset($input[$metric]))
			{
				$result[$column] = $input[$metric];
			}
		}
	
		return $result;
	}
		
	public static function getGraph($partner_id, $report_type, reportsInputFilter $input_filter, $dimension = null, $object_ids = null)
	{
		if (!self::shouldUseKava($partner_id, $report_type))
		{
			return myReportsMgr::getGraph($partner_id, $report_type, $input_filter, $dimension, $object_ids);
		}
		
		self::init();
		
		$report_def = self::getReportDef($report_type);
		
		if (isset($report_def[self::REPORT_SKIP_PARTNER_FILTER]))
		{
			$partner_id = Partner::ADMIN_CONSOLE_PARTNER_ID;
		}
		
		// get the graphs
		$result = self::getGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
		
		// reorder
		$map = null;
		if (isset($report_def[self::REPORT_GRAPH_MAP]))
		{
			$map = $report_def[self::REPORT_GRAPH_MAP];
		}
		else if (isset($report_def[self::REPORT_COLUMN_MAP]))
		{
			$map = $report_def[self::REPORT_COLUMN_MAP];
		}

		if ($map)
		{
			$result = self::reorderGraphs($map, $result);
		}
		
		return $result;
	}
		
	/// usage graph functions
	protected static function addAggregatedStorageGraphs(&$graphs, $base_values, $dates)
	{
		$cur_value = reset($base_values);
		foreach ($dates as $date)
		{
			$cur_value += $graphs[self::METRIC_STORAGE_ADDED_MB][$date];
			$graphs[self::METRIC_AVERAGE_STORAGE_MB][$date] = $cur_value;
			$graphs[self::METRIC_PEAK_STORAGE_MB][$date] = $cur_value;
			$graphs[self::METRIC_LATEST_STORAGE_MB][$date] = $cur_value;
			$cur_value -= $graphs[self::METRIC_STORAGE_DELETED_MB][$date];
		}
	}

	protected static function addAggregatedStorageGraphsBaseToEnd(&$graphs, $base_values, $dates)
	{
		// convert to base -> start
		$new_base_values = array(
			self::METRIC_STORAGE_TOTAL_MB =>
				reset($base_values) - array_sum($graphs[self::METRIC_STORAGE_ADDED_MB]) + array_sum($graphs[self::METRIC_STORAGE_DELETED_MB]),
		);

		self::addAggregatedStorageGraphs($graphs, $new_base_values, $dates);
	}

	protected static function addAggregatedEntriesGraphs(&$graphs, $base_values, $dates)
	{
		if (isset($base_values[self::METRIC_ENTRIES_TOTAL]))
		{
			$cur_value = $base_values[self::METRIC_ENTRIES_TOTAL];
			foreach ($dates as $date)
			{
				$cur_value += $graphs[self::METRIC_ENTRIES_ADDED][$date];
				$graphs[self::METRIC_AVERAGE_ENTRIES][$date] = $cur_value;
				$graphs[self::METRIC_PEAK_ENTRIES][$date] = $cur_value;
				$graphs[self::METRIC_LATEST_ENTRIES][$date] = $cur_value;
				$cur_value -= $graphs[self::METRIC_ENTRIES_DELETED][$date];
			}
		}
		
		if (isset($base_values[self::METRIC_DURATION_TOTAL_MSEC]))
		{
			$cur_value = $base_values[self::METRIC_DURATION_TOTAL_MSEC];
			foreach ($dates as $date)
			{
				$cur_value += $graphs[self::METRIC_DURATION_ADDED_MSEC][$date];
				$graphs[self::METRIC_AVERAGE_DURATION_MSEC][$date] = $cur_value;
				$graphs[self::METRIC_PEAK_DURATION_MSEC][$date] = $cur_value;
				$graphs[self::METRIC_LATEST_DURATION_MSEC][$date] = $cur_value;
				$cur_value -= $graphs[self::METRIC_DURATION_DELETED_MSEC][$date];
			}
		}
	}

	protected static function addAggregatedEntriesGraphsBaseToEnd(&$graphs, $base_values, $dates)
	{
		// convert to base -> start
		$new_base_values = array();
		if (isset($base_values[self::METRIC_ENTRIES_TOTAL]))
		{
			$new_base_values[self::METRIC_ENTRIES_TOTAL] = 
				$base_values[self::METRIC_ENTRIES_TOTAL] - array_sum($graphs[self::METRIC_ENTRIES_ADDED]) + array_sum($graphs[self::METRIC_ENTRIES_DELETED]);
		}

		if (isset($base_values[self::METRIC_DURATION_TOTAL_MSEC]))
		{
			$new_base_values[self::METRIC_DURATION_TOTAL_MSEC] = 
				$base_values[self::METRIC_DURATION_TOTAL_MSEC] - array_sum($graphs[self::METRIC_DURATION_ADDED_MSEC]) + array_sum($graphs[self::METRIC_DURATION_DELETED_MSEC]);
		}

		self::addAggregatedEntriesGraphs($graphs, $new_base_values, $dates);
	}

	protected static function addAggregatedUsersGraphs(&$graphs, $base_values, $dates)
	{
		$cur_value = reset($base_values);
		foreach ($dates as $date)
		{
			$cur_value += $graphs[self::METRIC_USERS_ADDED][$date];
			$graphs[self::METRIC_AVERAGE_USERS][$date] = $cur_value;
			$graphs[self::METRIC_PEAK_USERS][$date] = $cur_value;
			$graphs[self::METRIC_LATEST_USERS][$date] = $cur_value;
			$cur_value -= $graphs[self::METRIC_USERS_DELETED][$date];
		}
	}
	
	protected static function getAverageAggregatedMonthly($graph)
	{
		// group by month
		$grouped = array();
		foreach ($graph as $date => $value)
		{
			$month = substr($date, 0, 6);
			if (!isset($grouped[$month]))
			{
				$grouped[$month] = array();
			}
			$grouped[$month][] = $value;
		}
			
		// sum the average of each month
		$value = 0;
		foreach ($grouped as $values)
		{
			$value += array_sum($values) / count($values);
		}
		
		return $value;
	}
		
	protected static function aggregateUsageDataAll($graphs)
	{
		$result = array();
		foreach ($graphs as $name => $values)
		{
			if (kString::beginsWith($name, 'average_'))
			{
				$value = $values ? array_sum($values) / count($values) : 0;
			}
			else if (kString::beginsWith($name, 'peak_'))
			{
				$value = $values ? max($values) : 0;
			}
			else if (kString::beginsWith($name, 'latest_'))
			{
				$value = end($values);
			}
			else if ($name == self::METRIC_BANDWIDTH_STORAGE_MB)
			{
				$value = $result[self::METRIC_BANDWIDTH_SIZE_MB] + $result[self::METRIC_AVERAGE_STORAGE_MB];
			}
			else
			{
				$value = array_sum($values);
			}
		
			$result[$name] = $value;
		}
		
		return $result;
	}
	
	protected static function aggregateUsageDataByMonth($graphs, &$dates)
	{
		// group by months
		$grouped = array();
		foreach ($dates as $date)
		{
			$month = substr($date, 0, 6);
			foreach ($graphs as $name => $values)
			{
				$grouped[$month][$name][$date] = $values[$date];
			}
		}
		
		// aggregate the months
		$result = array();
		foreach ($grouped as $month => $graphs)
		{
			foreach (self::aggregateUsageDataAll($graphs) as $header => $value)
			{
				$result[$header][$month] = $value;
			}
		}
		
		$dates = array_keys($grouped);
		
		return $result;
	}

	protected static function aggregateUsageData($interval, $graphs, &$dates)
	{
		switch ($interval)
		{
			case self::INTERVAL_MONTHS:
				return self::aggregateUsageDataByMonth($graphs, $dates);
					
			case self::INTERVAL_ALL:
				$dates = null;
				$result = self::aggregateUsageDataAll($graphs);
				if (isset($graphs[self::METRIC_AVERAGE_STORAGE_MB]))
				{
					// add average storage aggregated monthly
					$result[self::METRIC_AVERAGE_STORAGE_AGGR_MONTHLY_MB] = 
						self::getAverageAggregatedMonthly($graphs[self::METRIC_AVERAGE_STORAGE_MB]);
				}

				foreach ($result as $key => $value)
				{
					$result[$key] = array($value);
				}
				return $result;
				
			// Note: no need to do anything for 'days', input data is already per day
		}
		
		return $graphs;
	}
	
	protected static function addCombinedUsageGraph(&$result, $dates)
	{
		if (!$dates)
		{
			$result[self::METRIC_BANDWIDTH_STORAGE_MB] = array(
				reset($result[self::METRIC_BANDWIDTH_SIZE_MB]) +
				reset($result[self::METRIC_AVERAGE_STORAGE_MB]));
			
			$result[self::METRIC_BANDWIDTH_STORAGE_AGGR_MONTHLY_MB] = array(
				reset($result[self::METRIC_BANDWIDTH_SIZE_MB]) +
				reset($result[self::METRIC_AVERAGE_STORAGE_AGGR_MONTHLY_MB]));
			return;
		}
		
		foreach ($dates as $date)
		{
			$result[self::METRIC_BANDWIDTH_STORAGE_MB][$date] =
				$result[self::METRIC_BANDWIDTH_SIZE_MB][$date] +
				$result[self::METRIC_AVERAGE_STORAGE_MB][$date];
		}
	}

	/// table enrich functions
	protected static function getEntriesNames($ids, $partner_id)
	{
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		$c->addSelectColumn(entryPeer::ID);
		$c->addSelectColumn(entryPeer::NAME);

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(entryPeer::PARTNER_ID, $partner_id);
		}
		$c->add(entryPeer::ID, $ids, Criteria::IN);

		entryPeer::setUseCriteriaFilter(false);
		$stmt = entryPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
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

	protected static function forEachKeys($keys, $partner_id, $enrich_context)
	{
		$result = array();
		foreach ($keys as $key)
		{
			$result[$key] = call_user_func($enrich_context, $key);
		}
		return $result;
	}

	protected static function getQuotedEntriesNames($ids, $partner_id)
	{
		$result = self::getEntriesNames($ids, $partner_id);
		foreach ($result as &$name)
		{
			$name = '"' . str_replace('"', '""', $name) . '"';
		}
		return $result;
	}

	protected static function getCoordinates($keys)
	{
		$coordKeys = array();
		foreach ($keys as $key)
		{
			$memcKey = kKavaBase::getCoordinatesKey(array($key));
			$coordKeys[$memcKey] = true;
		}
		$coords = kKavaBase::getCoordinatesForKeys(array_keys($coordKeys));
		$result = array();
		foreach ($keys as $key)
		{
			$memcKey = kKavaBase::getCoordinatesKey(array($key));
			if (isset($coords[$memcKey]))
			{
				$result[$key] = array($coords[$memcKey]);
			}
		}
		return $result;
	}

	protected static function getEntriesUserIdsAndNames($ids, $partner_id)
	{
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		$c->addSelectColumn(entryPeer::ID);
		$c->addSelectColumn(entryPeer::NAME);
		$c->addSelectColumn(entryPeer::PUSER_ID);

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(entryPeer::PARTNER_ID, $partner_id);
		}
		$c->add(entryPeer::ID, $ids, Criteria::IN);

		entryPeer::setUseCriteriaFilter(false);
		$stmt = entryPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		entryPeer::setUseCriteriaFilter(true);

		$result = array();
		foreach ($rows as $row)
		{
			$id = $row['ID'];
			$puser_id = $row['PUSER_ID'];
			$name = $row['NAME'];
			$result[$id] = array($puser_id, '"' . $name . '"');
		}
		return $result;
	}

	protected static function getCategoriesNames($ids, $partner_id)
	{
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);

		$c->addSelectColumn(categoryPeer::ID);
		$c->addSelectColumn(categoryPeer::NAME);

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(categoryPeer::PARTNER_ID, $partner_id);
		}
		$c->add(categoryPeer::ID, $ids, Criteria::IN);

		categoryPeer::setUseCriteriaFilter(false);
		$stmt = categoryPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
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

	protected static function hashUserId($conf, $partner_id, $puser_id, $kuser_id)
	{
		if (!isset($conf[$partner_id]))
		{
			return false;
		}
		$partner_conf = $conf[$partner_id];
		
		if (isset($partner_conf['userIdPattern']))
		{
			if (!preg_match($partner_conf['userIdPattern'], $puser_id))
			{
				return false;
			}
		}
	
		$salt = $partner_conf['salt'];
		$hash = md5($salt . $puser_id, true);
		for ($i = 0; $i < 32; $i += 2)
		{
			$hash[$i >> 1] = chr(
				(ord($hash[$i >> 1]) & 0xee) | 
				((($kuser_id >> $i) & 1) << 4) | 
				(($kuser_id >> ($i + 1)) & 1));
		}
		
		return strtoupper(bin2hex($hash));
	}

	protected static function getKuserIdFromHash($hash)
	{
		if (!preg_match('/^[0-9A-Z]{32}$/D', $hash))
		{
			return false;
		}
		
		$result = 0;
		for ($i = 0; $i < strlen($hash); $i++)
		{
			$cur = strtolower($hash[$i]);
			if ($cur >= '0' && $cur <= '9')
			{
				$value = ord($cur) - ord('0');
			}
			else if ($cur >= 'a' && $cur <= 'f')
			{
				$value = ord($cur) - ord('a') + 10;
			}
			else
			{
				return false;
			}
			
			$result |= ($value & 1) << $i;
		}
		
		return $result;
	}

	protected static function getUserScreenNameWithFallback($ids, $partner_id)
	{
		$context = array(
			'columns' => array('PUSER_ID', 'SCREEN_NAME'),
			'hash' => false,
		);
		$result = self::getUsersInfo($ids, $partner_id, $context);
		foreach ($result as $id => $row)
		{
			$result[$id] = $row[1] ? $row[1] : $row[0];
		}
		
		return $result;
	}
	
	protected static function getUsersInfo($ids, $partner_id, $context)
	{
		$columns = isset($context['columns']) ? $context['columns'] : array('PUSER_ID');
		if (!isset($context['hash']) || $context['hash'])
		{  
			$hash_conf = kConf::get('kava_hash_user_ids', 'local', array());
		}
		else
		{
			$hash_conf = array();
		}
		
		$result = array();
			
		// leave non-integer values as is (e.g. 'Unknown')
		foreach ($ids as $index => $id)
		{
			if (ctype_digit($id))
			{
				continue;
			}

			unset($ids[$index]);
			
			$output = array();
			foreach ($columns as $column)
			{
				$output[] = in_array($column, array('PUSER_ID', 'SCREEN_NAME')) ? $id : '';
			}
			$result[$id] = $output;
		}
		
		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);

		$c->addSelectColumn(kuserPeer::ID);
		$c->addSelectColumn(kuserPeer::PARTNER_ID);
		$c->addSelectColumn(kuserPeer::PUSER_ID);

		foreach ($columns as $column)
		{
			if ($column == 'PUSER_ID')
			{
				continue;
			}
			
			$c->addSelectColumn("kuser.$column");
		}
		
		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(kuserPeer::PARTNER_ID, $partner_id);
		}
		$c->add(kuserPeer::ID, $ids, Criteria::IN);

		kuserPeer::setUseCriteriaFilter(false);
		$stmt = kuserPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		kuserPeer::setUseCriteriaFilter(true);
		
		foreach ($rows as $row)
		{
			$partner_id = $row['PARTNER_ID'];
			$puser_id = $row['PUSER_ID'];
			$kuser_id = $row['ID'];

			$output = array();
			
			$hash = self::hashUserId($hash_conf, $partner_id, $puser_id, $kuser_id);
			if ($hash === false)
			{
				foreach ($columns as $column)
				{
					$output[] = $row[$column];
				}
			}
			else
			{
				foreach ($columns as $column)
				{
					// do not expose any column other than the hashed id
					$output[] = $column == 'PUSER_ID' ? $hash : '';
				}
			}
			
			$result[$kuser_id] = $output;
		}
		return $result;
	}

	protected static function getEntriesCategories($ids, $partner_id, $context)
	{
		// get the category ids of the entries
		$c = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);

		$c->addSelectColumn(categoryEntryPeer::ENTRY_ID);
		$c->addSelectColumn('GROUP_CONCAT('.categoryEntryPeer::CATEGORY_ID.')');
		
		$c->addGroupByColumn(categoryEntryPeer::ENTRY_ID);

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(categoryEntryPeer::PARTNER_ID, $partner_id);
		}
		$c->add(categoryEntryPeer::ENTRY_ID, $ids, Criteria::IN);

		$stmt = categoryEntryPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$all_categories = array();
		$result = array();
		foreach ($rows as $row)
		{
			$entry_id = $row['ENTRY_ID'];
			$categories_ids = $row['GROUP_CONCAT('.categoryEntryPeer::CATEGORY_ID.')'];
			$categories_ids = explode(',', $categories_ids);
			foreach ($categories_ids as $category_id)
			{
				$all_categories[$category_id] = true;
			}
			$result[$entry_id] = $categories_ids; 
		}
		
		// get the names of the categories
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);

		$c->addSelectColumn(categoryPeer::ID);
		$c->addSelectColumn(categoryPeer::FULL_NAME);

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add(categoryPeer::PARTNER_ID, $partner_id);
		}
		$c->add(categoryPeer::ID, array_keys($all_categories), Criteria::IN);

		categoryPeer::setUseCriteriaFilter(false);
		$stmt = categoryPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		categoryPeer::setUseCriteriaFilter(true);
		
		$category_name_map = array();
		foreach ($rows as $row)
		{
			$id = $row['ID'];
			$full_name = $row['FULL_NAME'];
			$category_name_map[$id] = $full_name; 
		}
		
		// add the names to the result
		foreach ($result as $entry_id => $categories_ids)
		{
			$names = array();
			foreach ($categories_ids as $category_id)
			{
				if (isset($category_name_map[$category_id]))
				{
					$names[] = $category_name_map[$category_id];
				}
			}
			
			$result[$entry_id] = array(
				'"' . str_replace('"', '""', implode(',', $categories_ids)) . '"', 
				'"' . str_replace('"', '""', implode(',', $names)) . '"',
			);
		}
		
		return $result;
	}
	
	protected static function genericQueryEnrich($ids, $partner_id, $context)
	{
		$peer = $context['peer'];
		$columns = $context['columns'];
		$dim_column = isset($context['dim_column']) ? $context['dim_column'] : 'ID';
		$partner_id_column = isset($context['partner_id_column']) ? $context['partner_id_column'] : 'PARTNER_ID';
		$group_by_columns = isset($context['group_by_columns']) ? $context['group_by_columns'] : array();
		$custom_crits = isset($context['custom_criterion']) ? $context['custom_criterion'] : array();
		$int_ids_only = isset($context['int_ids_only']) ? $context['int_ids_only'] : false;

		$c = KalturaCriteria::create($peer::OM_CLASS);

		$table_name = $peer::TABLE_NAME;
		$c->addSelectColumn($table_name . '.' . $dim_column);

		$column_formats = array();
		foreach ($columns as $index => $column)
		{
			switch ($column[0])
			{
			case '"':
				$column = trim($column, '"');
				$columns[$index] = $column;
				$column_formats[$column] = self::COLUMN_FORMAT_QUOTE;
				break;
				
			case '@':
				$column = substr($column, 1);
				$columns[$index] = $column;
				$column_formats[$column] = self::COLUMN_FORMAT_UNIXTIME;
				break;
			}

			if (strpos($column, '(') !== false)
			{
				$c->addSelectColumn($column);
			}
			else
			{
				$exploded_column = explode('.', $column);
				$c->addSelectColumn($table_name . '.' . $exploded_column[0]);
			}
		}

		foreach ($group_by_columns as $column)
		{
			$c->addGroupByColumn($table_name . '.' . $column);
		}

		if ($partner_id != Partner::ADMIN_CONSOLE_PARTNER_ID)
		{
			$c->add($table_name . '.' . $partner_id_column, $partner_id);
		}
				
		$result = array();
				
		foreach ($ids as $index => $id)
		{
			if (isset(self::$error_ids[$id]) || 
				($int_ids_only && !ctype_digit($id)))
			{
				unset($ids[$index]);
				$result[$id] = $id;
			}
		}
		
		$c->add($table_name . '.' . $dim_column, $ids, Criteria::IN);

		if (isset($custom_crits['column']))
		{
			$custom_crits = array($custom_crits);
		}

		foreach ($custom_crits as $custom_crit)
		{
			$column = $custom_crit['column'];
			$value = $custom_crit['value'];
			$comparison = $custom_crit['comparison'];
			if ($comparison == Criteria::IN && is_string($value))
			{
				$value = explode(',', $value);
			}
			$c->addAnd($c->getNewCriterion($column, $value, $comparison));
		}

		$con = $peer::alternativeCon(null);
		$con->exec('SET SESSION group_concat_max_len = 102400');

		$peer::setUseCriteriaFilter(false);
		$stmt = $peer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$rows = $stmt->fetchAll(PDO::FETCH_NUM);
		$peer::setUseCriteriaFilter(true);

		foreach ($rows as $row)
		{
			$id = reset($row);
			$output_row = array();
			foreach ($columns as $column)
			{
				$format = isset($column_formats[$column]) ? $column_formats[$column] : null;

				$value = next($row);
				
				$exploded_column = explode('.', $column);
				if (strpos($column, '(') === false && count($exploded_column) > 1)
				{
					list($column, $field) = $exploded_column;
					$value = @unserialize($value);
					$value = isset($value[$field]) ? $value[$field] : '';
				}

				switch ($format)
				{
				case self::COLUMN_FORMAT_QUOTE:
					$value = '"' . str_replace(array('"', "\n", "\r"), array('""', ' ', ''), $value) . '"';
					break;
					
				case self::COLUMN_FORMAT_UNIXTIME:
					$dt = new DateTime($value);
					$value = (int) $dt->format('U');
					break;
				}

				$output_row[] = $value;
			}

			$result[$id] = $output_row;
		}
		return $result;
	}

	protected static function getEnrichDefs($report_def)
	{
		if (!isset($report_def[self::REPORT_ENRICH_DEF]))
		{
			return array();
		}
		
		$result = $report_def[self::REPORT_ENRICH_DEF];
		if (isset($result[self::REPORT_ENRICH_OUTPUT]))
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
			$cur_fields = $enrich_def[self::REPORT_ENRICH_OUTPUT];
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

	protected static function arrayGetIndexes($arr, $elements)
	{
		$result = array();
		foreach ($elements as $element)
		{
			$index = array_search($element, $arr);
			if ($index === false)
			{
				return false;
			}
			$result[] = $index;
		}
		return $result;
	}

	protected static function arrayGetElements($arr, $indexes)
	{
		$result = array();
		foreach ($indexes as $index)
		{
			$result[] = $arr[$index];
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
			// func
			$enrich_func = $enrich_def[self::REPORT_ENRICH_FUNC];
			$enrich_context = isset($enrich_def[self::REPORT_ENRICH_CONTEXT]) ? 
				$enrich_def[self::REPORT_ENRICH_CONTEXT] : null;

			// output
			$cur_fields = $enrich_def[self::REPORT_ENRICH_OUTPUT];
			if (!is_array($cur_fields))
			{
				$cur_fields = array($cur_fields);
			}

			$enriched_indexes = self::arrayGetIndexes($headers, $cur_fields);
			if (!$enriched_indexes)
			{
				continue;
			}
			// input
			if (isset($enrich_def[self::REPORT_ENRICH_INPUT]))
			{
				$dim_headers = $enrich_def[self::REPORT_ENRICH_INPUT];
				if (!is_array($dim_headers))
				{
					$dim_headers = array($dim_headers);
				}
			}
			else
			{
				$dim_headers = array(reset($cur_fields));
			}
			$dim_indexes = self::arrayGetIndexes($headers, $dim_headers);
			if (!$dim_indexes)
			{
				continue;
			}
			$dim_indexes = implode(',', $dim_indexes);

			// add
			if (!isset($enrich_specs[$dim_indexes]))
			{
				$enrich_specs[$dim_indexes] = array();
			}
			$enrich_specs[$dim_indexes][] = array($enrich_func, $enrich_context, $enriched_indexes);
		}
		
		// enrich the data in chunks
		$rows_count = count($data);
		foreach ($enrich_specs as $dim_indexes => $cur_enrich_specs)
		{
			$dim_indexes = explode(',', $dim_indexes);
			$start = 0;
			while ($start < $rows_count)
			{
				// get the dimension values for the current chunk
				$limit = min($start + self::ENRICH_CHUNK_SIZE, $rows_count);
				$dimension_ids = array();
				for ($current_row = $start; $current_row < $limit; $current_row++) 
				{
					$key = self::arrayGetElements($data[$current_row], $dim_indexes);
					$key = implode(self::ENRICH_DIM_DELIMITER, $key);
					$dimension_ids[$key] = true;
				}
				
				// run the enrichment functions
				foreach ($cur_enrich_specs as $enrich_spec)
				{
					list($enrich_func, $enrich_context, $enriched_indexes) = $enrich_spec;

					$entities = call_user_func($enrich_func, array_keys($dimension_ids), $partner_id, $enrich_context);
			
					for ($current_row = $start; $current_row < $limit; $current_row++) 
					{
						$key = self::arrayGetElements($data[$current_row], $dim_indexes);
						$key = implode(self::ENRICH_DIM_DELIMITER, $key);
						$entity = isset($entities[$key]) ? $entities[$key] : null;
						foreach ($enriched_indexes as $index => $enrich_field)
						{
							$data[$current_row][$enrich_field] = is_array($entity) ? $entity[$index] : $entity;
						}
					}
				}
				
				$start = $limit;
			}
		}
	}

	/// table functions
	protected static function getDateColumnName($interval)
	{
		switch ($interval)
		{
			case self::INTERVAL_MONTHS:
				return 'month_id';

			case self::INTERVAL_ALL:
				return 'all';

			default:
				return 'date_id';
		}
	}
	
	protected static function getMetricFromOrderBy($report_def, $order_by)
	{
		if (!$order_by)
		{
			return null;
		}
		
		if ($order_by[0] === '-' || $order_by[0] === '+')
		{
			$order_by = substr($order_by, 1);
		}

		$map = null;
		if (isset($report_def[self::REPORT_TABLE_MAP]))
		{
			$map = $report_def[self::REPORT_TABLE_MAP];
		}
		else if (isset($report_def[self::REPORT_COLUMN_MAP]))
		{
			$map = $report_def[self::REPORT_COLUMN_MAP];
		}

		if ($map && isset($map[$order_by]))
		{
			$order_by = $map[$order_by];
		}

		if (isset(self::$headers_to_metrics[$order_by]))
		{
			return self::$headers_to_metrics[$order_by];
		}
		
		return null;
	}
	
	protected static function getTableFromGraphs($graphs, $has_aligned_dates, $date_column_name = 'date_id', $page_size = null, $page_index = 1)
	{
		if (!$has_aligned_dates)
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
		}
		else 
		{
			$dates = reset($graphs);
		}

		// build the table
		$header = array_keys($graphs);
		$data = array();
		foreach ($dates as $date => $ignore)
		{
			$row = array($date);
			foreach ($header as $column)
			{
				$row[] = isset($graphs[$column][$date]) ? $graphs[$column][$date] : 0;
			}
			$data[] = $row;
		}

		$total_count = count($data);
		if ($page_size)
		{
			$data = array_slice($data, ($page_index - 1) * $page_size, $page_size);
		}

		return array(array_merge(array($date_column_name), $header), $data, $total_count);
	}

	protected static function getTableFromKeyedGraphs($partner_id, $report_def, reportsInputFilter $input_filter, 
		$page_size, $page_index, $object_ids)
	{
		// calculate the graphs
		$result = self::getKeyedJoinGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
		if (!$result)
		{
			return array(array(), array(), 0);
		}
		
		// convert the result to a table
		$metric_headers = isset($report_def[self::REPORT_METRICS]) ? 
			$report_def[self::REPORT_METRICS] : array_keys(reset($result));
		$date_headers = $input_filter->interval != self::INTERVAL_ALL ? 
			array(self::getDateColumnName($input_filter->interval)) : 
			array();
		$headers = array_merge(
			$date_headers,
			$report_def[self::REPORT_DIMENSION_HEADERS], 
			$metric_headers);
		 
		$dim_header_count = count($report_def[self::REPORT_DIMENSION_HEADERS]);
				
		$data = array();
		if ($input_filter->interval != self::INTERVAL_ALL)
		{
			$first_graph = reset($result);
			$dates = array_keys(reset($first_graph));

			foreach ($dates as $date)
			{
				foreach ($result as $dim => $graphs)
				{
					$row = array_merge(array($date), array_fill(0, $dim_header_count, $dim));
					foreach ($metric_headers as $header)
					{
						$row[] = isset($graphs[$header][$date]) ? $graphs[$header][$date] : 0;
					}
					$data[] = $row;
				}
			}
		}
		else
		{
			foreach ($result as $dim => $graphs)
			{
				$row = array_fill(0, $dim_header_count, $dim);
				foreach ($metric_headers as $header)
				{
					$row[] = isset($graphs[$header]) ? reset($graphs[$header]) : 0;
				}
				$data[] = $row;
			}
		}
		
		return array(
			$headers, 
			array_slice($data, ($page_index - 1) * $page_size, $page_size), 
			count($data));
	}

	protected static function getGranularityFromFilterInterval($interval)
	{
		switch ($interval)
		{
			case self::INTERVAL_MONTHS:
				return self::GRANULARITY_MONTH;
			case self::INTERVAL_ALL:
				return self::DRUID_GRANULARITY_ALL;
			default:
				return self::GRANULARITY_DAY;
		}
	}

	protected static function getTotalTableCount($partner_id, $report_def, reportsInputFilter $input_filter, $intervals, $druid_filter, $dimension, $object_ids = null)
	{
		$cache_key = 'reportCount-' . md5("$partner_id|".serialize($report_def)."|$object_ids|".serialize($input_filter));

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

		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;

		$query = self::getDimCardinalityReport($data_source, $partner_id, $intervals, $dimension, $druid_filter, self::getMetrics($report_def));

		$total_count_arr = self::runQuery($query);
		if (isset($total_count_arr[0][self::DRUID_RESULT][self::METRIC_CARDINALITY]))
		{
			$total_count = floor($total_count_arr[0][self::DRUID_RESULT][self::METRIC_CARDINALITY]);
		}
		else
		{
			$total_count = 0;
		}

		KalturaLog::log("count: [$total_count]");

		if ($cache)
		{
			$cache->set($cache_key, $total_count, myReportsMgr::REPORTS_COUNT_CACHE);
		}

		return $total_count;
	}

	protected static function getSimpleTableImpl($partner_id, $report_def, 
		reportsInputFilter $input_filter,
		$page_size, $page_index, $order_by, $object_ids = null, $flags = 0)
	{
		$start = microtime (true);
		$total_count = null;

		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$intervals = self::getFilterIntervals($report_def, $input_filter);
		$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, $object_ids);
		$dimension = self::getDimension($report_def, $object_ids);
		$metrics = self::getMetrics($report_def);

		if (!$metrics)
		{
			// no metrics - can use a search query
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
			return array($report_def[self::REPORT_DIMENSION_HEADERS], $data, count($data));
		}

		// Note: max size is already validated externally when $isCsv is true
		$max_result_size = ($flags & self::GET_TABLE_FLAG_IS_CSV) ? PHP_INT_MAX : self::MAX_RESULT_SIZE;
		if ($page_index * $page_size > $max_result_size)
		{
			if ($page_index == 1 && ($flags & self::GET_TABLE_FLAG_IS_CSV) == 0)
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
			}

			$order_by = self::getMetricFromOrderBy($report_def, $order_by);

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
			if (is_array($dimension) && in_array(self::DIMENSION_TIME, $dimension))
			{
				$granularity = $report_def[self::REPORT_GRANULARITY];
				$granularity_def = self::getGranularityDef($granularity, $input_filter->timeZoneOffset);

				// remove the time dimension from the list
				$timeKey = array_search(self::DIMENSION_TIME, $dimension);
				unset($dimension[$timeKey]);
				$dimension = array_values($dimension);		// make sure the dimension is not rendered as an object in the druid query
			}
			else
			{
				$granularity_def = self::DRUID_GRANULARITY_ALL;
			}

			// topN works badly for non-linear metrics like avg drop off, since taking the topN per segment
			// does not necessarily yield the combined topN
			$threshold = $page_size * $page_index;

			$query = self::getGroupByReport(
				$data_source,
				$partner_id,
				$intervals,
				$granularity_def,
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
		else if ($flags & self::GET_TABLE_FLAG_IDS_ONLY)
		{
			// caller needs only the ids - get a single metric
			$query = self::getTopReport($data_source, $partner_id, $intervals, array($order_by), $dimension, $druid_filter, $order_by, $order_by_dir, $threshold, $metrics);
		}
		else if (!$object_ids &&
			!in_array($dimension, array(self::DIMENSION_LOCATION_COUNTRY, self::DIMENSION_DOMAIN, self::DIMENSION_DEVICE)) &&
			!self::getFilterValues($druid_filter, $dimension) &&
			($flags & self::GET_TABLE_FLAG_IS_CSV) == 0)
		{
			// get the topN objects first, otherwise the returned metrics can be inaccurate
			$query = self::getTopReport($data_source, $partner_id, $intervals, array($order_by), 
				$dimension, $druid_filter, $order_by, $order_by_dir, $threshold, $metrics);
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
			else if ((!($input_filter instanceof endUserReportsInputFilter)) || 
				isset($report_def[self::REPORT_FORCE_TOTAL_COUNT]))
			{
				$total_count = self::getTotalTableCount($partner_id, $report_def, 
					$input_filter, $intervals, $druid_filter, $dimension, $object_ids);
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

			// Note: not passing $dimension_ids as $object_ids since in some reports $object_ids
			//		filters by entries, and not by $dimension
			$druid_filter = self::getDruidFilter($partner_id, $report_def, $input_filter, null);

			$druid_filter[] = array(
				self::DRUID_DIMENSION => $dimension,
				self::DRUID_VALUES => $dimension_ids
			);

			$query = self::getTopReport($data_source, $partner_id, $intervals, $metrics, 
				$dimension, $druid_filter, $order_by, $order_by_dir, $threshold);
		}
		else
		{
			$query = self::getTopReport($data_source, $partner_id, $intervals, $metrics, 
				$dimension, $druid_filter, $order_by, $order_by_dir, $threshold);
		}

		// use lowest priority for csv queries
		if (($flags & self::GET_TABLE_FLAG_IS_CSV) && isset($query[self::DRUID_CONTEXT][self::DRUID_PRIORITY]))
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
			else if ((!($input_filter instanceof endUserReportsInputFilter)) || 
				isset($report_def[self::REPORT_FORCE_TOTAL_COUNT]) || 
				($flags & self::GET_TABLE_FLAG_IS_CSV))
			{
				$total_count = self::getTotalTableCount($partner_id, $report_def, $input_filter, $intervals, $druid_filter, $dimension, $object_ids);

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

		$dimension = $report_def[self::REPORT_DIMENSION];
		$dimension_headers = $report_def[self::REPORT_DIMENSION_HEADERS];
		if ($object_ids)
		{
			if (array_key_exists(self::REPORT_DRILLDOWN_DIMENSION, $report_def))
				$dimension = $report_def[self::REPORT_DRILLDOWN_DIMENSION];
			if (array_key_exists(self::REPORT_DRILLDOWN_DIMENSION_HEADERS, $report_def))
				$dimension_headers = $report_def[self::REPORT_DRILLDOWN_DIMENSION_HEADERS];
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

		if ($object_ids && isset($report_def[self::REPORT_DRILLDOWN_DIMENSION_MAP]))
		{
			$row_mapping = $report_def[self::REPORT_DRILLDOWN_DIMENSION_MAP];
		}
		elseif (isset($report_def[self::REPORT_DIMENSION_MAP]))
		{
			$row_mapping = $report_def[self::REPORT_DIMENSION_MAP];
		}
		else
		{
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
		}


		$row_mapping = array_merge($row_mapping, $metrics);

		// map the rows
		foreach ($rows as $index => $row)
		{
			if ($query[self::DRUID_QUERY_TYPE] == self::DRUID_GROUP_BY)
			{
				$timestamp = $row[self::DRUID_TIMESTAMP];
				$row = $row[self::DRUID_EVENT];
			}

			$row_data = array();
			foreach ($row_mapping as $column)
			{
				if ($column)
				{
					if (isset($row[$column]))
					{
						$value = $row[$column];
						if ($value == '-0')		// remove '-0' that sometimes returns from druid 
						{
							$value = 0;
						}
					}
					else if ($column == self::DIMENSION_TIME)
					{
						$value = $timestamp;
					}
					else
					{
						$value = 0;
					}
				}
				else
				{
					$value = null;
				}
				$row_data[] = $value;
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

		$end = microtime(true);
		KalturaLog::log('getTable took [' . ($end - $start) . ']');

		return array($headers, $data, $total_count);
	}

	protected static function getJoinTableImpl($partner_id, $report_def,
			reportsInputFilter $input_filter,
			$page_size, $page_index, $order_by, $object_ids = null, $flags = 0)
	{
		$interval = $input_filter->interval;
		// use interval=all, only relevant if the join includes graphs
		$input_filter->interval = self::INTERVAL_ALL;
		
		$report_defs = $report_def[self::REPORT_JOIN_REPORTS];
	
		// decide which report to run first, according to the order by
		$order_found = false;
		$root_metric = self::getMetricFromOrderBy($report_def, $order_by);
		if ($root_metric)
		{
			foreach ($report_defs as $index => $cur_report_def)
			{
				if (!in_array($root_metric, $cur_report_def[self::REPORT_METRICS]))
				{
					continue;
				}
				
				$order_found = true;
				unset($report_defs[$index]);
				break;
			}
		}
		
		if (!$order_found)
		{
			$cur_report_def = array_shift($report_defs);
		}
		
		// run the root report
		if (!isset($cur_report_def[self::REPORT_DIMENSION]))
		{
			$cur_report_def[self::REPORT_DIMENSION] = $report_def[self::REPORT_DIMENSION];
		}
		$cur_report_def[self::REPORT_DIMENSION_HEADERS] = array('dimension');
		
		$result = self::getTableImpl($partner_id, $cur_report_def, $input_filter,
			$page_size * $page_index, 1, $order_by, $object_ids, $flags);

		$headers = array_merge(
			$report_def[self::REPORT_DIMENSION_HEADERS], 
			array_slice($result[0], 1));
		
		$exclude_ids = array_map('reset', array_slice($result[1], 0, $page_size * ($page_index - 1)));
		$exclude_ids = array_flip($exclude_ids);
		
		$rows = array();
		foreach (array_slice($result[1], $page_size * ($page_index - 1)) as $row)
		{
			$cur_id = array_shift($row);
			$rows[$cur_id] = $row;
		}
		
		$total_count = $result[2];

		$metric_count = count($cur_report_def[self::REPORT_METRICS]);
		
		// run additional reports
		foreach ($report_defs as $cur_report_def)
		{
			// add the current report headers
			foreach ($cur_report_def[self::REPORT_METRICS] as $column)
			{
				$headers[] = self::$metrics_to_headers[$column];
			}

			if (!isset($cur_report_def[self::REPORT_DIMENSION]))
			{
				$cur_report_def[self::REPORT_DIMENSION] = $report_def[self::REPORT_DIMENSION];
			}
			$cur_report_def[self::REPORT_DIMENSION_HEADERS] = array('dimension');
				
			if (count($rows) < $page_size)
			{				
				// using a single topN - the metrics of this query are not used
				$result = self::getTableImpl($partner_id, $cur_report_def, $input_filter,
					$page_size * $page_index, 1, $order_by, $object_ids, $flags | self::GET_TABLE_FLAG_IDS_ONLY);
								
				foreach ($result[1] as $row)
				{
					$cur_id = reset($row);
					if (isset($rows[$cur_id]))
					{
						// there's a already a row for this item
						continue;
					}
										
					// exclude ids up to the start of the page
					if (isset($exclude_ids[$cur_id]))
					{
						continue;
					}
					
					if (count($exclude_ids) < $page_size * ($page_index - 1))
					{
						$exclude_ids[$cur_id] = true;
						continue;
					}
					
					// add a new row
					$rows[$cur_id] = array_fill(0, $metric_count, 0);
					if (count($rows) >= $page_size)
					{
						// already have all the items we need
						break;
					}
				}

				$cur_total_count = $result[2];
			}
			else
			{
				// already have all rows - get only the total count
				$intervals = self::getFilterIntervals($cur_report_def, $input_filter);
				$druid_filter = self::getDruidFilter($partner_id, $cur_report_def, $input_filter, $object_ids);				
				$cur_total_count = self::getTotalTableCount($partner_id, $cur_report_def, $input_filter, $intervals, $druid_filter, $report_def[self::REPORT_DIMENSION], $object_ids);
			}

			$total_count = max($total_count, $cur_total_count);
				
			if ($rows)
			{
				$ids_to_get = $rows;
				
				// enrich existing rows
				if (!isset($cur_report_def[self::REPORT_DIMENSION]))
				{
					$cur_report_def[self::REPORT_DIMENSION] = $report_def[self::REPORT_DIMENSION];
				}
				$cur_report_def[self::REPORT_FILTER_DIMENSION] = $cur_report_def[self::REPORT_DIMENSION];;
				$cur_report_def[self::REPORT_DIMENSION_HEADERS] = array('dimension');
				
				$result = self::getTableImpl($partner_id, $cur_report_def, $input_filter,
					count($ids_to_get), 1, null, implode(',', array_keys($ids_to_get)), $flags);
				
				foreach ($result[1] as $row)
				{
					$cur_id = array_shift($row);
					$rows[$cur_id] = array_merge($rows[$cur_id], $row);
					unset($ids_to_get[$cur_id]);
				}
				
				// zero fill rows that were not retrieved
				$filler = array_fill(0, count($cur_report_def[self::REPORT_METRICS]), 0);
				foreach ($ids_to_get as $cur_id => $ignore)
				{
					$rows[$cur_id] = array_merge($rows[$cur_id], $filler);
				}
			}
				
			$metric_count += count($cur_report_def[self::REPORT_METRICS]);
		}
		
		// make the rows non-associative
		$data = array();
		foreach ($rows as $cur_id => $row)
		{
			$data[] = array_merge(
				array_fill(0, count($report_def[self::REPORT_DIMENSION_HEADERS]), $cur_id),
				$row);
		}

		$input_filter->interval = $interval;
		return array($headers, $data, $total_count);
	}

	protected static function getTableImpl($partner_id, $report_def, 
		reportsInputFilter $input_filter,
		$page_size, $page_index, $order_by, $object_ids = null, $flags = 0)
	{
		if (isset($report_def[self::REPORT_EDIT_FILTER_FUNC]))
		{
			call_user_func($report_def[self::REPORT_EDIT_FILTER_FUNC], $input_filter);
		}
				
		if (!isset($report_def[self::REPORT_DIMENSION]))
		{
			$result = self::getGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
			$result = self::getTableFromGraphs($result, true, self::getDateColumnName($input_filter->interval),
				$page_size, $page_index);
		}
		else if (isset($report_def[self::REPORT_JOIN_GRAPHS]))
		{
			$result = self::getTableFromKeyedGraphs($partner_id, $report_def, $input_filter, 
				$page_size, $page_index, $object_ids);
		}
		else if (isset($report_def[self::REPORT_JOIN_REPORTS]))
		{
			$result = self::getJoinTableImpl($partner_id, $report_def, $input_filter, 
				$page_size, $page_index, $order_by, $object_ids, $flags);
		}
		else 
		{
			$result = self::getSimpleTableImpl($partner_id, $report_def, $input_filter,
				$page_size, $page_index, $order_by, $object_ids, $flags);
		}
		
		// finalize / enrich
		if (isset($report_def[self::REPORT_TABLE_FINALIZE_FUNC]))
		{
			call_user_func_array($report_def[self::REPORT_TABLE_FINALIZE_FUNC], array(&$result));
		}
		
		if (isset($report_def[self::REPORT_ENRICH_DEF]))
		{
			self::enrichData($report_def, $result[0], $partner_id, $result[1]);
		}

		return $result;
	}
		
	protected static function reorderTableColumns($dim_header_count, $column_map, $isTable, &$result) 
	{
		if ($dim_header_count > 0)
		{
			$indexes = range(0, $dim_header_count - 1);
			$header = array_slice($result[0], 0, $dim_header_count);
		}
		else
		{
			$indexes = array();
			$header = array();
		}
		
		foreach ($column_map as $column => $metric)
		{
			$index = array_search($metric, $result[0]);
			if ($index === false)
			{
				continue;
			}
				
			$indexes[] = $index;
			$header[] = $column;
		}
		
		$orig_header_count = count($result[0]);
		$result[0] = $header;		
		if ($indexes == range(0, $orig_header_count - 1))
		{
			return;
		}
		
		if ($isTable)
		{
			foreach ($result[1] as &$row)
			{
				$new_row = array();
				foreach ($indexes as $index)
				{
					$new_row[] = $row[$index];
				}
				$row = $new_row;
			}
		}
		else
		{
			$new_row = array();
			foreach ($indexes as $index)
			{
				$new_row[] = $result[1][$index];
			}
			$result[1] = $new_row;
		}
	}
	
	public static function getTable($partner_id, $report_type, reportsInputFilter $input_filter,
		$page_size, $page_index, $order_by, $object_ids = null, $offset = null, $isCsv = false)
	{
		if (!self::shouldUseKava($partner_id, $report_type))
		{
			return myReportsMgr::getTable($partner_id, $report_type, $input_filter,
				$page_size, $page_index, $order_by, $object_ids, $offset);
		}
		
		self::init();		

		// pager
		if (!$page_size || $page_size < 0)
			$page_size = 10;
		
		if (!$page_index || $page_index < 0)
			$page_index = 1;
		
		// run the query
		$report_def = self::getReportDef($report_type);
		if (isset($report_def[self::REPORT_SKIP_PARTNER_FILTER]))
		{
			$partner_id = Partner::ADMIN_CONSOLE_PARTNER_ID;
		}
		
		$flags = $isCsv ? self::GET_TABLE_FLAG_IS_CSV : 0;
		
		$result = self::getTableImpl($partner_id, $report_def, $input_filter,
			$page_size, $page_index, $order_by, $object_ids, $flags);
	
		// reorder
		$map = null;
		if (isset($report_def[self::REPORT_TABLE_MAP]))
		{
			$map = $report_def[self::REPORT_TABLE_MAP];
		}
		else if (isset($report_def[self::REPORT_COLUMN_MAP]))
		{
			$map = $report_def[self::REPORT_COLUMN_MAP];
		}

		if ($map)
		{
			$dim_header_count = isset($report_def[self::REPORT_DIMENSION_HEADERS]) ?
				count($report_def[self::REPORT_DIMENSION_HEADERS]) : 0;
			if (in_array(reset($result[0]), array('date_id', 'month_id')))
			{
				$dim_header_count++;	// the date header
			}

			self::reorderTableColumns(
				$dim_header_count, 
				$map, 
				true,
				$result);
		}
		
		return $result;
	}
	
	protected static function partnerUsageEditFilter($input_filter)
	{
		$current_date_id = date('Ymd');
		$from_day = min($input_filter->from_day, $current_date_id);
		
		$month_start = substr($from_day, 0, 6) . '01';
		$date = self::dateIdToDateTime($month_start);
		$date->modify('+1 month');
		$date->modify('-1 day');
		$month_end = min($date->format('Ymd'), $current_date_id);
		
		$is_free_package = $input_filter->extra_map[myPartnerUtils::IS_FREE_PACKAGE_PLACE_HOLDER] == 'TRUE';
		$input_filter->from_day = $is_free_package ? str_replace('-', '', self::BASE_DATE_ID) : $month_start;
		$input_filter->to_day = $month_end;
		$input_filter->interval = reportInterval::MONTHS;
	}
	
	protected static function addCombinedUsageColumn(&$result)
	{
		$headers = $result[0];
		$bandwidth = array_search(self::METRIC_BANDWIDTH_SIZE_MB, $headers);
		$storage = array_search(self::METRIC_AVERAGE_STORAGE_MB, $headers);
		if ($bandwidth === false || $storage === false)
		{
			return;
		}
		
		$result[0][] = self::METRIC_BANDWIDTH_STORAGE_MB;
		foreach ($result[1] as &$row)
		{
			$row[] = $row[$bandwidth] + $row[$storage]; 
		}
	}

	protected static function getRollupRow($data)
	{
		$row = reset($data);
		for (;;)
		{
			$cur_row = next($data);
			if (!$cur_row)
			{
				break;
			}
			
			for ($i = 1; $i < count($row); $i++)		// starting from 1 to skip the date id
			{
				$row[$i] += $cur_row[$i];
			}
		}
		
		return $row;
	}
	
	protected static function addRollupRow(&$result)
	{
		list($headers, $data, $total_count) = $result;
		
		$data[] = self::getRollupRow($data);
		
		$result = array($headers, $data, $total_count);
	}

	/// total functions
	protected static function getTotalPeakStorageFromTable($table)
	{
		$header = self::METRIC_PEAK_STORAGE_MB;
		$column_index = array_search($header, $table[0]);
		
		$value = 0;
		foreach ($table[1] as $row)
		{
			$value += $row[$column_index];
		}
		
		return array(array("SUM($header)"), array($value)); 
	}
	
	protected static function getSimpleTotalImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids = null)
	{
		$start = microtime(true);
		$data_source = isset($report_def[self::REPORT_DATA_SOURCE]) ? $report_def[self::REPORT_DATA_SOURCE] : null;
		$intervals = self::getFilterIntervals($report_def, $input_filter);
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
		
		$headers = array();
		$data = array();
		if (count($result) > 0)
		{
			$row = $result[0];
			$row_data = $row[self::DRUID_RESULT];

			foreach ($metrics as $column)
			{
				$headers[] = self::$metrics_to_headers[$column];
				$value = $row_data[$column];
				if ($value == '-0')
				{
					$value = 0;
				}
				$data[] = $value;
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

		$end = microtime(true);
		KalturaLog::log('getTotal took ['  . ($end - $start) . ']');

		return array($headers, $data);
	}
	
	protected static function getJoinTotalImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids = null) 
	{
		$report_defs = $report_def[self::REPORT_JOIN_REPORTS];
			
		$headers = array();
		$data = array();
		foreach ($report_defs as $cur_report_def)
		{
			list($cur_headers, $cur_data) = self::getSimpleTotalImpl($partner_id, $cur_report_def, $input_filter, $object_ids);
			$headers = array_merge($headers, $cur_headers);
			$data = array_merge($data, $cur_data);
		}
			
		return array($headers, $data);
	}

	protected static function getTotalImpl($partner_id, $report_def, reportsInputFilter $input_filter, $object_ids = null)
	{
		$interval = $input_filter->interval;
		$input_filter->interval = self::INTERVAL_ALL;

		if (isset($report_def[self::REPORT_TOTAL_FROM_TABLE_FUNC]))
		{
			$table = self::getTableImpl($partner_id, $report_def, $input_filter, self::MAX_RESULT_SIZE, 1, null, $object_ids);
			$result = call_user_func($report_def[self::REPORT_TOTAL_FROM_TABLE_FUNC], $table);
		}
		else if (isset($report_def[self::REPORT_JOIN_GRAPHS]) ||
				(!isset($report_def[self::REPORT_DIMENSION]) && isset($report_def[self::REPORT_GRAPH_METRICS])))
		{
			$result = self::getGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
			$result = array(array_keys($result), array_map('reset', array_values($result)));
		}
		else if (isset($report_def[self::REPORT_JOIN_REPORTS]))
		{
			$result = self::getJoinTotalImpl($partner_id, $report_def, $input_filter, $object_ids);
		}
		else 
		{
			$result = self::getSimpleTotalImpl($partner_id, $report_def, $input_filter, $object_ids);
		}

		$input_filter->interval = $interval;
		return $result;
	}
	
	public static function getTotal($partner_id, $report_type, reportsInputFilter $input_filter, $object_ids = null)
	{
		if (!self::shouldUseKava($partner_id, $report_type))
		{
			return myReportsMgr::getTotal($partner_id, $report_type, $input_filter, $object_ids);
		}
		
		self::init();

		$report_def = self::getReportDef($report_type);
		
		if (isset($report_def[self::REPORT_SKIP_PARTNER_FILTER]))
		{
			$partner_id = Partner::ADMIN_CONSOLE_PARTNER_ID;
		}
		
		// run the query
		$result = self::getTotalImpl($partner_id, $report_def, $input_filter, $object_ids);
		
		// reorder
		$map = null;
		if (isset($report_def[self::REPORT_TOTAL_MAP]))
		{
			$map = $report_def[self::REPORT_TOTAL_MAP];
		}
		else if (isset($report_def[self::REPORT_COLUMN_MAP]))
		{
			$map = $report_def[self::REPORT_COLUMN_MAP];
		}

		if ($map)
		{
			self::reorderTableColumns(0, $map, false, $result);
		}
		
		return $result;
	}

	public static function getBaseTotal($partner_id, $report_type, reportsInputFilter $input_filter, $object_ids = null )
	{
		if (!self::shouldUseKava($partner_id, $report_type))
		{
			return myReportsMgr::getBaseTotal($partner_id, $report_type, $input_filter, $object_ids);
		}
		
		switch ($report_type)
		{
			case myReportsMgr::REPORT_TYPE_USER_USAGE:
			case myReportsMgr::REPORT_TYPE_SPECIFIC_USER_USAGE:
				break;		// handled outside the switch
			
			default:
				throw new Exception("request for invalid report $report_type");
		}
		
		self::init();
		
		$report_def = array(
			self::REPORT_DIMENSION => self::DIMENSION_KUSER_ID, 
			self::REPORT_JOIN_REPORTS => array(
				// storage total
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_STORAGE_USAGE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_METRICS => array(self::METRIC_STORAGE_TOTAL_MB),
				),
				
				// entries total
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
					self::REPORT_METRICS => array(self::METRIC_ENTRIES_TOTAL, self::METRIC_DURATION_TOTAL_MSEC),
				),
			),
		);
		
		list($headers, $data) = self::getTotalImpl($partner_id, $report_def, $input_filter, $object_ids);
		
		return array_combine($headers, $data);
	}
	
	/// custom report functions
	protected static function replaceCustomParams(&$arr, $params)
	{
		foreach ($arr as $key => &$value)
		{
			if (is_array($value))
			{
				self::replaceCustomParams($value, $params);
				continue;
			}

			if ($key != 'value')		// currently, limiting var replacement only for keys called 'value'
			{
				continue;
			}

			if (!is_string($value) || !$value || $value[0] != ':')
			{
				continue;
			}

			$param_name = substr($value, 1);
			if (!isset($params[$param_name]))
			{
				throw new Exception("missing parameter $param_name");
			}
			$value = $params[$param_name];
		}
	}

	protected static function addEntryDescendants($partner_id, $ids)
	{
		$entry_filter = new entryFilter();
		$entry_filter->setPartnerSearchScope($partner_id);
		$entry_filter->set('_in_root_entry_id', $ids);
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entry_filter->attachToCriteria($c);
		$c->applyFilters();

		$result = array_merge($c->getFetchedIds(), explode(',', $ids));

		return implode(',', $result);
	}

	public static function customReport($id, $params)
	{
		self::init();

		self::$custom_reports = kConf::getMap('custom_reports');
		$report_def = self::getReportDef(-$id);

		// get the partner id
		if (isset($report_def[self::REPORT_SKIP_PARTNER_FILTER]))
		{
			$partner_id = Partner::ADMIN_CONSOLE_PARTNER_ID;
		}
		else
		{
			$partner_id = $params['partner_id'];
		}

		// apply param processing
		$optional_params = isset($report_def['report_optional_params']) ? 
			explode(',', $report_def['report_optional_params']) : array();

		foreach ($params as $key => $value)
		{
			if (!isset($report_def[self::REPORT_CUSTOM_PARAM][$key]))
			{
				continue;
			}

			$func = $report_def[self::REPORT_CUSTOM_PARAM][$key][self::REPORT_CUSTOM_PARAM_FUNC];
			$newValue = call_user_func($func, $partner_id, $value);
			if ($newValue == $value)
			{
				continue;
			}

			KalturaLog::log("updating param [$key] from [$value] to [$newValue]");
			$params[$key] = $newValue;
		}

		// build the filter
		$filter_type = isset($report_def['filter']['type']) ? 
			$report_def['filter']['type'] : 'reportsInputFilter'; 
		$input_filter = new $filter_type();
		foreach ($report_def['filter'] as $field => $value)
		{
			if (is_string($value) && $value[0] == ':')
			{
				$param_name = substr($value, 1);
				if (!isset($params[$param_name]))
				{
					if (in_array($param_name, $optional_params))
					{
						continue;
					}
					throw new Exception("missing parameter $param_name");
				}
				$value = $params[$param_name];
			}
			$input_filter->$field = $value;
		}
		
		if (isset($report_def[self::REPORT_ENRICH_DEF]))
		{
			self::replaceCustomParams($report_def[self::REPORT_ENRICH_DEF], $params);
		}

		$object_ids = isset($input_filter->object_ids) ? $input_filter->object_ids : null; 

		if (isset($report_def[self::REPORT_GRAPH_METRICS]))
		{
			// graph report
			$date_column_name = 'date_id';
			if (isset($report_def[self::REPORT_GRANULARITY]))
			{
				$date_column_name = $report_def[self::REPORT_GRANULARITY];

				$date_name_map = array(
					self::GRANULARITY_HOUR => 'hour_id',
					self::GRANULARITY_DAY => 'date_id',
					self::GRANULARITY_MONTH => 'month_id',
				);
				if (isset($date_name_map[$date_column_name]))
				{
					$date_column_name = $date_name_map[$date_column_name];
				}
			}

			$graphs = self::getGraphImpl($partner_id, $report_def, $input_filter, $object_ids);
			list($header, $data) = self::getTableFromGraphs($graphs, false, $date_column_name);
		}
		else if (isset($report_def[self::REPORT_DIMENSION]))
		{
			// table report
			list($header, $data, $totalCount) = self::getTableImpl(
				$partner_id,
				$report_def,
				$input_filter,
				isset($params['limit']) ? $params['limit'] : self::MAX_CUSTOM_REPORT_RESULT_SIZE,
				1,
				$report_def['order_by'],
				$object_ids,
				self::GET_TABLE_FLAG_IS_CSV);
		}
		else
		{
			// total report
			list($header, $data) = self::getTotalImpl(
				$partner_id,
				$report_def,
				$input_filter,
				$object_ids);
			$data = array($data);
		}

		if (isset($report_def['header']))
		{
			$header = explode(',', $report_def['header']);
		}

		return array($header, $data);
	}

	/// csv functions
	protected static function adjustCsvTableUnits($requested_headers, $headers, &$table_data)
	{
		$unit_translation = array(
			'average_storage-Avg Storage (GB)' => 1/1024,
			'bandwidth_consumption-Bandwidth (GB)' => 1/1024,
			'transcoding_consumption-Transcoding (GB)' => 1/1024,
		);

		list($ignore, $requested_headers) = explode(';', $requested_headers);
		$requested_headers = explode(',', $requested_headers);
		
		$translations = array();
		$limit = min(count($requested_headers), count($headers));
		for ($index = 0; $index < $limit; $index++)
		{
			$key = $headers[$index] . '-' . $requested_headers[$index];
			if (isset($unit_translation[$key]))
			{
				$translations[$index] = $unit_translation[$key];
			}
		}
		
		if (!$translations)
		{
			return;
		}
		
		foreach ($table_data as &$row)
		{
			foreach ($translations as $index => $ratio)
			{
				$row[$index] *= $ratio;
			}
		}
	}
	
	protected static function getCsvData(
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

		list($headers_for_total, $headers_for_table) = explode(';', $headers);

		$report_def = self::getReportDef($report_type);

		if (isset($report_def[self::REPORT_JOIN_REPORTS]) || isset($report_def[self::REPORT_JOIN_GRAPHS]) || isset($report_def[self::REPORT_GRAPH_METRICS]))
		{
			$arr = self::getGraph(
				$partner_id,
				$report_type,
				$input_filter,
				$dimension,
				$object_ids);
		}

		if (!empty($headers_for_total))
			list($total_header, $total_data) = self::getTotal(
				$partner_id,
				$report_type,
				$input_filter,
				$object_ids);

		if ($page_index * $page_size > self::MAX_CSV_RESULT_SIZE)
		{
			throw new kCoreException('Exceeded max query size: ' . self::MAX_CSV_RESULT_SIZE, kCoreException::SEARCH_TOO_GENERAL);
		}

		if (!empty($headers_for_table))
		{
			list($table_header, $table_data, $table_total_count) = self::getTable(
				$partner_id,
				$report_type,
				$input_filter,
				$page_size,
				$page_index,
				$order_by,
				$object_ids,
				null,
				true);

			self::adjustCsvTableUnits($headers, $table_header, $table_data);
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
		if (!self::shouldUseKava($partner_id, $report_type))
		{
			return myReportsMgr::getUrlForReportAsCsv(
				$partner_id, 
				$report_title, $report_text, $headers, 
				$report_type, 
				$input_filter, 
				$dimension, 
				$object_ids,
				$page_size, $page_index, $order_by);					
		}
		
		self::init();
		
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
}
