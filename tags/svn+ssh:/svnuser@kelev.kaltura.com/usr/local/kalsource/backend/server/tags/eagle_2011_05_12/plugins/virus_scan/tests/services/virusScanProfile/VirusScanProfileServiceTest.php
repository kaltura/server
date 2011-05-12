<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/VirusScanProfileServiceBaseTest.php');

/**
 * virusScanProfile service test case.
 */
class VirusScanProfileServiceTest extends VirusScanProfileServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaVirusScanProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaVirusScanProfileListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaVirusScanProfile $virusScanProfile, KalturaVirusScanProfile $reference)
	{
		parent::validateAdd($virusScanProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($virusScanProfileId, KalturaVirusScanProfile $reference)
	{
		parent::validateGet($virusScanProfileId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($virusScanProfileId, KalturaVirusScanProfile $virusScanProfile, KalturaVirusScanProfile $reference)
	{
		parent::validateUpdate($virusScanProfileId, $virusScanProfile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($virusScanProfileId)
	{
		parent::validateDelete($virusScanProfileId);
		// TODO - add your own validations here
	}

	/**
	 * Tests virusScanProfile->scan action
	 * @param string $flavorAssetId
	 * @param int $virusScanProfileId
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testScan($flavorAssetId, $virusScanProfileId = null, $reference)
	{
		$resultObject = $this->client->virusScanProfile->scan($flavorAssetId, $virusScanProfileId, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
