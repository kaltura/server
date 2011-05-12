<?php

/**
 * entryAdmin service base test case.
 */
abstract class EntryAdminServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests entryAdmin->get action
	 * @param string $entryId Entry id
	 * @param int $version Desired version of the data
	 * @param KalturaBaseEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaBaseEntry $reference)
	{
		$resultObject = $this->client->entryAdmin->get($entryId, $version);
		$this->assertType('KalturaBaseEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($entryId, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaBaseEntry $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
