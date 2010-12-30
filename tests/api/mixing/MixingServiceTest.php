<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/MixingServiceBaseTest.php');

/**
 * mixing service test case.
 */
class MixingServiceTest extends MixingServiceBaseTest
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests mixing->count action
	 * @param KalturaMediaEntryFilter $filter
	 * @dataProvider provideData
	 */
	public function testCount(KalturaMediaEntryFilter $filter = null)
	{
		$resultObject = $this->client->mixing->count($filter);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests mixing->clone action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testClone($entryId)
	{
		$resultObject = $this->client->mixing->clone($entryId);
		$this->assertType('KalturaMixEntry', $resultObject);
	}

	/**
	 * Tests mixing->appendMediaEntry action
	 * @param string $mixEntryId
	 * @param string $mediaEntryId
	 * @dataProvider provideData
	 */
	public function testAppendMediaEntry($mixEntryId, $mediaEntryId)
	{
		$resultObject = $this->client->mixing->appendMediaEntry($mixEntryId, $mediaEntryId);
		$this->assertType('KalturaMixEntry', $resultObject);
	}

	/**
	 * Tests mixing->requestFlattening action
	 * @param string $entryId
	 * @param string $fileFormat
	 * @param int $version
	 * @dataProvider provideData
	 */
	public function testRequestFlattening($entryId, $fileFormat, $version = -1)
	{
		$resultObject = $this->client->mixing->requestFlattening($entryId, $fileFormat, $version);
		$this->assertType('int', $resultObject);
	}

	/**
	 * Tests mixing->getMixesByMediaId action
	 * @param string $mediaEntryId
	 * @dataProvider provideData
	 */
	public function testGetMixesByMediaId($mediaEntryId)
	{
		$resultObject = $this->client->mixing->getMixesByMediaId($mediaEntryId);
		$this->assertType('KalturaMixEntryArray', $resultObject);
	}

	/**
	 * Tests mixing->getReadyMediaEntries action
	 * @param string $mixId
	 * @param int $version
	 * @dataProvider provideData
	 */
	public function testGetReadyMediaEntries($mixId, $version = -1)
	{
		$resultObject = $this->client->mixing->getReadyMediaEntries($mixId, $version);
		$this->assertType('KalturaMediaEntryArray', $resultObject);
	}

	/**
	 * Tests mixing->anonymousRank action
	 * @param string $entryId
	 * @param int $rank
	 * @dataProvider provideData
	 */
	public function testAnonymousRank($entryId, $rank)
	{
		$resultObject = $this->client->mixing->anonymousRank($entryId, $rank);
	}

}
