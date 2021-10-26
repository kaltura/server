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
			$this->handleLiveEntryCategoryChanged($object, $modifiedColumns);
			return true;
		}
		
		return true;
	}

	protected function handleLiveEntryCategoryChanged(entry $object, array $modifiedColumns)
	{
		/* @var $object LiveEntry */
		$recordedEntry = entryPeer::retrieveByPK($object->getRecordedEntryId());
		if (!$recordedEntry)
		{
			KalturaLog::info("Recorded entry {$object->getRecordedEntryId()} could not be retrieved.");
			return false;
		}
		
		$categories = $object->getCategories();
		if (is_null($categories))
		{
			KalturaLog::info("Categories for live entry {$object->getId()} could not be retrieved.");
			return false;
		}
		
		$recordedEntry->setCategories($categories);
		$recordedEntry->save();
		
		return true;
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