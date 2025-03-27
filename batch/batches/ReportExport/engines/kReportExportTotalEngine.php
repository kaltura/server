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
		$headers = $result->header;
		if ($this->reportItem->responseOptions->useFriendlyHeader)
		{
			//TODO: map headers to friendlyName
		}
		$this->writeDelimitedRow($headers);
		$this->writeDelimitedRow($result->data);
		fclose($this->fp);
		return $this->filename;
	}

}
