<?php

/**
 * thumbParamsOutput service base test case.
 */
abstract class ThumbParamsOutputServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setGetActionTestData();
		$this->setListActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Tests thumbParamsOutput->get action
	 * @param int $id 
	 * @param KalturaThumbParamsOutput $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaThumbParamsOutput $reference)
	{
		$resultObject = $this->client->thumbParamsOutput->get($id);
		$this->assertInstanceOf('KalturaThumbParamsOutput', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaThumbParamsOutput $reference)
	{
	}

	/**
	 * Tests thumbParamsOutput->listAction action
	 * @param KalturaThumbParamsOutputFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaThumbParamsOutputListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
		$resultObject = $this->client->thumbParamsOutput->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaThumbParamsOutputListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaThumbParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaThumbParamsOutputListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * TODO: replace testAdd with last test function that uses that id
	 * @depends testAdd
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
		return new KalturaTestSuite('ThumbParamsOutputServiceTest');
	}

}
