<?php
require_once("tests/bootstrapTests.php");

class MediaFlagTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var KalturaMediaEntry
	 */
	private $_mediaEntry;
	
	/**
	 * 
	 * @return KalturaModerationFlag
	 */
	private $_createdModerationFlag;  
	
	public function setUp ()
	{
		parent::setUp();
		
		$this->_mediaEntry = MediaTestsHelpers::createDummyEntry();
	}
	
	public function tearDown ()
	{
		parent::tearDown();
		
		$entry = entryPeer::retrieveByPK($this->_mediaEntry->id);
		if ($entry)
			$entry->delete();
			
		if ($this->_createdModerationFlag)
		{
			$moderationFlag = moderationFlagPeer::retrieveByPK($this->_createdModerationFlag->id);
			if ($moderationFlag)
				$moderationFlag->delete();
		}
	}
	
	public function __construct ()
	{
	}
	
	public function testFlagEntry()
	{
		// get the current moderation count
		$entry = entryPeer::retrieveByPK($this->_mediaEntry->id);
		$currentModerationCount = $entry->getModerationCount();
		
		$mediaService = KalturaTestsHelpers::getServiceInitializedForAction("media", "flag");
		$moderationFlag = MediaTestsHelpers::prepareModerationFlagForEntry($this->_mediaEntry->id);
		$this->_createdModerationFlag = $mediaService->flagAction(clone $moderationFlag);
		
		PHPUnit_Framework_Assert::assertType("KalturaModerationFlag", $this->_createdModerationFlag);
		PHPUnit_Framework_Assert::assertGreaterThan(0, $this->_createdModerationFlag->id);
		PHPUnit_Framework_Assert::assertEquals($moderationFlag->flaggedEntryId, $this->_createdModerationFlag->flaggedEntryId);
		PHPUnit_Framework_Assert::assertEquals($moderationFlag->flagType, $this->_createdModerationFlag->flagType);
		PHPUnit_Framework_Assert::assertEquals($moderationFlag->comments, $this->_createdModerationFlag->comments);
		PHPUnit_Framework_Assert::assertEquals(KalturaModerationFlagStatus::PENDING, $this->_createdModerationFlag->status);
		PHPUnit_Framework_Assert::assertEquals(KalturaModerationObjectType::ENTRY, $this->_createdModerationFlag->objectType);
		PHPUnit_Framework_Assert::assertEquals(KalturaTestsHelpers::getPartner()->getId(), $this->_createdModerationFlag->partnerId);
		PHPUnit_Framework_Assert::assertNull($this->_createdModerationFlag->flaggedUserId);
		PHPUnit_Framework_Assert::assertEquals(KalturaTestsHelpers::getUserId(), $this->_createdModerationFlag->userId);
		
		// assert moderation count on entry was increased
		$entry = entryPeer::retrieveByPK($this->_mediaEntry->id);
		$this->assertEquals($currentModerationCount + 1, $entry->getModerationCount());
	}
	
	public function testIgnoredPropertyOnInsert()
	{
		
	}
	
	public function testExceptionWhenFlagginMissingEntry()
	{
		
	}
	
	public function testExceptionWhenFlaggingEntryOfDifferentType()
	{
		
	}
}

