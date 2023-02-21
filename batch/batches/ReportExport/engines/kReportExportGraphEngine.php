<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
class kReportExportGraphEngine extends kReportExportEngine
{

	const GRAPH_BY_DATE_ID = 'by_date_id';
	const GRAPH_BY_NAME = 'by_name';
	const GRAPH_MULTI_BY_DATE_ID = 'multi_by_date_id';
	const GRAPH_MULTI_BY_NAME = 'multi_by_name';

	protected static $report_to_graph_type = array(
		KalturaReportType::CONTENT_DROPOFF => self::GRAPH_BY_NAME,
		KalturaReportType::USER_CONTENT_DROPOFF => self::GRAPH_BY_NAME,
		KalturaReportType::PLATFORMS => self::GRAPH_MULTI_BY_DATE_ID,
		KalturaReportType::OPERATING_SYSTEM => self::GRAPH_MULTI_BY_NAME,
		KalturaReportType::BROWSERS => self::GRAPH_MULTI_BY_NAME,
		KalturaReportType::OPERATING_SYSTEM_FAMILIES => self::GRAPH_MULTI_BY_NAME,
	);

	public function createReport()
	{
		$result =  KBatchBase::$kClient->report->getGraphs($this->reportItem->reportType, $this->reportItem->filter,
			null, $this->reportItem->objectIds, $this->reportItem->responseOptions);
		return $this->buildCsv($result);
	}

	protected function getGraphType()
	{
		if (isset(self::$report_to_graph_type[$this->reportItem->reportType]))
		{
			return self::$report_to_graph_type[$this->reportItem->reportType];
		}
		//assume that if not explicitly set then type is by date id
		return self::GRAPH_BY_DATE_ID;
	}

	protected function buildCsv($result)
	{
		fwrite($this->fp, "\xEF\xBB\xBF");
		$this->writeReportTitle();
		$graphType = $this->getGraphType();
		switch ($graphType)
		{
			case self::GRAPH_BY_DATE_ID:
			case self::GRAPH_BY_NAME:
			case self::GRAPH_MULTI_BY_NAME:
				$this->buildCsvGraphByKey($result);
				break;
			case self::GRAPH_MULTI_BY_DATE_ID:
				$this->buildCsvGraphMultiById($result);
				break;
			default:
				return; //shouldn't get here
		}

		fclose($this->fp);
		return $this->filename;
	}

	protected function buildCsvGraphByKey($result)
	{
		$headers = array('id');
		$rows = array();
		foreach ($result as $graph)
		{
			$headers[] = $graph->id;
			$data = explode(';', $graph->data);
			foreach ($data as $pair)
			{
				if (!$pair)
				{
					continue;
				}
				list($key, $value) = explode($this->getDelimiter(), $pair);
				$rows[$key][] = $value;
			}
		}
		$this->writeRow($headers);

		foreach ($rows as $key => $row)
		{
			$this->writeRow(array_merge(array($key), $row));
		}
	}

	protected function buildCsvGraphMultiById($result)
	{
		$headers = array('id', 'key');
		$rows = array();
		foreach ($result as $graph)
		{
			$headers[] = $graph->id;
			$values = explode(';', $graph->data);
			foreach ($values as $value)
			{
				if (!$value)
				{
					continue;
				}
				$data = explode($this->getDelimiter(), $value);
				$id = reset($data);
				$data = array_slice($data, 1);
				foreach ($data as $pair)
				{
					list($key, $value) = explode(':', $pair);
					$rows[$id][$key][] = $value;
				}
			}
		}

		$this->writeRow($headers);
		foreach ($rows as $id => $keys)
		{
			foreach ($keys as $key => $row)
			{
				$this->writeRow(array_merge(array($id, $key), $row));
			}
		}
	}

}