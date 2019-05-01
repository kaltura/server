<?php

class kKavaReportsDefinition
{
	const CASE_KAVA_REPORT = 0;

	const CUSTOM_REPORTS_CLASS = 'kKavaCustomReports';
	const KAVA_REPORTS_CLASS = 'kKavaReports';

	protected static function getReportClassName($report_type)
	{
		if ($report_type < 0)
		{
			return self::CUSTOM_REPORTS_CLASS;
		}

		$report_class = floor($report_type / 10000);
		switch ($report_class)
		{
			case self::CASE_KAVA_REPORT:
				return self::KAVA_REPORTS_CLASS;

			default:
				return null;
		}
	}

	public static function getReportDef($report_type)
	{
		$report_class = self::getReportClassName($report_type);
		return $report_class::getReportDefinition($report_type);
	}

	public static function isReportDefined($report_type)
	{
		//dynamic enum
		if (!ctype_digit($report_type))
		{
			return false;
		}

		$report_class = self::getReportClassName($report_type);
		//no report class
		if (is_null($report_class))
		{
			return false;
		}

		return $report_class::isReportDefined($report_type);
	}

}
