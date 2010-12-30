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
	 * @dataProvider provideData
	 */
	public function testList(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->fileSync->listAction($filter, $pager);
		$this->assertType('KalturaFileSyncListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
