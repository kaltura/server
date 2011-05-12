<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/KalturaInternalToolsSystemHelperServiceBaseTest.php');

/**
 * kalturaInternalToolsSystemHelper service test case.
 */
class KalturaInternalToolsSystemHelperServiceTest extends KalturaInternalToolsSystemHelperServiceBaseTest
{
	/**
	 * Tests kalturaInternalToolsSystemHelper->fromSecureString action
	 * @param string $str
	 * @param KalturaInternalToolsSession $reference
	 * @dataProvider provideData
	 */
	public function testFromSecureString($str, KalturaInternalToolsSession $reference)
	{
		$resultObject = $this->client->kalturaInternalToolsSystemHelper->fromSecureString($str, $reference);
		$this->assertType('KalturaInternalToolsSession', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests kalturaInternalToolsSystemHelper->iptocountry action
	 * @param string $remote_addr
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testIptocountry($remote_addr, $reference)
	{
		$resultObject = $this->client->kalturaInternalToolsSystemHelper->iptocountry($remote_addr, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests kalturaInternalToolsSystemHelper->getRemoteAddress action
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testGetRemoteAddress($reference)
	{
		$resultObject = $this->client->kalturaInternalToolsSystemHelper->getRemoteAddress($reference);
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
