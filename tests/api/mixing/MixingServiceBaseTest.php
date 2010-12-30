<?php

/**
 * mixing service base test case.
 */
abstract class MixingServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests mixing->add action
	 * @param KalturaMixEntry $mixEntry
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMixEntry $mixEntry)
	{
		$resultObject = $this->client->mixing->add($mixEntry);
		$this->assertType('KalturaMixEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests mixing->get action
	 * @param string $entryId
	 * @param int $version
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($entryId, $version = -1)
	{
		$resultObject = $this->client->mixing->get($entryId, $version);
		$this->assertType('KalturaMixEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests mixing->update action
	 * @param string $entryId
	 * @param KalturaMixEntry $mixEntry
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate($entryId, KalturaMixEntry $mixEntry)
	{
		$resultObject = $this->client->mixing->update($entryId, $mixEntry);
		$this->assertType('KalturaMixEntry', $resultObject);
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
	 * Tests mixing->delete action
	 * @param string $entryId
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($entryId)
	{
		$resultObject = $this->client->mixing->delete($entryId);
	}

	/**
	 * Tests mixing->list action
	 * @param KalturaMixEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaMixEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->mixing->listAction($filter, $pager);
		$this->assertType('KalturaMixListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
