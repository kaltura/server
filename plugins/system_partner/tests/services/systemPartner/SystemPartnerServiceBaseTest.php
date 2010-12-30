<?php

/**
 * systemPartner service base test case.
 */
abstract class SystemPartnerServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests systemPartner->get action
	 * @param int $partnerId
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($partnerId)
	{
		$resultObject = $this->client->systemPartner->get($partnerId);
		$this->assertType('KalturaPartner', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests systemPartner->list action
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->systemPartner->listAction($filter, $pager);
		$this->assertType('KalturaPartnerListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
