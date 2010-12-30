<?php

/**
 * mediaInfo service base test case.
 */
abstract class MediaInfoServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests mediaInfo->list action
	 * @param KalturaMediaInfoFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->mediaInfo->listAction($filter, $pager);
		$this->assertType('KalturaMediaInfoListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
