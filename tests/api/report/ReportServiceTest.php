<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/ReportServiceBaseTest.php');

/**
 * report service test case.
 */
class ReportServiceTest extends ReportServiceBaseTest
{
	/**
	 * Tests report->getGraphs action
	 * @param KalturaReportType $reportType
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $dimension
	 * @param string $objectIds
	 * @dataProvider provideData
	 */
	public function testGetGraphs(KalturaReportType $reportType, KalturaReportInputFilter $reportInputFilter, $dimension = null, $objectIds = null)
	{
		$resultObject = $this->client->report->getGraphs($reportType, $reportInputFilter, $dimension, $objectIds);
		$this->assertType('KalturaReportGraphArray', $resultObject);
	}

	/**
	 * Tests report->getTotal action
	 * @param KalturaReportType $reportType
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $objectIds
	 * @dataProvider provideData
	 */
	public function testGetTotal(KalturaReportType $reportType, KalturaReportInputFilter $reportInputFilter, $objectIds = null)
	{
		$resultObject = $this->client->report->getTotal($reportType, $reportInputFilter, $objectIds);
		$this->assertType('KalturaReportTotal', $resultObject);
	}

	/**
	 * Tests report->getTable action
	 * @param KalturaReportType $reportType
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param KalturaFilterPager $pager
	 * @param string $order
	 * @param string $objectIds
	 * @dataProvider provideData
	 */
	public function testGetTable(KalturaReportType $reportType, KalturaReportInputFilter $reportInputFilter, KalturaFilterPager $pager, $order = null, $objectIds = null)
	{
		$resultObject = $this->client->report->getTable($reportType, $reportInputFilter, $pager, $order, $objectIds);
		$this->assertType('KalturaReportTable', $resultObject);
	}

	/**
	 * Tests report->getUrlForReportAsCsv action
	 * @param string $reportTitle
	 * @param string $reportText
	 * @param string $headers
	 * @param KalturaReportType $reportType
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $dimension
	 * @param KalturaFilterPager $pager
	 * @param string $order
	 * @param string $objectIds
	 * @dataProvider provideData
	 */
	public function testGetUrlForReportAsCsv($reportTitle, $reportText, $headers, KalturaReportType $reportType, KalturaReportInputFilter $reportInputFilter, $dimension = null, KalturaFilterPager $pager = null, $order = null, $objectIds = null)
	{
		$resultObject = $this->client->report->getUrlForReportAsCsv($reportTitle, $reportText, $headers, $reportType, $reportInputFilter, $dimension, $pager, $order, $objectIds);
		$this->assertType('string', $resultObject);
	}

}
