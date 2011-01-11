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
	 * @param KalturaMediaInfoListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference)
	{
		$resultObject = $this->client->mediaInfo->list($filter, $pager);
		$this->assertType('KalturaMediaInfoListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
