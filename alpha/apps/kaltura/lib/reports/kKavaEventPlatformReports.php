<?php

class kKavaEventPlatformReports extends kKavaReportsMgr
{
	protected static $reports_def = array(
		ReportType::EP_WEBCAST_HIGHLIGHTS => array(
			self::REPORT_JOIN_REPORTS => array(
				// player events
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_HISTORICAL,
					self::REPORT_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME),
					self::REPORT_GRAPH_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME),
					self::REPORT_TOTAL_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_LIVE_VIEW_PERIOD_PLAY_TIME),
				),
				// kme
				array(
					self::REPORT_DATA_SOURCE => self::DATASOURCE_MEETING_HISTORICAL,
					self::REPORT_GRAPH_METRICS => array(self::METRIC_MEETING_VIEW_TIME),
					self::REPORT_METRICS => array(self::METRIC_MEETING_VIEW_TIME),
				),
			),
			self::REPORT_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_COMBINED_LIVE_VIEW_TIME, self::METRIC_COMBINED_VOD_LIVE_VIEW_TIME),
			self::REPORT_TOTAL_METRICS => array(self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME, self::METRIC_COMBINED_LIVE_VIEW_TIME, self::METRIC_COMBINED_VOD_LIVE_VIEW_TIME),
			self::REPORT_GRAPH_FINALIZE_FUNC => 'self::addCombinedLiveVodGraph',
			self::REPORT_TABLE_FINALIZE_FUNC => 'self::addCombinedLiveVodColumn',
			self::REPORT_TOTAL_FINALIZE_FUNC => 'self::addTotalCombinedLiveVodColumn',
			self::REPORT_COLUMN_MAP => array(
				'vod_view_time' => self::METRIC_VOD_VIEW_PERIOD_PLAY_TIME,
				'live_view_time' => self::METRIC_COMBINED_LIVE_VIEW_TIME,
				'vod_live_view_time' => self::METRIC_COMBINED_VOD_LIVE_VIEW_TIME,
			),
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
			self::REPORT_METRICS => array(self::METRIC_COMBINED_LIVE_VIEW_PERIOD_COUNT, self::METRIC_COMBINED_LIVE_ENGAGED_USERS_RATIO),
			self::REPORT_TABLE_FINALIZE_FUNC => "self::addZeroMinutes",
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