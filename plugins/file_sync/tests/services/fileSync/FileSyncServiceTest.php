<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/FileSyncServiceBaseTest.php');

/**
 * fileSync service test case.
 */
class FileSyncServiceTest extends FileSyncServiceBaseTest
{
	/**
	 * Tests fileSync->sync action
	 * @param int $fileSyncId
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testSync($fileSyncId, file $fileData)
	{
		$resultObject = $this->client->fileSync->sync($fileSyncId, $fileData);
		$this->assertType('KalturaFileSync', $resultObject);
	}

}
