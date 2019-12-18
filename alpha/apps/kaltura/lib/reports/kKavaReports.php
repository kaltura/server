<?php

class kKavaReports extends kKavaReportsMgr
{

	protected static $reports_def = array(
		ReportType::TOP_CONTENT => array(
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

		ReportType::CONTENT_DROPOFF => array(
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

		ReportType::CONTENT_INTERACTIONS => array(
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

		ReportType::MAP_OVERLAY => array(
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

		ReportType::TOP_SYNDICATION => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_DOMAIN,
				'domain_name' => self::DIMENSION_DOMAIN
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_AVG_DROP_OFF, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_DOMAIN,
			self::REPORT_DRILLDOWN_DIMENSION_MAP => array(
				'referrer' => self::DIMENSION_URL,
			),
		),

		ReportType::USER_ENGAGEMENT => array(
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

		ReportType::SPECIFIC_USER_ENGAGEMENT => array(
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

		ReportType::USER_TOP_CONTENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_SHARE_CLICKED),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_SHARE_CLICKED),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_UNIQUE_USERS, self::METRIC_UNIQUE_ENTRIES, self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_SHARE_CLICKED),
		),

		ReportType::USER_CONTENT_DROPOFF => array(
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

		ReportType::USER_CONTENT_INTERACTIONS => array(
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

		ReportType::APPLICATIONS => array(
			self::REPORT_INTERVAL => '-30/0',
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_APPLICATION
			),
			self::REPORT_METRICS => array(),
		),

		ReportType::PLATFORMS => array(
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

		ReportType::OPERATING_SYSTEM => array(
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

		ReportType::BROWSERS => array(
			self::REPORT_DIMENSION_MAP => array(
				'browser' => self::DIMENSION_BROWSER
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
		),

		ReportType::OPERATING_SYSTEM_FAMILIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'os_family' => self::DIMENSION_OS_FAMILY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
		),

		ReportType::BROWSERS_FAMILIES => array(
			self::REPORT_DIMENSION_MAP => array(
				'browser_family' => self::DIMENSION_BROWSER_FAMILY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_TYPE => self::GRAPH_MULTI_BY_NAME,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_UNIQUE_USERS),
		),

		ReportType::LIVE => array(
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

		ReportType::TOP_PLAYBACK_CONTEXT => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_PLAYBACK_CONTEXT,
				'name' => self::DIMENSION_PLAYBACK_CONTEXT
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('name'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'categoryPeer',
					'int_ids_only' => true,
					'columns' => array('NAME'),
				)
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_UNIQUE_VIEWERS, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self:: METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_VIEWERS, self::METRIC_UNIQUE_PERCENTILES_RATIO),
		),

		ReportType::VPAAS_USAGE => array(
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
				'total_plays' => self::EVENT_TYPE_PLAY,
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
				'total_media_entries' => self::METRIC_PEAK_ENTRIES,
				'total_end_users' => self::METRIC_PEAK_USERS,
				'total_views' => self::EVENT_TYPE_PLAYER_IMPRESSION,
				'origin_bandwidth_consumption' => self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB
			),
		),

		ReportType::VPAAS_USAGE_MULTI => array(
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
			self::REPORT_JOIN_REPORTS => array(
				// unique users
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_API_USAGE,
					self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
					self::REPORT_METRICS => array(self::METRIC_UNIQUE_USERS),
				),

				array(
				self::REPORT_JOIN_GRAPHS => array(

					// transcoding
					array(
						self::REPORT_DATA_SOURCE => self::DATASOURCE_TRANSCODING_USAGE,
						self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
						self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
						self::REPORT_GRAPH_METRICS => array(self::METRIC_TRANSCODING_SIZE_MB),
					),

					// bandwidth
					array(
						self::REPORT_DATA_SOURCE => self::DATASOURCE_BANDWIDTH_USAGE,
						self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
						self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
						self::REPORT_GRAPH_METRICS => array(self::METRIC_BANDWIDTH_SIZE_MB, self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB),
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

					// entries
					array(
						self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
						self::REPORT_GRANULARITY => self::GRANULARITY_DAY,
						self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
						self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_ENTRIES_DELETED),
					),

					array(
						self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
						self::REPORT_INTERVAL => self::INTERVAL_BASE_TO_START,
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
				self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_BANDWIDTH_SIZE_MB, self::METRIC_AVERAGE_STORAGE_MB, self::METRIC_TRANSCODING_SIZE_MB, self::METRIC_PEAK_ENTRIES, self::METRIC_PEAK_USERS, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB, self::METRIC_STORAGE_ADDED_MB, self::METRIC_STORAGE_DELETED_MB, self::METRIC_PEAK_STORAGE_MB,)
				),
			),
			self::REPORT_COLUMN_MAP => array(
				'total_plays' => self::EVENT_TYPE_PLAY,
				'bandwidth_consumption' => self::METRIC_BANDWIDTH_SIZE_MB,
				'average_storage' => self::METRIC_AVERAGE_STORAGE_MB,
				'transcoding_consumption' => self::METRIC_TRANSCODING_SIZE_MB,
				'total_entries' => self::METRIC_PEAK_ENTRIES,
				'total_end_users' => self::METRIC_PEAK_USERS,
				'total_views' => self::EVENT_TYPE_PLAYER_IMPRESSION,
				'origin_bandwidth_consumption' => self::METRIC_ORIGIN_BANDWIDTH_SIZE_MB,
				'added_storage' => self::METRIC_STORAGE_ADDED_MB,
				'deleted_storage' => self::METRIC_STORAGE_DELETED_MB,
				'peak_storage' => self::METRIC_PEAK_STORAGE_MB,
				'unique_known_users' => self::METRIC_UNIQUE_USERS,
			),
		),

		ReportType::TOP_CONTRIBUTORS => array(
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

		ReportType::CONTENT_CONTRIBUTIONS => array(
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

		ReportType::TOP_CREATORS => array(
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

		ReportType::USER_USAGE => array(
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

		ReportType::SPECIFIC_USER_USAGE => array(
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

		ReportType::PARTNER_USAGE => array(
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

		ReportType::ENTRY_USAGE => array(
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
				'count loads' => self::EVENT_TYPE_PLAYER_IMPRESSION,
				'count plays' => self::EVENT_TYPE_PLAY,
				'count media' => self::METRIC_COUNT_TOTAL,
				'count media all time' => self::METRIC_COUNT_TOTAL_ALL_TIME,
				'count video' => self::MEDIA_TYPE_VIDEO,
				'count image' => self::MEDIA_TYPE_IMAGE,
				'count audio' => self::MEDIA_TYPE_AUDIO,
				'count mix' => self::MEDIA_TYPE_SHOW,
				'count bandwidth mb' => self::METRIC_BANDWIDTH_SIZE_MB,
				'added storage mb' => self::METRIC_STORAGE_ADDED_MB,
				'deleted storage mb' => self::METRIC_STORAGE_DELETED_MB,
				'peak storage mb' => self::METRIC_PEAK_STORAGE_MB,
				'average storage mb' => self::METRIC_AVERAGE_STORAGE_MB,
				'combined bandwidth storage' => self::METRIC_BANDWIDTH_STORAGE_MB,
				'transcoding mb' => self::METRIC_TRANSCODING_SIZE_MB,
			),
		),

		ReportType::VAR_USAGE => array(
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

		ReportType::REACH_USAGE => array(
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

		ReportType::TOP_CUSTOM_VAR1 => array(
			self::REPORT_DIMENSION_MAP => array('custom_var1' => self::DIMENSION_CUSTOM_VAR1),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_CUSTOM_VAR1,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
		),

		ReportType::MAP_OVERLAY_CITY => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'city' => self::DIMENSION_LOCATION_CITY,
				'coordinates' => self::DIMENSION_LOCATION_CITY,
				'country_code' => self::DIMENSION_LOCATION_COUNTRY,
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_VIEWERS, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => 'country_code',
					self::REPORT_ENRICH_FUNC => self::ENRICH_FOREACH_KEYS_FUNC,
					self::REPORT_ENRICH_CONTEXT => 'kKavaCountryCodes::toShortName',
				),
				array(
					self::REPORT_ENRICH_INPUT =>  array('country','region','city'),
					self::REPORT_ENRICH_OUTPUT => 'coordinates',
					self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
				)
			),
			self::REPORT_COLUMN_MAP => array(
				'count_plays' => self::EVENT_TYPE_PLAY,
				'count_plays_25' => self::EVENT_TYPE_PLAYTHROUGH_25,
				'count_plays_50' => self::EVENT_TYPE_PLAYTHROUGH_50,
				'count_plays_75' => self::EVENT_TYPE_PLAYTHROUGH_75,
				'count_plays_100' => self::EVENT_TYPE_PLAYTHROUGH_100,
				'play_through_ratio' => self::METRIC_PLAYTHROUGH_RATIO,
				'unique_known_users' => self::METRIC_UNIQUE_VIEWERS,
				'avg_view_drop_off' => self::METRIC_AVG_DROP_OFF,
				'count_loads' => self::EVENT_TYPE_PLAYER_IMPRESSION,
			),
		),

		ReportType::USER_ENGAGEMENT_TIMELINE => array(
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_UNIQUE_VIEWERS, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_UNIQUE_VIEWERS, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_COLUMN_MAP => array(
				'count_plays' => self::EVENT_TYPE_PLAY,
				'sum_time_viewed' => self::METRIC_QUARTILE_PLAY_TIME,
				'unique_known_users' => self::METRIC_UNIQUE_VIEWERS,
				'avg_view_drop_off' => self::METRIC_AVG_DROP_OFF,
				'avg_completion_rate' => self::METRIC_UNIQUE_PERCENTILES_RATIO,
				'count_loads' => self::EVENT_TYPE_PLAYER_IMPRESSION,
			),
		),

		ReportType::UNIQUE_USERS_PLAY => array(
			self::REPORT_FILTER => array(
				self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
				self::DRUID_VALUES => array(self::EVENT_TYPE_PLAY)
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_USERS),
		),

		ReportType::APP_DOMAIN_UNIQUE_ACTIVE_USERS => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_API_USAGE,
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_PARTNER_ID,
			self::REPORT_DIMENSION_MAP => array(
				'application' => self::DIMENSION_APPLICATION,
				'domain' => self::DIMENSION_DOMAIN
			),
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_USERS)
		),

		ReportType::MAP_OVERLAY_COUNTRY => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' =>  self::DIMENSION_LOCATION_COUNTRY,
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'coordinates' => self::DIMENSION_LOCATION_COUNTRY
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_VIEWERS, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION),
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
			),
			self::REPORT_COLUMN_MAP => array(
				'count_plays' => self::EVENT_TYPE_PLAY,
				'count_plays_25' => self::EVENT_TYPE_PLAYTHROUGH_25,
				'count_plays_50' => self::EVENT_TYPE_PLAYTHROUGH_50,
				'count_plays_75' => self::EVENT_TYPE_PLAYTHROUGH_75,
				'count_plays_100' => self::EVENT_TYPE_PLAYTHROUGH_100,
				'play_through_ratio' => self::METRIC_PLAYTHROUGH_RATIO,
				'unique_known_users' => self::METRIC_UNIQUE_VIEWERS,
				'avg_view_drop_off' => self::METRIC_AVG_DROP_OFF,
				'count_loads' => self::EVENT_TYPE_PLAYER_IMPRESSION,
			),
		),

		ReportType::MAP_OVERLAY_REGION => array(
			self::REPORT_DIMENSION_MAP => array(
				'country' => self::DIMENSION_LOCATION_COUNTRY,
				'region' => self::DIMENSION_LOCATION_REGION,
				'coordinates' => self::DIMENSION_LOCATION_REGION
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYTHROUGH_25, self::EVENT_TYPE_PLAYTHROUGH_50, self::EVENT_TYPE_PLAYTHROUGH_75, self::EVENT_TYPE_PLAYTHROUGH_100, self::METRIC_PLAYTHROUGH_RATIO, self::METRIC_UNIQUE_VIEWERS, self::METRIC_AVG_DROP_OFF, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_INPUT =>  array('country', 'region'),
				self::REPORT_ENRICH_OUTPUT => 'coordinates',
				self::REPORT_ENRICH_FUNC => 'self::getCoordinates',
			),
			self::REPORT_COLUMN_MAP => array(
				'count_plays' => self::EVENT_TYPE_PLAY,
				'count_plays_25' => self::EVENT_TYPE_PLAYTHROUGH_25,
				'count_plays_50' => self::EVENT_TYPE_PLAYTHROUGH_50,
				'count_plays_75' => self::EVENT_TYPE_PLAYTHROUGH_75,
				'count_plays_100' => self::EVENT_TYPE_PLAYTHROUGH_100,
				'play_through_ratio' => self::METRIC_PLAYTHROUGH_RATIO,
				'unique_known_users' => self::METRIC_UNIQUE_VIEWERS,
				'avg_view_drop_off' => self::METRIC_AVG_DROP_OFF,
				'count_loads' => self::EVENT_TYPE_PLAYER_IMPRESSION,
			),
		),

		ReportType::TOP_CONTENT_CREATOR => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'creator_name' => self::DIMENSION_ENTRY_ID,
				'created_at' => self::DIMENSION_ENTRY_ID,
				'status' => self::DIMENSION_ENTRY_ID,
				'media_type' => self::DIMENSION_ENTRY_ID,
				'duration_msecs' => self::DIMENSION_ENTRY_ID,
				'entry_source' => self::DIMENSION_ENTRY_ID,
			),

			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name', 'creator_name', 'created_at', 'status', 'media_type', 'duration_msecs', 'entry_source'),
					self::REPORT_ENRICH_FUNC => 'self::getEntriesSource',
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('NAME', 'KUSER_ID', '@CREATED_AT', 'STATUS', 'MEDIA_TYPE', 'LENGTH_IN_MSECS'),
					)
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('creator_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)'),
						'peer' => 'kuserPeer',
					)
				)
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS, self::METRIC_ENGAGEMENT_RANKING, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION),
			self::REPORT_TOTAL_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_PLAYER_IMPRESSION_RATIO, self::METRIC_AVG_DROP_OFF, self::METRIC_UNIQUE_USERS),
		),

		ReportType::TOP_CONTENT_CONTRIBUTORS => array(
			self::REPORT_DIMENSION_MAP => array(
				'user_id' => self::DIMENSION_KUSER_ID,
				'creator_name' => self::DIMENSION_KUSER_ID,
				'created_at' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('user_id', 'creator_name', 'created_at'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'columns' => array('PUSER_ID', 'IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)', '@CREATED_AT'),
					'peer' => 'kuserPeer',
				)
			),
			self::REPORT_JOIN_REPORTS => array(
				// plays
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_DIMENSION => self::DIMENSION_ENTRY_CREATOR_ID,
					self::REPORT_FILTER => array(
						self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
						self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_LIVE_STREAM, self::MEDIA_TYPE_LIVE_WIN_MEDIA, self::MEDIA_TYPE_LIVE_REAL_MEDIA, self::MEDIA_TYPE_LIVE_QUICKTIME)
					),
					self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::METRIC_PLAYS_RANKING),
					self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY),
					self::REPORT_TOTAL_METRICS => array(self::EVENT_TYPE_PLAY),
				),

				// entries & msecs added
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_FILTER => array(
						array(
							self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
							self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_LIVE_STREAM, self::MEDIA_TYPE_LIVE_WIN_MEDIA, self::MEDIA_TYPE_LIVE_REAL_MEDIA, self::MEDIA_TYPE_LIVE_QUICKTIME)
						),
						array(
							self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
							self::DRUID_VALUES => array(self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_STATUS)
						),
					),
					self::REPORT_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_ENTRIES_RANKING),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_UNIQUE_CONTRIBUTORS),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_UNIQUE_CONTRIBUTORS),
				),
			),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addContributorRankingColumn',
			self::REPORT_TABLE_MAP => array(
				'count_plays' => self::EVENT_TYPE_PLAY,
				'added_entries' => self::METRIC_ENTRIES_ADDED,
				'added_msecs' => self::METRIC_DURATION_ADDED_MSEC,
				'contributor_ranking' => self::METRIC_CONTRIBUTOR_RANKING,
			)
		),

		ReportType::TOP_SOURCES => array(
			self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
			self::REPORT_DIMENSION_MAP => array(
				'source' => self::DIMENSION_SOURCE_TYPE
			),
			self::REPORT_FILTER => array(
				array(
					self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
					self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_LIVE_STREAM, self::MEDIA_TYPE_LIVE_WIN_MEDIA, self::MEDIA_TYPE_LIVE_REAL_MEDIA, self::MEDIA_TYPE_LIVE_QUICKTIME)
				),
				array(
					self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
					self::DRUID_VALUES => array(self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_STATUS)
				),
			),
			self::REPORT_METRICS => array(self::METRIC_ENTRIES_ADDED, self::METRIC_DURATION_ADDED_MSEC, self::METRIC_UNIQUE_CONTRIBUTORS),
		),

		ReportType::PERCENTILES => array(
			self::REPORT_DIMENSION_MAP => array(
				'percentile' => self::DIMENSION_PERCENTILES
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_VIEW_PERIOD, self::METRIC_UNIQUE_USERS),
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addZeroPercentiles',
		),

		ReportType::CONTENT_REPORT_REASONS => array(
			self::REPORT_DIMENSION_MAP => array(
				'reason' => self::DIMENSION_EVENT_VAR1
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_REPORT_SUBMITTED),
		),

		ReportType::PLAYER_RELATED_INTERACTIONS => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'status' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('entry_name', 'status'),
				self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'entryPeer',
					'columns' => array('NAME', 'STATUS'),
				)
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_SUBMITTED, self::EVENT_TYPE_CAPTIONS, self::EVENT_TYPE_INFO, self::EVENT_TYPE_RELATED_SELECTED),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_EDIT_CLICKED, self::EVENT_TYPE_SHARE_CLICKED, self::EVENT_TYPE_DOWNLOAD_CLICKED, self::EVENT_TYPE_REPORT_SUBMITTED, self::EVENT_TYPE_CAPTIONS, self::EVENT_TYPE_INFO, self::EVENT_TYPE_RELATED_SELECTED),
		),

		ReportType::PLAYBACK_RATE => array(
			self::REPORT_DIMENSION_MAP => array(
				'playback_rate' => self::DIMENSION_EVENT_VAR1
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_SPEED),
		),

		ReportType::TOP_USER_CONTENT => array(
			self::REPORT_DIMENSION_MAP => array(
				'object_id' => self::DIMENSION_ENTRY_ID,
				'entry_name' => self::DIMENSION_ENTRY_ID,
				'creator_name' => self::DIMENSION_ENTRY_ID,
				'created_at' => self::DIMENSION_ENTRY_ID,
				'status' => self::DIMENSION_ENTRY_ID,
				'media_type' => self::DIMENSION_ENTRY_ID,
				'duration_msecs' => self::DIMENSION_ENTRY_ID,
			),
			self::REPORT_ENRICH_DEF => array(
				array(
					self::REPORT_ENRICH_OUTPUT => array('entry_name', 'creator_name', 'created_at', 'status', 'media_type', 'duration_msecs'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'peer' => 'entryPeer',
						'columns' => array('NAME', 'KUSER_ID', '@CREATED_AT', 'STATUS', 'MEDIA_TYPE', 'LENGTH_IN_MSECS'),
					)
				),
				array(
					self::REPORT_ENRICH_OUTPUT => array('creator_name'),
					self::REPORT_ENRICH_FUNC => 'self::genericQueryEnrich',
					self::REPORT_ENRICH_CONTEXT => array(
						'columns' => array('IFNULL(TRIM(CONCAT(FIRST_NAME, " ", LAST_NAME)), PUSER_ID)'),
						'peer' => 'kuserPeer',
					)
				)
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_FORCE_TOTAL_COUNT => true,
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_UNIQUE_PERCENTILES_RATIO),
		),

		ReportType::USER_HIGHLIGHTS => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_JOIN_REPORTS => array(
				// player events metrics
				array(
					self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_SHARE_CLICKED),
					self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_QUARTILE_PLAY_TIME, self::METRIC_AVG_PLAY_TIME, self::METRIC_UNIQUE_PERCENTILES_RATIO, self::EVENT_TYPE_SHARE_CLICKED),
				),

				// entries added
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_ENTRY_LIFECYCLE,
					self::REPORT_FILTER => array(
						array(
							self::DRUID_DIMENSION => self::DIMENSION_MEDIA_TYPE,
							self::DRUID_VALUES => array(self::MEDIA_TYPE_VIDEO, self::MEDIA_TYPE_AUDIO, self::MEDIA_TYPE_LIVE_STREAM, self::MEDIA_TYPE_LIVE_WIN_MEDIA, self::MEDIA_TYPE_LIVE_REAL_MEDIA, self::MEDIA_TYPE_LIVE_QUICKTIME)
						),
						array(
							self::DRUID_DIMENSION => self::DIMENSION_EVENT_TYPE,
							self::DRUID_VALUES => array(self::EVENT_TYPE_PHYSICAL_ADD, self::EVENT_TYPE_STATUS)
						),
					),
					self::REPORT_METRICS => array(self::METRIC_ENTRIES_ADDED),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_ENTRIES_ADDED),
				),
			),
		),

		ReportType::USER_INTERACTIVE_VIDEO => array(
			self::REPORT_DIMENSION_MAP => array(
				'name' => self::DIMENSION_KUSER_ID
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => 'name',
				self::REPORT_ENRICH_FUNC => 'self::getUsersInfo'
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_VIEW_PERIOD_PLAY_TIME, self::METRIC_AVG_VIEW_PERIOD_PLAY_TIME),
			self::REPORT_TOTAL_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_VIEW_PERIOD_PLAY_TIME, self::METRIC_AVG_VIEW_PERIOD_PLAY_TIME, self::METRIC_UNIQUE_VIEWERS),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_PLAY, self::EVENT_TYPE_PLAYER_IMPRESSION, self::METRIC_VIEW_PERIOD_PLAY_TIME, self::METRIC_AVG_VIEW_PERIOD_PLAY_TIME),
		),

		ReportType::INTERACTIVE_VIDEO_TOP_NODES => array(
			self::REPORT_DIMENSION_MAP => array(
				'node_id' => self::DIMENSION_NODE_ID,
			),
			self::REPORT_FILTER_DIMENSION => self::DIMENSION_NODE_ID,
			self::REPORT_METRICS => array(self::EVENT_TYPE_NODE_PLAY, self::METRIC_UNIQUE_USERS, self::METRIC_NODE_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_NODE_PLAY, self::METRIC_UNIQUE_USERS, self::METRIC_NODE_UNIQUE_PERCENTILES_RATIO),
			self::REPORT_COLUMN_MAP => array(
				'count_node_plays' => self::EVENT_TYPE_NODE_PLAY,
				'unique_known_users' => self::METRIC_UNIQUE_USERS,
				'avg_completion_rate' => self::METRIC_NODE_UNIQUE_PERCENTILES_RATIO,
			),
		),
	);

	public static function getReportDef($report_type)
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
