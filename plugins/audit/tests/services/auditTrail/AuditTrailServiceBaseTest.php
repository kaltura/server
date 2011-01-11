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
	 * @param KalturaAuditTrailListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null, KalturaAuditTrailListResponse $reference)
	{
		$resultObject = $this->client->auditTrail->list($filter, $pager);
		$this->assertType('KalturaAuditTrailListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null, KalturaAuditTrailListResponse $reference)
	{
	}

	/**
	 * Tests auditTrail->add action
	 * @param KalturaAuditTrail $auditTrail 
	 * @param KalturaAuditTrail $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAuditTrail $auditTrail, KalturaAuditTrail $reference)
	{
		$resultObject = $this->client->auditTrail->add($auditTrail);
		$this->assertType('KalturaAuditTrail', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($auditTrail, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaAuditTrail $auditTrail, KalturaAuditTrail $reference)
	{
	}

	/**
	 * Tests auditTrail->get action
	 * @param KalturaAuditTrail $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaAuditTrail $reference, $id)
	{
		$resultObject = $this->client->auditTrail->get($id);
		$this->assertType('KalturaAuditTrail', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaAuditTrail $reference, $id)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
