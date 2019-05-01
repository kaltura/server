<?php

class kKavaCustomReports extends kKavaReportsMgr
{

	protected static $custom_reports = null;

	protected static function initMap()
	{
		if (is_null(self::$custom_reports))
		{
			self::$custom_reports = kConf::getMap('custom_reports');
		}
	}

	public static function isReportDefined($report_type)
	{
		self::initMap();
		return isset(self::$custom_reports[-$report_type]);
	}

	public static function getReportDefinition($report_type)
	{
		self::initMap();
		$report_def = self::$custom_reports[-$report_type];
		return self::getReportDef($report_def);
	}

}
