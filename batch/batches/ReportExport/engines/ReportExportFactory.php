<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
class ReportExportFactory
{
	public static function getEngine($reportItem, $outputPath)
	{
		switch ($reportItem->action)
		{
			case KalturaReportExportItemType::TABLE:
				return new kReportExportTableEngine($reportItem, $outputPath);
			case KalturaReportExportItemType::GRAPH:
				return new kReportExportGraphEngine($reportItem, $outputPath);
			case KalturaReportExportItemType::TOTAL:
				return new kReportExportTotalEngine($reportItem, $outputPath);
			default:
				return null;
		}
	}

}
