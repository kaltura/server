<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/NotificationServiceBaseTest.php');

/**
 * notification service test case.
 */
class NotificationServiceTest extends NotificationServiceBaseTest
{
	/**
	 * Tests notification->getClientNotification action
	 * @param string $entryId
	 * @param KalturaNotificationType $type
	 * @dataProvider provideData
	 */
	public function testGetClientNotification($entryId, KalturaNotificationType $type)
	{
		$resultObject = $this->client->notification->getClientNotification($entryId, $type);
		$this->assertType('KalturaClientNotification', $resultObject);
	}

}
