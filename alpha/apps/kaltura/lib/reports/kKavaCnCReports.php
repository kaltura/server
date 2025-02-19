<?php

class kKavaCnCReports extends kKavaReportsMgr
{
	protected static $reports_def = array(

		ReportType::CNC_PARTICIPATION => array(
			self::REPORT_METRICS => array(self::EVENT_TYPE_REACTION_CLICKED, self::METRIC_REACTION_CLICKED_UNIQUE_USERS, self::EVENT_TYPE_GROUP_MESSAGE_SENT, self::METRIC_UNIQUE_SENT_GROUP_MESSAGE_USERS, self::METRIC_UNIQUE_ANSWERED_POLL_USERS, self::METRIC_UNIQUE_SENT_Q_AND_A_USERS, self::METRIC_Q_AND_A_THREADS_COUNT, self::METRIC_UNIQUE_PRIVATE_MESSAGE_SENT_USERS, self::METRIC_UNIQUE_LOGGED_IN_USERS),
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_REACTION_CLICKED, self::METRIC_REACTION_CLICKED_UNIQUE_USERS, self::EVENT_TYPE_GROUP_MESSAGE_SENT, self::METRIC_UNIQUE_SENT_GROUP_MESSAGE_USERS, self::METRIC_UNIQUE_ANSWERED_POLL_USERS, self::METRIC_UNIQUE_SENT_Q_AND_A_USERS, self::METRIC_Q_AND_A_THREADS_COUNT, self::METRIC_UNIQUE_PRIVATE_MESSAGE_SENT_USERS, self::METRIC_UNIQUE_LOGGED_IN_USERS)
		),
	);

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_CNC_EVENTS;
		self::initTransformTimeDimensions();

		return $report_def;
	}

}
