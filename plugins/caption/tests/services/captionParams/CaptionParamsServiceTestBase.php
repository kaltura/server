<?php

/**
 * captionParams service base test case.
 */
abstract class CaptionParamsServiceTestBase extends KalturaApiTestCase
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
	 * Tests captionParams->add action
	 * @param KalturaCaptionParams $captionParams 
	 * @param KalturaCaptionParams $reference 
	 * @return KalturaCaptionParams
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaCaptionParams $captionParams, KalturaCaptionParams $reference)
	{
		$resultObject = $this->client->captionParams->add($captionParams);
		$this->assertInstanceOf('KalturaCaptionParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($captionParams, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaCaptionParams $captionParams, KalturaCaptionParams $reference)
	{
	}

	/**
	 * Tests captionParams->get action
	 * @param int $id 
	 * @param KalturaCaptionParams $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaCaptionParams $reference)
	{
		$resultObject = $this->client->captionParams->get($id);
		$this->assertInstanceOf('KalturaCaptionParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaCaptionParams $reference)
	{
	}

	/**
	 * Tests captionParams->update action
	 * @param int $id 
	 * @param KalturaCaptionParams $captionParams 
	 * @param KalturaCaptionParams $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaCaptionParams $captionParams, KalturaCaptionParams $reference)
	{
		$resultObject = $this->client->captionParams->update($id, $captionParams);
		$this->assertInstanceOf('KalturaCaptionParams', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->validateUpdate($id, $captionParams, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCaptionParams $captionParams, KalturaCaptionParams $reference)
	{
	}

	/**
	 * Tests captionParams->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->captionParams->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests captionParams->listAction action
	 * @param KalturaCaptionParamsFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaCaptionParamsListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaCaptionParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaCaptionParamsListResponse $reference)
	{
		$resultObject = $this->client->captionParams->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaCaptionParamsListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaCaptionParamsFilter $filter = null, KalturaFilterPager $pager = null, KalturaCaptionParamsListResponse $reference)
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
		return new KalturaTestSuite('CaptionParamsServiceTest');
	}

}
