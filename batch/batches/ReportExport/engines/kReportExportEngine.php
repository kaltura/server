<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
abstract class kReportExportEngine
{
	const DEFAULT_TITLE = 'default';

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
		if ($filter && $filter->toDay && $filter->fromDay)
		{
			$fromDate = strtotime(date('Y-m-d 00:00:00', strtotime($filter->fromDay)));
			$toDate = strtotime(date('Y-m-d 23:59:59', strtotime($filter->toDay)));
			$this->writeRow("Filtered dates (Unix time): $fromDate - $toDate");
		}
		else if ($filter && $filter->toDate && $filter->fromDate)
		{
			$this->writeRow("Filtered dates (Unix time): $filter->fromDate - $filter->toDate");
		}
	}

	protected function getFileUniqueId()
	{
		$id = print_r($this->reportItem, true);
		$id .= time();
		return md5($id);
	}

	protected function writeDelimitedRow($row)
	{
		if ($this->getDelimiter() == ',')
		{
			$this->writeRow($row);
		}
		else
		{
			$rowArr = explode($this->getDelimiter(), $row);
			$this->writeRow(implode(',', $rowArr));
		}
	}

	protected function writeRow($row)
	{
		fwrite($this->fp, $row."\n");
	}

	protected function createFileName($outputPath)
	{
		$fileName = 'Report_export_' .  $this->getFileUniqueId();

		return $outputPath.DIRECTORY_SEPARATOR.$fileName;
	}

}
