<?php

/**
 * widget service base test case.
 */
abstract class WidgetServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setAddActionTestData();
		$this->setUpdateActionTestData();
		$this->setGetActionTestData();
		$this->setCloneActionTestData();
		$this->setListActionTestData();

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
	 * Set up the testCloneAction initial data (If needed)
	 */
	protected function setCloneActionTestData(){}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Tests widget->add action
	 * @param KalturaWidget $widget 
	 * @param KalturaWidget $reference 
	 * @return KalturaWidget
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaWidget $widget, KalturaWidget $reference)
	{
		$resultObject = $this->client->widget->add($widget);
		$this->assertInstanceOf('KalturaWidget', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($widget, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaWidget $widget, KalturaWidget $reference)
	{
	}

	/**
	 * Tests widget->update action
	 * @param string $id 
	 * @param KalturaWidget $widget 
	 * @param KalturaWidget $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaWidget $widget, KalturaWidget $reference)
	{
		$resultObject = $this->client->widget->update($id, $widget);
		$this->assertInstanceOf('KalturaWidget', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($id, $widget, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaWidget $widget, KalturaWidget $reference)
	{
	}

	/**
	 * Tests widget->get action
	 * @param string $id 
	 * @param KalturaWidget $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaWidget $reference)
	{
		$resultObject = $this->client->widget->get($id);
		$this->assertInstanceOf('KalturaWidget', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaWidget $reference)
	{
	}

	/**
	 * Tests widget->listAction action
	 * @param KalturaWidgetFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaWidgetListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null, KalturaWidgetListResponse $reference)
	{
		$resultObject = $this->client->widget->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaWidgetListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaWidgetFilter $filter = null, KalturaFilterPager $pager = null, KalturaWidgetListResponse $reference)
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
		return new KalturaTestSuite('WidgetServiceTest');
	}

}
