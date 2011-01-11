<?php

/**
 * mixing service base test case.
 */
abstract class MixingServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests mixing->add action
	 * @param KalturaMixEntry $mixEntry Mix entry metadata
	 * @param KalturaMixEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->add($mixEntry);
		$this->assertType('KalturaMixEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($mixEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
	}

	/**
	 * Tests mixing->get action
	 * @param string $entryId Mix entry id
	 * @param int $version Desired version of the data
	 * @param KalturaMixEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($entryId, $version = -1, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->get($entryId, $version);
		$this->assertType('KalturaMixEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($entryId, $version, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaMixEntry $reference)
	{
	}

	/**
	 * Tests mixing->update action
	 * @param string $entryId Mix entry id to update
	 * @param KalturaMixEntry $mixEntry Mix entry metadata to update
	 * @param KalturaMixEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
		$resultObject = $this->client->mixing->update($entryId, $mixEntry);
		$this->assertType('KalturaMixEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($entryId, $mixEntry, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaMixEntry $mixEntry, KalturaMixEntry $reference)
	{
	}

	/**
	 * Tests mixing->delete action
	 * @param string $entryId Mix entry id to delete
	 * @dataProvider provideData
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->mixing->delete($entryId);
		$this->validateDelete($entryId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
	}

	/**
	 * Tests mixing->list action
	 * @param KalturaMixEntryFilter $filter Mix entry filter
	 * @param KalturaFilterPager $pager Pager
	 * @param KalturaMixListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMixListResponse $reference)
	{
		$resultObject = $this->client->mixing->list($filter, $pager);
		$this->assertType('KalturaMixListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaMixListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
