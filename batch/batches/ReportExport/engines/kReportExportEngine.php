<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
abstract class kReportExportEngine
{
	const DEFAULT_TITLE = 'default';
	const DISCLAIMER_CONFIG_KEY = 'report_filter_disclaimer_message';

	protected $reportItem;
	protected $fp;
	protected $fileName;
	protected $fromDate;
	protected $toDate;

	public function __construct($reportItem, $outputPath)
	{
		$this->reportItem = $reportItem;
		$this->filename = $this->createFileName($outputPath);
		$this->fp = fopen($this->filename, 'w');
		if (!$this->fp)
		{
			throw new KOperationEngineException("Failed to open report file : " . $this->filename);
		}
	}

	abstract public function createReport();
	abstract protected function buildCsv($res);

	protected function getDelimiter()
	{
		if ($this->reportItem->responseOptions && $this->reportItem->responseOptions->delimiter)
		{
			return $this->reportItem->responseOptions->delimiter;
		}
		return ',';
	}

	protected function getTitle()
	{
		if ($this->reportItem->reportTitle)
		{
			return $this->reportItem->reportTitle;
		}
		return self::DEFAULT_TITLE;
	}

	protected function writeReportTitle()
	{
		$this->writeRow("# ------------------------------------");
		$title = $this->getTitle();
		$this->writeRow("Report: $title");
		$this->writeFilterData();
		$this->writeRow("# ------------------------------------");
	}

	protected function writeFilterData()
	{
		$filter = $this->reportItem->filter;
		if (!$filter)
		{
			return;
		}

		$disclaimerMessage = kConf::get(self::DISCLAIMER_CONFIG_KEY, 'local', null);
		if ($disclaimerMessage)
		{
			$this->writeRow($disclaimerMessage);
		}

		if ($filter->toDay && $filter->fromDay)
		{
			$fromDate = date('Y-m-d 00:00:00', strtotime($filter->fromDay));
			$toDate = date('Y-m-d 23:59:59', strtotime($filter->toDay));
			$this->writeRow("Filtered dates: $fromDate - $toDate (GMT)");
		}
		else if ($filter->toDate && $filter->fromDate)
		{
			$fromDate = gmdate('Y-m-d H:i:s', $filter->fromDate);
			$toDate = gmdate('Y-m-d H:i:s', $filter->toDate);
			$this->writeRow("Filtered dates: $fromDate - $toDate (GMT)");
		}

		if ($filter->entryIdIn)
		{
			$entryIds = $filter->entryIdIn;
			$this->writeRow("Filtered entries: $entryIds");
		}

	}

	protected function writeDelimitedRow($row)
	{
		$rowArr = explode($this->getDelimiter(), $row);
		$this->writeRow($rowArr);
	}

	protected function writeRow($row)
	{
		if (!is_array($row))
		{
			$row = array($row);
		}
		KCsvWrapper::sanitizedFputCsv($this->fp, $row);
	}

	protected function createFileName($outputPath)
	{
		$fileName = 'Report_export_' . uniqid();

		return $outputPath.DIRECTORY_SEPARATOR.$fileName;
	}

}
