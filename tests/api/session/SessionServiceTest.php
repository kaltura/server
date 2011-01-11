<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/SessionServiceBaseTest.php');

/**
 * session service test case.
 */
class SessionServiceTest extends SessionServiceBaseTest
{
	/**
	 * Tests session->start action
	 * @param string $secret
	 * @param string $userId
	 * @param KalturaSessionType $type
	 * @param int $partnerId
	 * @param int $expiry
	 * @param string $privileges
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testStart($secret, $userId = null, $type = null, $partnerId = -1, $expiry = 86400, $privileges = null, $reference)
	{
		$resultObject = $this->client->session->start($secret, $userId, $type, $partnerId, $expiry, $privileges, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests session->end action
	 * @dataProvider provideData
	 */
	public function testEnd()
	{
		$resultObject = $this->client->session->end();
		// TODO - add here your own validations
	}

	/**
	 * Tests session->impersonate action
	 * @param string $secret
	 * @param int $impersonatedPartnerId
	 * @param string $userId
	 * @param KalturaSessionType $type
	 * @param int $partnerId
	 * @param int $expiry
	 * @param string $privileges
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testImpersonate($secret, $impersonatedPartnerId, $userId = null, $type = null, $partnerId = -1, $expiry = 86400, $privileges = null, $reference)
	{
		$resultObject = $this->client->session->impersonate($secret, $impersonatedPartnerId, $userId, $type, $partnerId, $expiry, $privileges, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests session->startWidgetSession action
	 * @param string $widgetId
	 * @param int $expiry
	 * @param KalturaStartWidgetSessionResponse $reference
	 * @dataProvider provideData
	 */
	public function testStartWidgetSession($widgetId, $expiry = 86400, KalturaStartWidgetSessionResponse $reference)
	{
		$resultObject = $this->client->session->startWidgetSession($widgetId, $expiry, $reference);
		$this->assertType('KalturaStartWidgetSessionResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
