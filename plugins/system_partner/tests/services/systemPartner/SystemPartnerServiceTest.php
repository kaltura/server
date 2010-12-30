<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/SystemPartnerServiceBaseTest.php');

/**
 * systemPartner service test case.
 */
class SystemPartnerServiceTest extends SystemPartnerServiceBaseTest
{
	/**
	 * Tests systemPartner->getUsage action
	 * @param KalturaPartnerFilter $partnerFilter
	 * @param KalturaSystemPartnerUsageFilter $usageFilter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testGetUsage(KalturaPartnerFilter $partnerFilter = null, KalturaSystemPartnerUsageFilter $usageFilter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->systemPartner->getUsage($partnerFilter, $usageFilter, $pager);
		$this->assertType('KalturaSystemPartnerUsageListResponse', $resultObject);
	}

	/**
	 * Tests systemPartner->updateStatus action
	 * @param int $partnerId
	 * @param KalturaPartnerStatus $status
	 * @dataProvider provideData
	 */
	public function testUpdateStatus($partnerId, KalturaPartnerStatus $status)
	{
		$resultObject = $this->client->systemPartner->updateStatus($partnerId, $status);
	}

	/**
	 * Tests systemPartner->getAdminSession action
	 * @param int $partnerId
	 * @dataProvider provideData
	 */
	public function testGetAdminSession($partnerId)
	{
		$resultObject = $this->client->systemPartner->getAdminSession($partnerId);
		$this->assertType('string', $resultObject);
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
	}

	/**
	 * Tests systemPartner->getConfiguration action
	 * @param int $partnerId
	 * @dataProvider provideData
	 */
	public function testGetConfiguration($partnerId)
	{
		$resultObject = $this->client->systemPartner->getConfiguration($partnerId);
		$this->assertType('KalturaSystemPartnerConfiguration', $resultObject);
	}

	/**
	 * Tests systemPartner->getPackages action
	 * @dataProvider provideData
	 */
	public function testGetPackages()
	{
		$resultObject = $this->client->systemPartner->getPackages();
		$this->assertType('KalturaSystemPartnerPackageArray', $resultObject);
	}

}
