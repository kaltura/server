<?php

require_once(dirname(__FILE__) . '/../../bootstrap/bootstrapApi.php');

/**
 * The DWH sanity test case
 * tests if decision layer makes a right decision about converting and validating files 
 * @author Roni
 *
 */
class dwhTest extends KalturaApiTestCase
{
	/**
	 * 
	 * Creates a new DWH Test case
	 * @param string $name
	 * @param array<unknown_type> $data
	 * @param string $dataName
	 */
	public function __construct($name = "dwhTest", array $data = array(), $dataName ="Default data")
	{
		parent::__construct($name, $data, $dataName);
	}
	
	/**
	 * 
	 * Test the DWH Checks that the starting calls return okay
	 * @param array<unknown_type> $params
	 * @param array<unknown_type> $results
	 * @dataProvider provideData
	 */
	public function testGraph(KalturaReportInputFilter $reportInputFilter, $expectedGraphArray)
	{
		$graphArray = $this->client->report->getGraphs(KalturaReportType::TOP_CONTENT, $reportInputFilter);
		$this->compareOnField("plays", $graphArray, array(), "assertEquals");
	}
	
/**
	 * 
	 * Test the DWH Checks that the starting calls return okay
	 * @param array<unknown_type> $params
	 * @param array<unknown_type> $results
	 * @dataProvider provideData
	 */
	public function testTotal(KalturaReportInputFilter $reportInputFilter, KalturaReportTotal $expectedTotal)
	{
		$total  = $this->client->report->getTotal(KalturaReportType::TOP_CONTENT, $reportInputFilter);
		$this->CompareAPIObjects($expectedTotal, $total);
	}
}
