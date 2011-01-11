<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/SystemPartnerServiceBaseTest.php');

/**
 * systemPartner service test case.
 */
class SystemPartnerServiceTest extends SystemPartnerServiceBaseTest
{
	/**
	 * Validates testGet results
	 */
	protected function validateGet($partnerId, KalturaPartner $reference)
	{
		parent::validateGet($partnerId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests systemPartner->getUsage action
	 * @param KalturaPartnerFilter $partnerFilter
	 * @param KalturaSystemPartnerUsageFilter $usageFilter
	 * @param KalturaFilterPager $pager
	 * @param KalturaSystemPartnerUsageListResponse $reference
	 * @dataProvider provideData
	 */
	public function testGetUsage(KalturaPartnerFilter $partnerFilter = null, KalturaSystemPartnerUsageFilter $usageFilter = null, KalturaFilterPager $pager = null, KalturaSystemPartnerUsageListResponse $reference)
	{
		$resultObject = $this->client->systemPartner->getUsage($partnerFilter, $usageFilter, $pager, $reference);
		$this->assertType('KalturaSystemPartnerUsageListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaPartnerListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests systemPartner->updateStatus action
	 * @param int $partnerId
	 * @param KalturaPartnerStatus $status
	 * @dataProvider provideData
	 */
	public function testUpdateStatus($partnerId, $status)
	{
		$resultObject = $this->client->systemPartner->updateStatus($partnerId, $status);
		// TODO - add here your own validations
	}

	/**
	 * Tests systemPartner->getAdminSession action
	 * @param int $partnerId
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testGetAdminSession($partnerId, $reference)
	{
		$resultObject = $this->client->systemPartner->getAdminSession($partnerId, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests systemPartner->updateConfiguration action
	 * @param int $partnerId
	 * @param KalturaSystemPartnerConfiguration $configuration
	 * @dataProvider provideData
	 */
	public function testUpdateConfiguration($partnerId, KalturaSystemPartnerConfiguration $configuration)
	{
		$resultObject = $this->client->systemPartner->updateConfiguration($partnerId, $configuration);
		// TODO - add here your own validations
	}

	/**
	 * Tests systemPartner->getConfiguration action
	 * @param int $partnerId
	 * @param KalturaSystemPartnerConfiguration $reference
	 * @dataProvider provideData
	 */
	public function testGetConfiguration($partnerId, KalturaSystemPartnerConfiguration $reference)
	{
		$resultObject = $this->client->systemPartner->getConfiguration($partnerId, $reference);
		$this->assertType('KalturaSystemPartnerConfiguration', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests systemPartner->getPackages action
	 * @param KalturaSystemPartnerPackageArray $reference
	 * @dataProvider provideData
	 */
	public function testGetPackages(KalturaSystemPartnerPackageArray $reference)
	{
		$resultObject = $this->client->systemPartner->getPackages($reference);
		$this->assertType('KalturaSystemPartnerPackageArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
