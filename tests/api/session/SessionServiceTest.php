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
	 * @dataProvider provideData
	 */
	public function testStart($secret, $userId = null, KalturaSessionType $type = null, $partnerId = -1, $expiry = 86400, $privileges = null)
	{
		$resultObject = $this->client->session->start($secret, $userId, $type, $partnerId, $expiry, $privileges);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests session->end action
	 * @dataProvider provideData
	 */
	public function testEnd()
	{
		$resultObject = $this->client->session->end();
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
	 * @dataProvider provideData
	 */
	public function testImpersonate($secret, $impersonatedPartnerId, $userId = null, KalturaSessionType $type = null, $partnerId = -1, $expiry = 86400, $privileges = null)
	{
		$resultObject = $this->client->session->impersonate($secret, $impersonatedPartnerId, $userId, $type, $partnerId, $expiry, $privileges);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests session->startWidgetSession action
	 * @param string $widgetId
	 * @param int $expiry
	 * @dataProvider provideData
	 */
	public function testStartWidgetSession($widgetId, $expiry = 86400)
	{
		$resultObject = $this->client->session->startWidgetSession($widgetId, $expiry);
		$this->assertType('KalturaStartWidgetSessionResponse', $resultObject);
	}

}
