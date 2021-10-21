<?php


class kLiveStreamConsumer implements kObjectChangedEventConsumer
{
    /**
     * @inheritDoc
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        if ($object instanceof LiveEntry)
        {
            $this->handleLiveEntryChanged($object, $modifiedColumns);
        }

        return true;
    }

    protected function handleLiveEntryChanged(entry $object, array $modifiedColumns)
    {
    	if(!in_array(entryPeer::CATEGORIES, $modifiedColumns))
        {
            return false;
        }

        /* @var $object LiveEntry */
        $recordedEntry = entryPeer::retrieveByPK($object->getRecordedEntryId());
        if (!$recordedEntry)
        {
            KalturaLog::info("Recorded entry {$object->getRecordedEntryId()} could not be retrieved. Skipping.");
            return true;
        }
        
        $categories = $object->getCategories();
		if (is_null($categories))
		{
			KalturaLog::info("Categories for live entry {$object->getId()} could not be retrieved retrieved. Skipping.");
			return true;
		}

        $recordedEntry->setCategories($categories);
		$recordedEntry->save();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        if (!($object instanceof LiveEntry))
        {
            return false;
        }
        
        if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_KALTURA_LIVE_SYNC_RECORDED_VOD_CATEGORY, kCurrentContext::getCurrentPartnerId()))
        {
            return false;
        }
		
        return true;
    }
}
