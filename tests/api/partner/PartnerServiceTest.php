<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/PartnerServiceBaseTest.php');

/**
 * partner service test case.
 */
class PartnerServiceTest extends PartnerServiceBaseTest
{
	/**
	 * Tests partner->register action
	 * @param KalturaPartner $partner
	 * @param string $cmsPassword
	 * @dataProvider provideData
	 */
	public function testRegister(KalturaPartner $partner, $cmsPassword = null)
	{
		$resultObject = $this->client->partner->register($partner, $cmsPassword);
		$this->assertType('KalturaPartner', $resultObject);
	}

	/**
	 * Tests partner->getSecrets action
	 * @param int $partnerId
	 * @param string $adminEmail
	 * @param string $cmsPassword
	 * @dataProvider provideData
	 */
	public function testGetSecrets($partnerId, $adminEmail, $cmsPassword)
	{
		$resultObject = $this->client->partner->getSecrets($partnerId, $adminEmail, $cmsPassword);
		$this->assertType('KalturaPartner', $resultObject);
	}

	/**
	 * Tests partner->getInfo action
	 * @dataProvider provideData
	 */
	public function testGetInfo()
	{
		$resultObject = $this->client->partner->getInfo();
		$this->assertType('KalturaPartner', $resultObject);
	}

	/**
	 * Tests partner->getUsage action
	 * @param int $year
	 * @param int $month
	 * @param string $resolution
	 * @dataProvider provideData
	 */
	public function testGetUsage($year = null, $month = 1, $resolution = 'days')
	{
		$resultObject = $this->client->partner->getUsage($year, $month, $resolution);
		$this->assertType('KalturaPartnerUsage', $resultObject);
	}

}
