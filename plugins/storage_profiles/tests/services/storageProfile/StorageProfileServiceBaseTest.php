<?php

/**
 * storageProfile service base test case.
 */
abstract class StorageProfileServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests storageProfile->get action
	 * @param int $storageProfileId 
	 * @param KalturaStorageProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($storageProfileId, KalturaStorageProfile $reference)
	{
		$resultObject = $this->client->storageProfile->get($storageProfileId);
		$this->assertType('KalturaStorageProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($storageProfileId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($storageProfileId, KalturaStorageProfile $reference)
	{
	}

	/**
	 * Tests storageProfile->update action
	 * @param int $storageProfileId 
	 * @param KalturaStorageProfile $storageProfile Id
	 * @param KalturaStorageProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($storageProfileId, KalturaStorageProfile $storageProfile, KalturaStorageProfile $reference)
	{
		$resultObject = $this->client->storageProfile->update($storageProfileId, $storageProfile);
		$this->assertType('KalturaStorageProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($storageProfileId, $storageProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($storageProfileId, KalturaStorageProfile $storageProfile, KalturaStorageProfile $reference)
	{
	}

	/**
	 * Tests storageProfile->add action
	 * @param KalturaStorageProfile $storageProfile 
	 * @param KalturaStorageProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaStorageProfile $storageProfile, KalturaStorageProfile $reference)
	{
		$resultObject = $this->client->storageProfile->add($storageProfile);
		$this->assertType('KalturaStorageProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($storageProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaStorageProfile $storageProfile, KalturaStorageProfile $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
