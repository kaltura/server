<?php


class kLiveStreamEventConsumer implements kObjectChangedEventConsumer
{

    /**
     * @inheritDoc
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        if ($object instanceof entry)
        {
            $this->handleEntryChanged($object);
        }

        if ($object instanceof asset)
        {
            $this->handleAssetChanged($object);
        }

        return true;
    }

    protected function handleAssetChanged (asset $object)
    {
        if (!($object instanceof thumbAsset))
        {
            KalturaLog::info('The object being handled is not a ThumbAsset. Skipping');
            return true;
        }
        /* @var $object thumbAsset */
        $entry = entryPeer::retrieveByPK($object->getEntryId());
        if (!$entry)
        {
            KalturaLog::info("Thumb asset entry ID {$object->getEntryId()} could not be retrieved. Skipping");
            return true;
        }

        if ($entry instanceof LiveEntry)
        {
            $recordedEntry = entryPeer::retrieveByPK($entry->getRecordedEntryId());
            if (!$recordedEntry)
            {
                KalturaLog::info("Recorded entry ID for live entry {$entry->getId()} could not be retrieved. Skipping.");
                return true;
            }

            $object->copyToEntry($recordedEntry->getId());
        }

        return true;
    }

    protected function handleEntryChanged(entry $object)
    {
        if (!($object instanceof LiveEntry) || !($object->getRecordedEntryId()))
        {
            KalturaLog::info("Entry {$object->getId()} is either not a live entry, or does not have recorded entry ID. Skipping.");
            return true;
        }

        /* @var $object LiveEntry */
        $recordedEntry = entryPeer::retrieveByPK($object->getRecordedEntryId());
        if (!$recordedEntry)
        {
            KalturaLog::info("Recorded entry {$object->getRecordedEntryId()} could not be retrieved. Skipping.");
            return true;
        }

        $changesMade = false;
        if ($recordedEntry->getDescription() != $object->getDescription())
        {
            $changesMade = true;
            $recordedEntry->setDescription($object->getDescription());
        }

        if (strpos($recordedEntry->getName(), $object->getName()) !== 0)
        {
            $changesMade = true;
            $recordedEntry->setName($object->getName());
        }

        if ($changesMade)
        {
            KalturaLog::info("Resetting recorded entry ID {$object->getRecordedEntryId()} name/description");
            $recordedEntry->save();
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SYNC_VOD_LIVE_METADATA, kCurrentContext::getCurrentPartnerId()))
        {
            //This feature is dependent on a partner level permission
            return false;
        }

        if ($object instanceof entry && (!($object instanceof LiveEntry) || !($object->getRecordedEntryId())))
        {
            return false;
        }

        if ($object instanceof asset)
        {
            if (!($object instanceof thumbAsset))
            {
                return false;
            }

            if (!in_array(assetPeer::STATUS, $modifiedColumns) || $object->getStatus() != asset::ASSET_STATUS_READY)
            {
                return false;
            }

            $entry = entryPeer::retrieveByPK($object->getEntryId());
            if (!$entry || (!($entry instanceof LiveStreamEntry) && $entry->getSourceType() != EntrySourceType::KALTURA_RECORDED_LIVE))
            {
                return false;
            }

            $thumbAssetTags = explode($object->getTags());
            $excludedTags = kConf::get('default_live_thumbasset_tags');
            if (count(array_intersect($thumbAssetTags, $excludedTags)))
            {
                return false;
            }

            //if thumbAsset belongs to recorded entry but live entry has its own custom thumbnails with tags other than set list - return false
        }

        return true;

    }


}