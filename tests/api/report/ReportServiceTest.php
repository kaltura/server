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
	 * @param KalturaReportGraphArray $reference
	 * @dataProvider provideData
	 */
	public function testGetGraphs($reportType, KalturaReportInputFilter $reportInputFilter, $dimension = null, $objectIds = null, KalturaReportGraphArray $reference)
	{
		$resultObject = $this->client->report->getGraphs($reportType, $reportInputFilter, $dimension, $objectIds, $reference);
		$this->assertType('KalturaReportGraphArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests report->getTotal action
	 * @param KalturaReportType $reportType
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param string $objectIds
	 * @param KalturaReportTotal $reference
	 * @dataProvider provideData
	 */
	public function testGetTotal($reportType, KalturaReportInputFilter $reportInputFilter, $objectIds = null, KalturaReportTotal $reference)
	{
		$resultObject = $this->client->report->getTotal($reportType, $reportInputFilter, $objectIds, $reference);
		$this->assertType('KalturaReportTotal', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests report->getTable action
	 * @param KalturaReportType $reportType
	 * @param KalturaReportInputFilter $reportInputFilter
	 * @param KalturaFilterPager $pager
	 * @param string $order
	 * @param string $objectIds
	 * @param KalturaReportTable $reference
	 * @dataProvider provideData
	 */
	public function testGetTable($reportType, KalturaReportInputFilter $reportInputFilter, KalturaFilterPager $pager, $order = null, $objectIds = null, KalturaReportTable $reference)
	{
		$resultObject = $this->client->report->getTable($reportType, $reportInputFilter, $pager, $order, $objectIds, $reference);
		$this->assertType('KalturaReportTable', $resultObject);
		// TODO - add here your own validations
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
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testGetUrlForReportAsCsv($reportTitle, $reportText, $headers, $reportType, KalturaReportInputFilter $reportInputFilter, $dimension = null, KalturaFilterPager $pager = null, $order = null, $objectIds = null, $reference)
	{
		$resultObject = $this->client->report->getUrlForReportAsCsv($reportTitle, $reportText, $headers, $reportType, $reportInputFilter, $dimension, $pager, $order, $objectIds, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
