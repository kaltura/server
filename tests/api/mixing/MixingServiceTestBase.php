<?php

/**
 * mixing service base test case.
 */
abstract class MixingServiceTestBase extends KalturaApiTestCase
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
		$this->setCountActionTestData();
		$this->setCloneActionTestData();
		$this->setAppendMediaEntryActionTestData();
		$this->setRequestFlatteningActionTestData();
		$this->setGetMixesByMediaIdActionTestData();
		$this->setGetReadyMediaEntriesActionTestData();
		$this->setAnonymousRankActionTestData();

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
	 * Set up the testCountAction initial data (If needed)
	 */
	protected function setCountActionTestData(){}

	/**
	 * Set up the testCloneAction initial data (If needed)
	 */
	protected function setCloneActionTestData(){}

	/**
	 * Set up the testAppendMediaEntryAction initial data (If needed)
	 */
	protected function setAppendMediaEntryActionTestData(){}

	/**
	 * Set up the testRequestFlatteningAction initial data (If needed)
	 */
	protected function setRequestFlatteningActionTestData(){}

	/**
	 * Set up the testGetMixesByMediaIdAction initial data (If needed)
	 */
	protected function setGetMixesByMediaIdActionTestData(){}

	/**
	 * Set up the testGetReadyMediaEntriesAction initial data (If needed)
	 */
	protected function setGetReadyMediaEntriesActionTestData(){}

	/**
	 * Set up the testAnonymousRankAction initial data (If needed)
	 */
	protected function setAnonymousRankActionTestData(){}

	/**
	 * Tests mixing->add action
	 * @param KalturaMixEntry $mixEntry Mix entry metadata
	 * @param KalturaMixEntry $reference 
	 * @return KalturaMixEntry
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->add($mixEntry);
		$this->assertInstanceOf('KalturaMixEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($mixEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
	}

	/**
	 * Tests mixing->get action
	 * @param string $entryId Mix entry id
	 * @param int $version Desired version of the data
	 * @param KalturaMixEntry $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->get($entryId, $version);
		$this->assertInstanceOf('KalturaMixEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateGet($entryId, $version, $reference);
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaMixEntry $reference)
	{
	}

	/**
	 * Tests mixing->update action
	 * @param string $entryId Mix entry id to update
	 * @param KalturaMixEntry $mixEntry Mix entry metadata to update
	 * @param KalturaMixEntry $reference 
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->update($entryId, $mixEntry);
		$this->assertInstanceOf('KalturaMixEntry', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateUpdate($entryId, $mixEntry, $reference);
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
	}

	/**
	 * Tests mixing->delete action
	 * @param string $entryId Mix entry id to delete
	 * @depends testAdd
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->mixing->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests mixing->listAction action
	 * @param KalturaMixEntryFilter $filter Mix entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaMixListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMixListResponse $reference)
	{
		$resultObject = $this->client->mixing->listAction($filter, $pager);
		$this->assertInstanceOf('KalturaMixListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath'));
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMixListResponse $reference)
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
		return new KalturaTestSuite('MixingServiceTest');
	}

}
