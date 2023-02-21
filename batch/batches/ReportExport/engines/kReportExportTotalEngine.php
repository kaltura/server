<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
class kReportExportTotalEngine extends kReportExportEngine
{
	public function createReport()
	{
		$result =  KBatchBase::$kClient->report->getTotal($this->reportItem->reportType, $this->reportItem->filter,
			$this->reportItem->objectIds, $this->reportItem->responseOptions);
		return $this->buildCsv($result);
	}

	protected function buildCsv($result)
	{
		fwrite($this->fp, "\xEF\xBB\xBF");
		$this->writeReportTitle();
		$this->writeDelimitedRow($result->header);
		$this->writeDelimitedRow($result->data);
		fclose($this->fp);
		return $this->filename;
	}

}
