<?php

/**
 * flavorParamsOutput service base test case.
 */
abstract class FlavorParamsOutputServiceTestBase extends KalturaApiTestCase
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
	 * Tests flavorParamsOutput->get action
	 * @param int $id 
	 * @param KalturaFlavorParamsOutput $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaFlavorParamsOutput $reference)
	{
		$resultObject = $this->client->flavorParamsOutput->get($id);
		$this->assertInstanceOf('KalturaFlavorParamsOutput', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaFlavorParamsOutput $reference)
	{
	}

	/**
	 * Tests flavorParamsOutput->listAction action
	 * @param KalturaFlavorParamsOutputFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaFlavorParamsOutputListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
	{
		$resultObject = $this->client->flavorParamsOutput->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaFlavorParamsOutputListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaFlavorParamsOutputFilter $filter = null, KalturaFilterPager $pager = null, KalturaFlavorParamsOutputListResponse $reference)
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
		return new KalturaTestSuite('FlavorParamsOutputServiceTest');
	}

}
