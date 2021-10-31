<?php


class kLiveStreamConsumer implements kObjectChangedEventConsumer
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
		
		return true;
	}

	protected function handleLiveEntryCategoryChanged(LiveEntry $liveEntry)
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, $liveEntry->getPartnerId()))
		{
			$recordedEntry = entryPeer::retrieveByPK($liveEntry->getRecordedEntryId());
			if (!$recordedEntry)
			{
				KalturaLog::info("Recorded entry {$liveEntry->getRecordedEntryId()} could not be retrieved.");
				return false;
			}
			
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
			$recordedEntryId = $liveEntry->getRecordedEntryId();
			$recordedEntry = BaseentryPeer::retrieveByPK($recordedEntryId);
			if (!$recordedEntry)
			{
				KalturaLog::info("Recorded entry {$liveEntry->getRecordedEntryId()} could not be retrieved.");
				return false;
			}
			
			$liveEntryCategories = retrieveCategoriesArrayFromEntry($liveEntry);
			$recordedEntryCategories = retrieveCategoriesArrayFromEntry($recordedEntry);
			
			$categoriesToAdd = array_diff($liveEntryCategories, $recordedEntryCategories);
			$categoriesToRemove = array_diff($recordedEntryCategories, $liveEntryCategories);
				
			foreach ($categoriesToAdd as $categoryId)
			{
				$categoryEntry = new categoryEntry();
				$categoryEntry->add($recordedEntryId, $categoryId);
				$categoryEntry->setEntryId($recordedEntryId);
				$categoryEntry->setCategoryId($categoryId);
				$categoryEntry ->save();
			}
			
			foreach ($categoriesToRemove as $categoryId)
			{
				$categoryToRemove = CategoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $recordedEntryId);
				$categoryToRemove->setAsDeleted();
				$categoryToRemove ->save();
			}
			
			if ($categoriesToRemove)
			{
				myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $recordedEntry);
			}
		}
		
		return true;
	}
	
	protected function retrieveCategoriesArrayFromEntry($entry)
	{
		$categoriesIds = $entry->getCategoriesIds(false);
		if (is_null($categoriesIds))
		{
			KalturaLog::info("Categories for live entry {$entry->getId()} could not be retrieved.");
			return array();
		}
		return explode(entry::ENTRY_CATEGORY_SEPARATOR, $categoriesIds);
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
		
		return false;
	}
}