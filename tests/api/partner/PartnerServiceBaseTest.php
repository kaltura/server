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
	 * @param KalturaPartner $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaPartner $partner, $allowEmpty = null, KalturaPartner $reference)
	{
		$resultObject = $this->client->partner->update($partner, $allowEmpty);
		$this->assertType('KalturaPartner', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($partner, $allowEmpty, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaPartner $partner, $allowEmpty = null, KalturaPartner $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
