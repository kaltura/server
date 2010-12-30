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
	 * @dataProvider provideData
	 */
	public function testXAddBulkDownload($entryIds, $flavorParamsId = null)
	{
		$resultObject = $this->client->xInternal->xAddBulkDownload($entryIds, $flavorParamsId);
		$this->assertType('string', $resultObject);
	}

}
