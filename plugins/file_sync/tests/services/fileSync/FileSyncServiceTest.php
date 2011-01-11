<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/FileSyncServiceBaseTest.php');

/**
 * fileSync service test case.
 */
class FileSyncServiceTest extends FileSyncServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null, KalturaFileSyncListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests fileSync->sync action
	 * @param int $fileSyncId
	 * @param file $fileData
	 * @param KalturaFileSync $reference
	 * @dataProvider provideData
	 */
	public function testSync($fileSyncId, file $fileData, KalturaFileSync $reference)
	{
		$resultObject = $this->client->fileSync->sync($fileSyncId, $fileData, $reference);
		$this->assertType('KalturaFileSync', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testAdd - TODO: replace testAdd with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
