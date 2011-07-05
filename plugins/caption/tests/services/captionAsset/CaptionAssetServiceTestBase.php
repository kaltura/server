<?php

/**
 * captionAsset service base test case.
 */
abstract class CaptionAssetServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setSetContentActionTestData();
		$this->setUpdateActionTestData();
		$this->setServeByEntryIdActionTestData();
		$this->setServeActionTestData();
		$this->setSetAsDefaultActionTestData();
		$this->setGetActionTestData();
		$this->setListActionTestData();
		$this->setDeleteActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testSetContentAction initial data (If needed)
	 */
	protected function setSetContentActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testServeByEntryIdAction initial data (If needed)
	 */
	protected function setServeByEntryIdActionTestData(){}

	/**
	 * Set up the testServeAction initial data (If needed)
	 */
	protected function setServeActionTestData(){}

	/**
	 * Set up the testSetAsDefaultAction initial data (If needed)
	 */
	protected function setSetAsDefaultActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Tests captionAsset->add action
	 * @param string $entryId 
	 * @param KalturaCaptionAsset $captionAsset 
	 * @param KalturaCaptionAsset $reference 
	 * @return KalturaCaptionAsset
	 * @dataProvider provideData
	 */
	public function testAdd($entryId, KalturaCaptionAsset $captionAsset, KalturaCaptionAsset $reference)
	{
		$resultObject = $this->client->captionAsset->add($entryId, $captionAsset);
		$this->assertInstanceOf('KalturaCaptionAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($entryId, $captionAsset, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd($entryId, KalturaCaptionAsset $captionAsset, KalturaCaptionAsset $reference)
	{
	}

	/**
	 * Tests captionAsset->update action
	 * @param string $id 
	 * @param KalturaCaptionAsset $captionAsset 
	 * @param KalturaContentResource $contentResource 
	 * @param KalturaCaptionAsset $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaCaptionAsset $captionAsset, KalturaContentResource $contentResource = null, KalturaCaptionAsset $reference)
	{
		$resultObject = $this->client->captionAsset->update($id, $captionAsset, $contentResource);
		$this->assertInstanceOf('KalturaCaptionAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->validateUpdate($id, $captionAsset, $contentResource, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCaptionAsset $captionAsset, KalturaContentResource $contentResource = null, KalturaCaptionAsset $reference)
	{
	}

	/**
	 * Tests captionAsset->get action
	 * @param string $captionAssetId 
	 * @param KalturaCaptionAsset $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($captionAssetId, KalturaCaptionAsset $reference)
	{
		$resultObject = $this->client->captionAsset->get($captionAssetId);
		$this->assertInstanceOf('KalturaCaptionAsset', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->validateGet($captionAssetId, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($captionAssetId, KalturaCaptionAsset $reference)
	{
	}

	/**
	 * Tests captionAsset->listAction action
	 * @param KalturaAssetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaCaptionAssetListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaCaptionAssetListResponse $reference)
	{
		$resultObject = $this->client->captionAsset->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaCaptionAssetListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaAssetFilter $filter = null, KalturaFilterPager $pager = null, KalturaCaptionAssetListResponse $reference)
	{
	}

	/**
	 * Tests captionAsset->delete action
	 * @param string $captionAssetId 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($captionAssetId)
	{
		$resultObject = $this->client->captionAsset->delete($captionAssetId);
		$this->validateDelete($captionAssetId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($captionAssetId)
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
		return new KalturaTestSuite('CaptionAssetServiceTest');
	}

}
