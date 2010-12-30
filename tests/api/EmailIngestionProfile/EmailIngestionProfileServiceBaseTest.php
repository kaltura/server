<?php

/**
 * EmailIngestionProfile service base test case.
 */
abstract class EmailIngestionProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests EmailIngestionProfile->add action
	 * @param KalturaEmailIngestionProfile $EmailIP
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaEmailIngestionProfile $EmailIP)
	{
		$resultObject = $this->client->EmailIngestionProfile->add($EmailIP);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests EmailIngestionProfile->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->EmailIngestionProfile->get($id);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests EmailIngestionProfile->update action
	 * @param KalturaEmailIngestionProfile $EmailIP
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaEmailIngestionProfile $EmailIP, $id)
	{
		$resultObject = $this->client->EmailIngestionProfile->update($id, $EmailIP);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests EmailIngestionProfile->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->EmailIngestionProfile->delete($id);
	}

}
