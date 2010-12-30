<?php

/**
 * entryAdmin service base test case.
 */
abstract class EntryAdminServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests entryAdmin->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->entryAdmin->get($entryId, $version);
		$this->assertType('KalturaBaseEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

}
