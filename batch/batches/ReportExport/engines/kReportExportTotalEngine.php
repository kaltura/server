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
		$headersArr = explode($this->getDelimiter(), $headers);
		if ($this->reportItem->responseOptions->useFriendlyHeadersNames)
		{
			$headersArr = $this->mapHeadersNames($headersArr);
		}
		$this->writeRow($headersArr);
		$this->writeDelimitedRow($result->data);
		fclose($this->fp);
		return $this->filename;
	}

}
