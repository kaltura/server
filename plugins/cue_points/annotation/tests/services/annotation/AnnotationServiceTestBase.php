<?php

/**
 * annotation service base test case.
 */
abstract class AnnotationServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setListActionTestData();
		$this->setCountActionTestData();
		$this->setAddActionTestData();
		$this->setAddFromBulkActionTestData();
		$this->setGetActionTestData();
		$this->setDeleteActionTestData();
		$this->setUpdateActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

	/**
	 * Set up the testCountAction initial data (If needed)
	 */
	protected function setCountActionTestData(){}

	/**
	 * Set up the testAddAction initial data (If needed)
	 */
	protected function setAddActionTestData(){}

	/**
	 * Set up the testAddFromBulkAction initial data (If needed)
	 */
	protected function setAddFromBulkActionTestData(){}

	/**
	 * Set up the testGetAction initial data (If needed)
	 */
	protected function setGetActionTestData(){}

	/**
	 * Set up the testDeleteAction initial data (If needed)
	 */
	protected function setDeleteActionTestData(){}

	/**
	 * Set up the testUpdateAction initial data (If needed)
	 */
	protected function setUpdateActionTestData(){}

	/**
	 * Tests annotation->listAction action
	 * @param KalturaCuePointFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaCuePointListResponse $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null, KalturaCuePointListResponse $reference)
	{
		$resultObject = $this->client->annotation->listAction($filter, $pager);
		$this->assertInternalType('KalturaCuePointListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null, KalturaCuePointListResponse $reference)
	{
	}

	/**
	 * Tests annotation->add action
	 * @param KalturaCuePoint $cuePoint 
	 * @param KalturaCuePoint $reference 
	 * @return KalturaCuePoint
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaCuePoint $cuePoint, KalturaCuePoint $reference)
	{
		$resultObject = $this->client->annotation->add($cuePoint);
		$this->assertInternalType('KalturaCuePoint', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($cuePoint, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaCuePoint $cuePoint, KalturaCuePoint $reference)
	{
	}

	/**
	 * Tests annotation->get action
	 * @param string $id 
	 * @param KalturaCuePoint $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaCuePoint $reference)
	{
		$resultObject = $this->client->annotation->get($id);
		$this->assertInternalType('KalturaCuePoint', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId'));
		$this->validateGet($id, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaCuePoint $reference)
	{
	}

	/**
	 * Tests annotation->delete action
	 * @param string $id 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->annotation->delete($id);
		$this->validateDelete($id);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests annotation->update action
	 * @param string $id 
	 * @param KalturaCuePoint $cuePoint 
	 * @param KalturaCuePoint $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaCuePoint $cuePoint, KalturaCuePoint $reference)
	{
		$resultObject = $this->client->annotation->update($id, $cuePoint);
		$this->assertInternalType('KalturaCuePoint', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId'));
		$this->validateUpdate($id, $cuePoint, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaCuePoint $cuePoint, KalturaCuePoint $reference)
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
		return new KalturaTestSuite('AnnotationServiceTest');
	}

}
