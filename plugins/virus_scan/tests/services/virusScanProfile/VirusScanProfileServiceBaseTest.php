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
	 * @param KalturaVirusScanProfileListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaVirusScanProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaVirusScanProfileListResponse $reference)
	{
		$resultObject = $this->client->virusScanProfile->list($filter, $pager);
		$this->assertType('KalturaVirusScanProfileListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaVirusScanProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaVirusScanProfileListResponse $reference)
	{
	}

	/**
	 * Tests virusScanProfile->add action
	 * @param KalturaVirusScanProfile $virusScanProfile 
	 * @param KalturaVirusScanProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaVirusScanProfile $virusScanProfile, KalturaVirusScanProfile $reference)
	{
		$resultObject = $this->client->virusScanProfile->add($virusScanProfile);
		$this->assertType('KalturaVirusScanProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($virusScanProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaVirusScanProfile $virusScanProfile, KalturaVirusScanProfile $reference)
	{
	}

	/**
	 * Tests virusScanProfile->get action
	 * @param int $virusScanProfileId 
	 * @param KalturaVirusScanProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($virusScanProfileId, KalturaVirusScanProfile $reference)
	{
		$resultObject = $this->client->virusScanProfile->get($virusScanProfileId);
		$this->assertType('KalturaVirusScanProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($virusScanProfileId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($virusScanProfileId, KalturaVirusScanProfile $reference)
	{
	}

	/**
	 * Tests virusScanProfile->update action
	 * @param int $virusScanProfileId 
	 * @param KalturaVirusScanProfile $virusScanProfile Id
	 * @param KalturaVirusScanProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($virusScanProfileId, KalturaVirusScanProfile $virusScanProfile, KalturaVirusScanProfile $reference)
	{
		$resultObject = $this->client->virusScanProfile->update($virusScanProfileId, $virusScanProfile);
		$this->assertType('KalturaVirusScanProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($virusScanProfileId, $virusScanProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($virusScanProfileId, KalturaVirusScanProfile $virusScanProfile, KalturaVirusScanProfile $reference)
	{
	}

	/**
	 * Tests virusScanProfile->delete action
	 * @param int $virusScanProfileId 
	 * @dataProvider provideData
	 */
	public function testDelete($virusScanProfileId)
	{
		$resultObject = $this->client->virusScanProfile->delete($virusScanProfileId);
		$this->validateDelete($virusScanProfileId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($virusScanProfileId)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
