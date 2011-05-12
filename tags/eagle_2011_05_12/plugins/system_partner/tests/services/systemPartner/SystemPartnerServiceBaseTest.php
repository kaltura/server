<?php

/**
 * systemPartner service base test case.
 */
abstract class SystemPartnerServiceBaseTest extends KalturaApiTestCase
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
	 * Tests systemPartner->listAction action
	 * @param KalturaPartnerFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaPartnerListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaPartnerListResponse $reference)
	{
		$resultObject = $this->client->systemPartner->listAction($filter, $pager);
		$this->assertType('KalturaPartnerListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null, KalturaPartnerListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
