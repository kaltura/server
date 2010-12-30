<?php

/**
 * partner service base test case.
 */
abstract class PartnerServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests partner->update action
	 * @param KalturaPartner $partner
	 * @param bool $allowEmpty
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaPartner $partner, $allowEmpty = null)
	{
		$resultObject = $this->client->partner->update($partner, $allowEmpty);
		$this->assertType('KalturaPartner', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

}
