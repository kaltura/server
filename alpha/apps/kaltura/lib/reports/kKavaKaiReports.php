<?php

class kKavaKaiReports extends kKavaReportsMgr
{
	protected static $reports_def = array(

		ReportType::GENIE_USAGE_REPORT => array(
			self::REPORT_DIMENSION_MAP => array(
				'Genie ID' => self::DIMENSION_EVENT_VAR1,
			),
			self::REPORT_FILTER => array(
				array(
					self::DRUID_DIMENSION => self::DIMENSION_EVENT_VAR2,
					self::DRUID_VALUES => array(self::SEARCH_ACTION_TYPE)
				),
				array(
					self::DRUID_DIMENSION => self::DIMENSION_KALTURA_APPLICATION,
					self::DRUID_VALUES => array(self::GENIE_APP)
				),
			),
			self::REPORT_METRICS => array(self::METRIC_REQUEST_COUNT, self::METRIC_UNIQUE_USERS)
		),
	);

	public static function getReportDef($report_type, $input_filter)
	{
		$report_def = isset(self::$reports_def[$report_type]) ? self::$reports_def[$report_type] : null;
		if (is_null($report_def))
		{
			return null;
		}

		$report_def[self::REPORT_DATA_SOURCE] = self::DATASOURCE_KAI;
		self::initTransformTimeDimensions();

		return $report_def;
	}

}
