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