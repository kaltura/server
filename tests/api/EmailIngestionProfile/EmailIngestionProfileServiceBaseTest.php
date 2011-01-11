<?php

/**
 * EmailIngestionProfile service base test case.
 */
abstract class EmailIngestionProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests EmailIngestionProfile->add action
	 * @param KalturaEmailIngestionProfile $EmailIP Mandatory input parameter of type KalturaEmailIngestionProfile
	 * @param KalturaEmailIngestionProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEmailIngestionProfile $EmailIP, KalturaEmailIngestionProfile $reference)
	{
		$resultObject = $this->client->EmailIngestionProfile->add($EmailIP);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($EmailIP, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaEmailIngestionProfile $EmailIP, KalturaEmailIngestionProfile $reference)
	{
	}

	/**
	 * Tests EmailIngestionProfile->get action
	 * @param KalturaEmailIngestionProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaEmailIngestionProfile $reference, $id)
	{
		$resultObject = $this->client->EmailIngestionProfile->get($id);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaEmailIngestionProfile $reference, $id)
	{
	}

	/**
	 * Tests EmailIngestionProfile->update action
	 * @param KalturaEmailIngestionProfile $EmailIP 
	 * @param KalturaEmailIngestionProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaEmailIngestionProfile $EmailIP, KalturaEmailIngestionProfile $reference, $id)
	{
		$resultObject = $this->client->EmailIngestionProfile->update($id, $EmailIP);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($EmailIP, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaEmailIngestionProfile $EmailIP, KalturaEmailIngestionProfile $reference, $id)
	{
	}

	/**
	 * Tests EmailIngestionProfile->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->EmailIngestionProfile->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
