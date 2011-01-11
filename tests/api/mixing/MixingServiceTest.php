<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/MixingServiceBaseTest.php');

/**
 * mixing service test case.
 */
class MixingServiceTest extends MixingServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
		parent::validateAdd($mixEntry, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaMixEntry $reference)
	{
		parent::validateGet($entryId, $version, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
		parent::validateUpdate($entryId, $mixEntry, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
		parent::validateDelete($entryId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMixListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests mixing->count action
	 * @param KalturaMediaEntryFilter $filter
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testCount(KalturaMediaEntryFilter $filter = null, $reference)
	{
		$resultObject = $this->client->mixing->count($filter, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests mixing->clone action
	 * @param string $entryId
	 * @param KalturaMixEntry $reference
	 * @dataProvider provideData
	 */
	public function testClone($entryId, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->clone($entryId, $reference);
		$this->assertType('KalturaMixEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests mixing->appendMediaEntry action
	 * @param string $mixEntryId
	 * @param string $mediaEntryId
	 * @param KalturaMixEntry $reference
	 * @dataProvider provideData
	 */
	public function testAppendMediaEntry($mixEntryId, $mediaEntryId, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->appendMediaEntry($mixEntryId, $mediaEntryId, $reference);
		$this->assertType('KalturaMixEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests mixing->requestFlattening action
	 * @param string $entryId
	 * @param string $fileFormat
	 * @param int $version
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testRequestFlattening($entryId, $fileFormat, $version = -1, $reference)
	{
		$resultObject = $this->client->mixing->requestFlattening($entryId, $fileFormat, $version, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests mixing->getMixesByMediaId action
	 * @param string $mediaEntryId
	 * @param KalturaMixEntryArray $reference
	 * @dataProvider provideData
	 */
	public function testGetMixesByMediaId($mediaEntryId, KalturaMixEntryArray $reference)
	{
		$resultObject = $this->client->mixing->getMixesByMediaId($mediaEntryId, $reference);
		$this->assertType('KalturaMixEntryArray', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests mixing->getReadyMediaEntries action
	 * @param string $mixId
	 * @param int $version
	 * @param KalturaMediaEntryArray $reference
	 * @dataProvider provideData
	 */
	public function testGetReadyMediaEntries($mixId, $version = -1, KalturaMediaEntryArray $reference)
	{
		$resultObject = $this->client->mixing->getReadyMediaEntries($mixId, $version, $reference);
		$this->assertType('KalturaMediaEntryArray', $resultObject);
		// TODO - add here your own validations
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
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
