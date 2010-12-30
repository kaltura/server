<?php

/**
 * auditTrail service base test case.
 */
abstract class AuditTrailServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests auditTrail->list action
	 * @param KalturaAuditTrailFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->auditTrail->listAction($filter, $pager);
		$this->assertType('KalturaAuditTrailListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

	/**
	 * Tests auditTrail->add action
	 * @param KalturaAuditTrail $auditTrail
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAuditTrail $auditTrail)
	{
		$resultObject = $this->client->auditTrail->add($auditTrail);
		$this->assertType('KalturaAuditTrail', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests auditTrail->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->auditTrail->get($id);
		$this->assertType('KalturaAuditTrail', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

}
