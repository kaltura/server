<?php

/**
 * virusScanProfile service base test case.
 */
abstract class VirusScanProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests virusScanProfile->list action
	 * @param KalturaVirusScanProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaVirusScanProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->virusScanProfile->listAction($filter, $pager);
		$this->assertType('KalturaVirusScanProfileListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

	/**
	 * Tests virusScanProfile->add action
	 * @param KalturaVirusScanProfile $virusScanProfile
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaVirusScanProfile $virusScanProfile)
	{
		$resultObject = $this->client->virusScanProfile->add($virusScanProfile);
		$this->assertType('KalturaVirusScanProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests virusScanProfile->get action
	 * @param int $virusScanProfileId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($virusScanProfileId)
	{
		$resultObject = $this->client->virusScanProfile->get($virusScanProfileId);
		$this->assertType('KalturaVirusScanProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests virusScanProfile->update action
	 * @param int $virusScanProfileId
	 * @param KalturaVirusScanProfile $virusScanProfile
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($virusScanProfileId, KalturaVirusScanProfile $virusScanProfile)
	{
		$resultObject = $this->client->virusScanProfile->update($virusScanProfileId, $virusScanProfile);
		$this->assertType('KalturaVirusScanProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests virusScanProfile->delete action
	 * @param int $virusScanProfileId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($virusScanProfileId)
	{
		$resultObject = $this->client->virusScanProfile->delete($virusScanProfileId);
	}

}
