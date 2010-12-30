<?php

/**
 * storageProfile service base test case.
 */
abstract class StorageProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests storageProfile->get action
	 * @param int $storageProfileId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($storageProfileId)
	{
		$resultObject = $this->client->storageProfile->get($storageProfileId);
		$this->assertType('KalturaStorageProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests storageProfile->update action
	 * @param int $storageProfileId
	 * @param KalturaStorageProfile $storageProfile
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($storageProfileId, KalturaStorageProfile $storageProfile)
	{
		$resultObject = $this->client->storageProfile->update($storageProfileId, $storageProfile);
		$this->assertType('KalturaStorageProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests storageProfile->add action
	 * @param KalturaStorageProfile $storageProfile
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaStorageProfile $storageProfile)
	{
		$resultObject = $this->client->storageProfile->add($storageProfile);
		$this->assertType('KalturaStorageProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

}
