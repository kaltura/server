<?php

/**
 * mediaInfo service base test case.
 */
abstract class MediaInfoServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests mediaInfo->listAction action
	 * @param KalturaMediaInfoFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaMediaInfoListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference)
	{
		$resultObject = $this->client->mediaInfo->listAction($filter, $pager);
		$this->assertType('KalturaMediaInfoListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
