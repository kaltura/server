<?php

/**
 * uiConf service base test case.
 */
abstract class UiConfServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setUpdateActionTestData();
		$this->setGetActionTestData();
		$this->setDeleteActionTestData();
		$this->setCloneActionTestData();
		$this->setListTemplatesActionTestData();
		$this->setListActionTestData();
		$this->setGetAvailableTypesActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testCloneAction initial data (If needed)
	 */
	protected function setCloneActionTestData(){}

	/**
	 * Set up the testListTemplatesAction initial data (If needed)
	 */
	protected function setListTemplatesActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testGetAvailableTypesAction initial data (If needed)
	 */
	protected function setGetAvailableTypesActionTestData(){}

	/**
	 * Tests uiConf->add action
	 * @param KalturaUiConf $uiConf Mandatory input parameter of type KalturaUiConf
	 * @param KalturaUiConf $reference 
	 * @return KalturaUiConf
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
		$resultObject = $this->client->uiConf->add($uiConf);
		$this->assertInstanceOf('KalturaUiConf', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($uiConf, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
	}

	/**
	 * Tests uiConf->update action
	 * @param int $id 
	 * @param KalturaUiConf $uiConf 
	 * @param KalturaUiConf $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
		$resultObject = $this->client->uiConf->update($id, $uiConf);
		$this->assertInstanceOf('KalturaUiConf', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $uiConf, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaUiConf $uiConf, KalturaUiConf $reference)
	{
	}

	/**
	 * Tests uiConf->get action
	 * @param int $id 
	 * @param KalturaUiConf $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaUiConf $reference)
	{
		$resultObject = $this->client->uiConf->get($id);
		$this->assertInstanceOf('KalturaUiConf', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaUiConf $reference)
	{
	}

	/**
	 * Tests uiConf->delete action
	 * @param int $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->uiConf->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests uiConf->listAction action
	 * @param KalturaUiConfFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaUiConfListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfListResponse $reference)
	{
		$resultObject = $this->client->uiConf->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaUiConfListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaUiConfFilter $filter = null, KalturaFilterPager $pager = null, KalturaUiConfListResponse $reference)
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
		return new KalturaTestSuite('UiConfServiceTest');
	}

}
