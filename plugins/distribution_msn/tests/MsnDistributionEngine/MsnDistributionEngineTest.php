<?php

require_once(dirname(__FILE__) . '/../../../../tests/base/bootstrap.php');

/**
 * MsnDistributionEngine test case.
 */
class MsnDistributionEngineTest extends KalturaUnitTestCase
{
	/**
	 * @var MsnDistributionEngine
	 */
	private $MsnDistributionEngine;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$this->MsnDistributionEngine = new MsnDistributionEngine();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->MsnDistributionEngine = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests MsnDistributionEngine->configure()
	 * @param KSchedularTaskConfig $taskConfig
	 * @dataProvider provideData
	 */
	public function testConfigure(KSchedularTaskConfig $taskConfig)
	{
		$this->MsnDistributionEngine->configure($taskConfig);
	}
	
	/**
	 * Tests MsnDistributionEngine->submit()
	 * @param KalturaDistributionSubmitJobData $data
	 * @dataProvider provideData
	 */
	public function testSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->MsnDistributionEngine->submit($data);
	}
	
	/**
	 * Tests MsnDistributionEngine->closeSubmit()
	 * @param KalturaDistributionSubmitJobData $data
	 * @dataProvider provideData
	 */
	public function testCloseSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->MsnDistributionEngine->closeSubmit($data);
	}
	
	/**
	 * Tests MsnDistributionEngine->delete()
	 * @param KalturaDistributionDeleteJobData $data
	 * @dataProvider provideData
	 */
	public function testDelete(KalturaDistributionDeleteJobData $data)
	{
		$this->MsnDistributionEngine->delete($data);
	}
	
	/**
	 * Tests MsnDistributionEngine->closeDelete()
	 * @param KalturaDistributionDeleteJobData $data
	 * @dataProvider provideData
	 */
	public function testCloseDelete(KalturaDistributionDeleteJobData $data)
	{
		$this->MsnDistributionEngine->closeDelete($data);
	}
	
	/**
	 * Tests MsnDistributionEngine->closeUpdate()
	 * @param KalturaDistributionUpdateJobData $data
	 * @dataProvider provideData
	 */
	public function testCloseUpdate(KalturaDistributionUpdateJobData $data)
	{
		$this->MsnDistributionEngine->closeUpdate($data);
	}
	
	/**
	 * Tests MsnDistributionEngine->fetchReport()
	 * @param KalturaDistributionFetchReportJobData $data
	 * @dataProvider provideData
	 */
	public function testFetchReport(KalturaDistributionFetchReportJobData $data)
	{
		$this->MsnDistributionEngine->fetchReport($data);
	}
	
	/**
	 * Tests MsnDistributionEngine->update()
	 * @param KalturaDistributionUpdateJobData $data
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaDistributionUpdateJobData $data)
	{
		$this->MsnDistributionEngine->update($data);
	}
}

