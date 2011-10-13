<?php

/**
 * mediaInfo service base test case.
 */
abstract class MediaInfoServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		$this->setListActionTestData();

		parent::setUp();
	}

	/**
	 * Set up the testListAction initial data (If needed)
	 */
	protected function setListActionTestData(){}

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
		$this->assertInstanceOf('KalturaMediaInfoListResponse', $resultObject);
		$this->compareApiObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
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
	 * TODO: replace testAdd with last test function that uses that id
	 * @depends testAdd
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * 
	 * Returns the suite for the test
	 */
	public static function suite()
	{
		return new KalturaTestSuite('MediaInfoServiceTest');
	}

}
