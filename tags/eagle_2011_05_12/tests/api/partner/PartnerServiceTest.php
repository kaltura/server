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
	 * @param KalturaPartner $reference
	 * @dataProvider provideData
	 */
	public function testRegister(KalturaPartner $partner, $cmsPassword = null, KalturaPartner $reference)
	{
		$resultObject = $this->client->partner->register($partner, $cmsPassword, $reference);
		$this->assertType('KalturaPartner', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaPartner $partner, $allowEmpty = null, KalturaPartner $reference)
	{
		parent::validateUpdate($partner, $allowEmpty, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests partner->getSecrets action
	 * @param int $partnerId
	 * @param string $adminEmail
	 * @param string $cmsPassword
	 * @param KalturaPartner $reference
	 * @dataProvider provideData
	 */
	public function testGetSecrets($partnerId, $adminEmail, $cmsPassword, KalturaPartner $reference)
	{
		$resultObject = $this->client->partner->getSecrets($partnerId, $adminEmail, $cmsPassword, $reference);
		$this->assertType('KalturaPartner', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests partner->getInfo action
	 * @param KalturaPartner $reference
	 * @dataProvider provideData
	 */
	public function testGetInfo(KalturaPartner $reference)
	{
		$resultObject = $this->client->partner->getInfo($reference);
		$this->assertType('KalturaPartner', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests partner->getUsage action
	 * @param int $year
	 * @param int $month
	 * @param string $resolution
	 * @param KalturaPartnerUsage $reference
	 * @dataProvider provideData
	 */
	public function testGetUsage($year = null, $month = 1, $resolution = 'days', KalturaPartnerUsage $reference)
	{
		$resultObject = $this->client->partner->getUsage($year, $month, $resolution, $reference);
		$this->assertType('KalturaPartnerUsage', $resultObject);
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
