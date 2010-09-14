<?php
require_once("tests/bootstrapTests.php");

class ClientNotificationTest extends PHPUnit_Framework_TestCase 
{
	private $_notificationService;
	private $_url = "http://localhost/dummy_notifications.php";
	
	public function setUp() 
	{
		$this->_notificationService = KalturaTestsHelpers::getServiceInitializedForAction("notification", "getclientnotification");
		
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setUrl2($this->_url);
		$partner->save();
	}
	
	public function tearDown() 
	{
		// clean up all notifications for the testing partner
		$c = new Criteria();
		$c->add(notificationPeer::PARTNER_ID, KalturaTestsHelpers::getPartner()->getId());
		$notifications = notificationPeer::doSelect($c);
		foreach($notifications as $notification)
		{
			$notification->delete();
		}
		
		// reset the notifications config
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotificationsConfig(null);
		$partner->setNotify(false);
		$partner->save();
		
		$this->_notificationService = null;
	}
	
	public function testExceptionForInvalidEntryId()
	{
		try
		{
			$notifications = $this->_notificationService->getClientNotificationAction("abc", KalturaNotificationType::ENTRY_ADD);
			self::fail("Expecting exception");
		}
		catch(Exception $ex)
		{
			self::assertEquals($ex->getCode(), "NOTIFICATION_FOR_ENTRY_NOT_FOUND");
		}
	}

	public function testExceptionWhenNotifyIsOff()
	{
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotify(false);
		
		$mediaEntry = $this->addNewMediaEntry();
		try
		{
			$notifications = $this->_notificationService->getClientNotificationAction($mediaEntry->id, KalturaNotificationType::ENTRY_ADD);
			self::fail("Expecting exception");
		}
		catch(Exception $ex)
		{
			self::assertEquals($ex->getCode(), "NOTIFICATION_FOR_ENTRY_NOT_FOUND");
		}
	}
	
	public function testExceptionWhenNotificationConfigIsNoSend()
	{
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotify(true);
		$partner->setNotificationsConfig(KalturaNotificationType::ENTRY_ADD."=".myNotificationMgr::NOTIFICATION_MGR_NO_SEND);

		$partner->save();
		$mediaEntry = $this->addNewMediaEntry();
		try
		{
			$notifications = $this->_notificationService->getClientNotificationAction($mediaEntry->id, KalturaNotificationType::ENTRY_ADD);
			self::fail("Expecting exception");
		}
		catch(Exception $ex)
		{
			self::assertEquals($ex->getCode(), "NOTIFICATION_FOR_ENTRY_NOT_FOUND");
		}
	}
	
	public function testEmptyObjectWhenNotificationConfigIsAsync()
	{
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotify(true);
		$partner->setNotificationsConfig(KalturaNotificationType::ENTRY_ADD."=".myNotificationMgr::NOTIFICATION_MGR_SEND_ASYNCH);
		$partner->save();
		
		$mediaEntry = $this->addNewMediaEntry();
		$notification = $this->_notificationService->getClientNotificationAction($mediaEntry->id, KalturaNotificationType::ENTRY_ADD);
		self::assertNull($notification->url);
		self::assertNull($notification->data);
	}
	
	public function testWhenNotificationConfigIsSynch()
	{
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotify(true);
		$partner->setNotificationsConfig(KalturaNotificationType::ENTRY_ADD."=".myNotificationMgr::NOTIFICATION_MGR_SEND_SYNCH);
		$partner->save();
		
		$mediaEntry = $this->addNewMediaEntry();
		$notification = $this->_notificationService->getClientNotificationAction($mediaEntry->id, KalturaNotificationType::ENTRY_ADD);
		self::assertEquals($this->_url, $notification->url);
		$this->assertNotificationData($notification->data, $mediaEntry);
	}
	
	public function testWhenNotificationConfigIsBoth()
	{
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotify(true);
		$partner->setNotificationsConfig(KalturaNotificationType::ENTRY_ADD."=".myNotificationMgr::NOTIFICATION_MGR_SEND_BOTH);
		$partner->save();
		
		$mediaEntry = $this->addNewMediaEntry();
		$notification = $this->_notificationService->getClientNotificationAction($mediaEntry->id, KalturaNotificationType::ENTRY_ADD);
		self::assertEquals($this->_url, $notification->url);
		$this->assertNotificationData($notification->data, $mediaEntry);
	}
	
	public function testWhenAllNotificationsConfigsAreBoth()
	{
		$partner = KalturaTestsHelpers::getPartner();
		$partner->setNotify(true);
		$partner->setNotificationsConfig("*=".myNotificationMgr::NOTIFICATION_MGR_SEND_BOTH);
		$partner->save();
		
		$mediaEntry = $this->addNewMediaEntry();
		$notification = $this->_notificationService->getClientNotificationAction($mediaEntry->id, KalturaNotificationType::ENTRY_ADD);
		self::assertEquals($this->_url, $notification->url);
		$this->assertNotificationData($notification->data, $mediaEntry);
	}
	
	private function addNewMediaEntry()
	{
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "addFromUrl");
		$mediaEntry = new KalturaMediaEntry();
		$mediaEntry->name = KalturaTestsHelpers::getRandomText(5);
		$mediaEntry->mediaType = KalturaMediaType::VIDEO;
		return $mediaService->addFromUrlAction($mediaEntry, "url");
	}

	private function assertNotificationData($data, $entry)
	{
		$params = array();
		parse_str($data, $params);
		
		self::assertEquals($entry->userId, $params["puser_id"]);
		self::assertEquals($entry->partnerId, $params["partner_id"]);
		self::assertEquals($entry->id, $params["entry_id"]);
		self::assertEquals($entry->name, $params["name"]);
		self::assertEquals($entry->tags, $params["tags"]);
		self::assertEquals(trim($entry->searchText), trim($params["search_text"]));
		self::assertEquals($entry->mediaType, $params["media_type"]);
		self::assertEquals($entry->thumbnailUrl, $params["thumbnail_url"]);
		self::assertEquals($entry->partnerData, $params["partner_data"]);
		self::assertEquals($entry->width, $params["width"]);
		self::assertEquals($entry->height, $params["height"]);
		self::assertEquals($entry->dataUrl, $params["data_url"]);
		self::assertEquals($entry->downloadUrl, $params["download_url"]);
		self::assertEquals($entry->mediaDate, $params["media_date"]);
	}
}