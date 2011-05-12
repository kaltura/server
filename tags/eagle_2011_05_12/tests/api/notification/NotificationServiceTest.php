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
	 * @param KalturaClientNotification $reference
	 * @dataProvider provideData
	 */
	public function testGetClientNotification($entryId, $type, KalturaClientNotification $reference)
	{
		$resultObject = $this->client->notification->getClientNotification($entryId, $type, $reference);
		$this->assertType('KalturaClientNotification', $resultObject);
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
