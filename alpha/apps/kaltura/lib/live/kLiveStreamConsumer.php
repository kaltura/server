<?php


class kLiveStreamConsumer implements kObjectChangedEventConsumer,  kObjectCreatedEventConsumer
{
	/**
	* @inheritDoc
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof LiveEntry && $this->isLiveEntryCategoryChanged($object, $modifiedColumns))
		{
			$this->handleLiveEntryCategoryChanged($object);
			return true;
		}
		
		if ($object instanceof categoryEntry)
		{
			$entry = entryPeer::retrieveByPK($object->getEntryId());
			if ($entry && $entry->getType() == entryType::LIVE_STREAM && $entry->getRecordedEntryId())
			{
				$this->handleLiveEntryCategoryChanged($entry);
			}
		}
		
		return true;
	}

	protected function handleLiveEntryCategoryChanged(LiveEntry $liveEntry)
	{
		$recordedEntryId = $liveEntry->getRecordedEntryIdFromCustomData();
		$recordedEntry = BaseentryPeer::retrieveByPK($recordedEntryId);
		if (!$recordedEntry)
		{
			KalturaLog::info("Recorded entry $recordedEntryId could not be retrieved.");
			return false;
		}
		
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, $liveEntry->getPartnerId()))
		{
			$categories = $liveEntry->getCategories();
			if (is_null($categories))
			{
				KalturaLog::info("Categories for live entry {$liveEntry->getId()} could not be retrieved.");
				return false;
			}
			
			$recordedEntry->setCategories($categories);
			$recordedEntry->save();
		}
		else
		{
			$this->compareAndSyncCategories($liveEntry, $recordedEntry);
		}
		
		return true;
	}
	
	protected function compareAndSyncCategories($liveEntry, $recordedEntry)
	{
		$liveEntryCategories = myEntryUtils::getCategoriesIdsArrayFromEntry($liveEntry);
		$recordedEntryCategories = myEntryUtils::getCategoriesIdsArrayFromEntry($recordedEntry);
		
		$categoriesToAdd = array_diff($liveEntryCategories, $recordedEntryCategories);
		$categoriesToRemove = array_diff($recordedEntryCategories, $liveEntryCategories);
		
		$recordedEntryId = $recordedEntry->getId();
		
		foreach ($categoriesToAdd as $categoryId)
		{
			$categoryEntry = new categoryEntry();
			$categoryEntry->add($recordedEntryId, $categoryId);
			$categoryEntry->setEntryId($recordedEntryId);
			$categoryEntry->setCategoryId($categoryId);
			$categoryEntry->save();
		}
		
		foreach ($categoriesToRemove as $categoryId)
		{
			$categoryToRemove = CategoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $recordedEntryId);
			$categoryToRemove->setAsDeleted();
			$categoryToRemove->save();
		}
		
		if ($categoriesToRemove)
		{
			myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $recordedEntry);
		}
	}

	protected function isLiveEntryCategoryChanged(entry $object, array $modifiedColumns)
	{
		/* @var $object LiveEntry */
		
		if (!$object->getRecordedEntryId())
		{
			return false;
		}
		
		if(!in_array(entryPeer::CATEGORIES, $modifiedColumns))
		{
			return false;
		}
		
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_KALTURA_LIVE_SYNC_RECORDED_VOD_CATEGORY, kCurrentContext::getCurrentPartnerId()))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if ($object instanceof LiveEntry && $this->isLiveEntryCategoryChanged($object, $modifiedColumns))
		{
			return true;
		}
		
		if ($object instanceof categoryEntry)
		{
			return $this->shouldConsumeEventForCategoryEntry($object);
		}
		
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function objectCreated(BaseObject $object)
	{
		if ($object instanceof categoryEntry)
		{
			$entry = entryPeer::retrieveByPK($object->getEntryId());
			if ($entry && $entry->getType() == entryType::LIVE_STREAM && $entry->getRecordedEntryId())
			{
				$this->handleLiveEntryCategoryChanged($entry);
			}
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if ($object instanceof categoryEntry)
		{
			return $this->shouldConsumeEventForCategoryEntry($object);
		}
		return false;
	}

	protected function shouldConsumeEventForCategoryEntry(categoryEntry $categoryEntry)
	{
		$partnerId = $categoryEntry->getPartnerId();

		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, $partnerId) &&
			PermissionPeer::isValidForPartner(PermissionName::FEATURE_KALTURA_LIVE_SYNC_RECORDED_VOD_CATEGORY, $partnerId)) {
			$entry = entryPeer::retrieveByPK($categoryEntry->getEntryId());
			if ($entry && $entry->getType() == entryType::LIVE_STREAM) {
				return true;
			}
		}
		return false;
	}
}