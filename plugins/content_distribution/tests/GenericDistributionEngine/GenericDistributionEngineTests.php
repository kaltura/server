<?php

require_once(dirname(__FILE__) . '/../../../../tests/base/bootstrap.php');

/**
 * GenericDistributionEngine test case.
 */
class GenericDistributionEngineTests extends KalturaUnitTestCase
{
	/**
	 * @var GenericDistributionEngine
	 */
	private $GenericDistributionEngine;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->GenericDistributionEngine = new GenericDistributionEngine();
	}
	
	/**
	 * Tests GenericDistributionEngine->configure()
	 * @param KSchedularTaskConfig $taskConfig
	 * @dataProvider provideData
	 */
	public function testConfigure(KSchedularTaskConfig $taskConfig)
	{
		$this->GenericDistributionEngine->configure($taskConfig);
	}
	
	/**
	 * Tests GenericDistributionEngine->submit()
	 * @dataProvider provideData
	 */
	public function testSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->GenericDistributionEngine->submit($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->closeSubmit()
	 * @dataProvider provideData
	 */
	public function testCloseSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->GenericDistributionEngine->closeSubmit($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->delete()
	 * @dataProvider provideData
	 */
	public function testDelete(KalturaDistributionDeleteJobData $data)
	{
		$this->GenericDistributionEngine->delete($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->closeDelete()
	 * @dataProvider provideData
	 */
	public function testCloseDelete(KalturaDistributionDeleteJobData $data)
	{
		$this->GenericDistributionEngine->closeDelete($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->closeReport()
	 * @dataProvider provideData
	 */
	public function testCloseReport(KalturaDistributionFetchReportJobData $data)
	{
		$this->GenericDistributionEngine->closeReport($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->closeUpdate()
	 * @dataProvider provideData
	 */
	public function testCloseUpdate(KalturaDistributionUpdateJobData $data)
	{
		$this->GenericDistributionEngine->closeUpdate($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->fetchReport()
	 * @dataProvider provideData
	 */
	public function testFetchReport(KalturaDistributionFetchReportJobData $data)
	{
		$this->GenericDistributionEngine->fetchReport($data);
	}
	
	/**
	 * Tests GenericDistributionEngine->update()
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaDistributionUpdateJobData $data)
	{
		$this->GenericDistributionEngine->update($data);
	}
}

