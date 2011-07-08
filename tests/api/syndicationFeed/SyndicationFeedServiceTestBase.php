<?php

/**
 * syndicationFeed service base test case.
 */
abstract class SyndicationFeedServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setGetActionTestData();
		$this->setUpdateActionTestData();
		$this->setDeleteActionTestData();
		$this->setListActionTestData();
		$this->setGetEntryCountActionTestData();
		$this->setRequestConversionActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testGetEntryCountAction initial data (If needed)
	 */
	protected function setGetEntryCountActionTestData(){}

	/**
	 * Set up the testRequestConversionAction initial data (If needed)
	 */
	protected function setRequestConversionActionTestData(){}

	/**
	 * Tests syndicationFeed->add action
	 * @param KalturaBaseSyndicationFeed $syndicationFeed 
	 * @param KalturaBaseSyndicationFeed $reference 
	 * @return KalturaBaseSyndicationFeed
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
		$resultObject = $this->client->syndicationFeed->add($syndicationFeed);
		$this->assertInstanceOf('KalturaBaseSyndicationFeed', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($syndicationFeed, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
	}

	/**
	 * Tests syndicationFeed->get action
	 * @param string $id 
	 * @param KalturaBaseSyndicationFeed $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaBaseSyndicationFeed $reference)
	{
		$resultObject = $this->client->syndicationFeed->get($id);
		$this->assertInstanceOf('KalturaBaseSyndicationFeed', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaBaseSyndicationFeed $reference)
	{
	}

	/**
	 * Tests syndicationFeed->update action
	 * @param string $id 
	 * @param KalturaBaseSyndicationFeed $syndicationFeed 
	 * @param KalturaBaseSyndicationFeed $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
		$resultObject = $this->client->syndicationFeed->update($id, $syndicationFeed);
		$this->assertInstanceOf('KalturaBaseSyndicationFeed', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $syndicationFeed, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaBaseSyndicationFeed $syndicationFeed, KalturaBaseSyndicationFeed $reference)
	{
	}

	/**
	 * Tests syndicationFeed->delete action
	 * @param string $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->syndicationFeed->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests syndicationFeed->listAction action
	 * @param KalturaBaseSyndicationFeedFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaBaseSyndicationFeedListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseSyndicationFeedListResponse $reference)
	{
		$resultObject = $this->client->syndicationFeed->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaBaseSyndicationFeedListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaBaseSyndicationFeedFilter $filter = null, KalturaFilterPager $pager = null, KalturaBaseSyndicationFeedListResponse $reference)
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
		return new KalturaTestSuite('SyndicationFeedServiceTest');
	}

}
