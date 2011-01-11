<?php

/**
 * systemPartner service base test case.
 */
abstract class SystemPartnerServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests systemPartner->get action
	 * @param int $partnerId X
	 * @param KalturaPartner $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($partnerId, KalturaPartner $reference)
	{
		$resultObject = $this->client->systemPartner->get($partnerId);
		$this->assertType('KalturaPartner', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($partnerId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($partnerId, KalturaPartner $reference)
	{
	}

	/**
	 * Tests systemPartner->list action
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaPartnerListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaPartnerListResponse $reference)
	{
		$resultObject = $this->client->systemPartner->list($filter, $pager);
		$this->assertType('KalturaPartnerListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaPartnerListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
