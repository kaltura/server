<?php

/**
 * fileSync service base test case.
 */
abstract class FileSyncServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests fileSync->list action
	 * @param KalturaFileSyncFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaFileSyncListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null, KalturaFileSyncListResponse $reference)
	{
		$resultObject = $this->client->fileSync->list($filter, $pager);
		$this->assertType('KalturaFileSyncListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null, KalturaFileSyncListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
