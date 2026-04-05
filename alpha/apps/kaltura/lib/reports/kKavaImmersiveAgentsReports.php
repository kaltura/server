<?php

class kKavaImmersiveAgentsReports extends kKavaReportsMgr
{
	protected static $reports_def = array(

		ReportType::IMMERSIVE_AGENTS_HIGHLIGHTS => array(
			self::REPORT_METRICS => array(self::METRIC_UNIQUE_THREADS, self::EVENT_TYPE_MESSAGE_RESPONSE, self::METRIC_AVG_MESSAGES, self::METRIC_UNIQUE_USERS),
			self::REPORT_GRAPH_METRICS => array(self::METRIC_UNIQUE_THREADS, self::EVENT_TYPE_MESSAGE_RESPONSE, self::METRIC_AVG_MESSAGES, self::METRIC_UNIQUE_USERS),
		),

		ReportType::IMMERSIVE_AGENTS_MESSAGES_OVERTIME => array(
			self::REPORT_GRAPH_METRICS => array(self::EVENT_TYPE_MESSAGE_RESPONSE)
		),

		ReportType::IMMERSIVE_AGENTS_MESSAGE_FEEDBACK => array(
			self::REPORT_DIMENSION_MAP => array(
				'reactionType' => self::DIMENSION_EVENT_VAR2,
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_MESSAGE_FEEDBACK)
		),

		ReportType::IMMERSIVE_AGENTS_TOP_SOURCES => array(
			self::REPORT_DIMENSION_MAP => array(
				'source' => self::DIMENSION_EVENT_MULTI_VAR1,
				'source_name' => self::DIMENSION_EVENT_MULTI_VAR1,
				'source_type' => self::DIMENSION_EVENT_MULTI_VAR1,
			),
			self::REPORT_ENRICH_DEF => array(
				self::REPORT_ENRICH_OUTPUT => array('source_name','source_type'),
				self::REPORT_ENRICH_FUNC => 'kKavaReportsMgr::genericQueryEnrich',
				self::REPORT_ENRICH_CONTEXT => array(
					'peer' => 'entryPeer',
					'columns' => array('NAME', 'MEDIA_TYPE'),
				),
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_MESSAGE_RESPONSE, self::METRIC_UNIQUE_USERS)
		),

		ReportType::IMMERSIVE_AGENTS_AVATAR_SESSIONS => array(
			self::REPORT_METRICS => array(self::EVENT_TYPE_AVATAR_CALL_STARTED, self::METRIC_AVATAR_CALL_DURATION, self::METRIC_AVATAR_AVG_CALL_DURATION),
		),

		ReportType::IMMERSIVE_AGENTS_RESPONSE_EXPERIENCE_TYPES => array(
			self::REPORT_DIMENSION_MAP => array(
				'experience' => self::DIMENSION_EVENT_VAR2,
				'response_type' => self::DIMENSION_EVENT_VAR3,
			),
			self::REPORT_METRICS => array(self::EVENT_TYPE_MESSAGE_RESPONSE),
			self::REPORT_TOTAL_METRICS => array(self::EVENT_TYPE_MESSAGE_RESPONSE, self::METRIC_AVATAR_CALL_MESSAGES, self::METRIC_AVATAR_CHAT_MESSAGES)
		)
	);

	public static function getReportDef($report_type, $input_filter, $response_options = null)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_IMMERSIVE_AGENTS_EVENTS;
		self::initTransformTimeDimensions();

		return $report_def;
	}
}
