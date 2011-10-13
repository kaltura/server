<?php

/**
 * synacorHbo service base test case.
 */
abstract class SynacorHboServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setGetFeedActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testGetFeedAction initial data (If needed)
	 */
	protected function setGetFeedActionTestData(){}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * TODO: replace testAddFromEntry with last test function that uses that id
	 * @depends testAddFromEntry
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * 
	 * Returns the suite for the test
	 */
	public static function suite()
	{
		return new KalturaTestSuite('SynacorHboServiceTest');
	}

}
