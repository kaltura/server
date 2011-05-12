<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/XInternalServiceBaseTest.php');

/**
 * xInternal service test case.
 */
class XInternalServiceTest extends XInternalServiceBaseTest
{
	/**
	 * Tests xInternal->xAddBulkDownload action
	 * @param string $entryIds
	 * @param string $flavorParamsId
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testXAddBulkDownload($entryIds, $flavorParamsId = null, $reference)
	{
		$resultObject = $this->client->xInternal->xAddBulkDownload($entryIds, $flavorParamsId, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testGet - TODO: replace testGet with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
