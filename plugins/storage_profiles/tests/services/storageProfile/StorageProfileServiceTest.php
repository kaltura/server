<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/StorageProfileServiceBaseTest.php');

/**
 * storageProfile service test case.
 */
class StorageProfileServiceTest extends StorageProfileServiceBaseTest
{
	/**
	 * Tests storageProfile->listByPartner action
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @param KalturaStorageProfileListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListByPartner(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaStorageProfileListResponse $reference)
	{
		$resultObject = $this->client->storageProfile->listByPartner($filter, $pager, $reference);
		$this->assertType('KalturaStorageProfileListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests storageProfile->updateStatus action
	 * @param int $storageId
	 * @param KalturaStorageProfileStatus $status
	 * @dataProvider provideData
	 */
	public function testUpdateStatus($storageId, $status)
	{
		$resultObject = $this->client->storageProfile->updateStatus($storageId, $status);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($storageProfileId, KalturaStorageProfile $reference)
	{
		parent::validateGet($storageProfileId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($storageProfileId, KalturaStorageProfile $storageProfile, KalturaStorageProfile $reference)
	{
		parent::validateUpdate($storageProfileId, $storageProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaStorageProfile $storageProfile, KalturaStorageProfile $reference)
	{
		parent::validateAdd($storageProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testAdd - TODO: replace testAdd with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
