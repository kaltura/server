<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/VirusScanProfileServiceBaseTest.php');

/**
 * virusScanProfile service test case.
 */
class VirusScanProfileServiceTest extends VirusScanProfileServiceBaseTest
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests virusScanProfile->scan action
	 * @param string $flavorAssetId
	 * @param int $virusScanProfileId
	 * @dataProvider provideData
	 */
	public function testScan($flavorAssetId, $virusScanProfileId = null)
	{
		$resultObject = $this->client->virusScanProfile->scan($flavorAssetId, $virusScanProfileId);
		$this->assertType('int', $resultObject);
	}

}
