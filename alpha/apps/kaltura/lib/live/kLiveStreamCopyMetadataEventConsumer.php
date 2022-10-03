<?php


class kLiveStreamCopyMetadataEventConsumer implements kObjectChangedEventConsumer
{
    const LIVE_STREAM_VOD_THUMBNAIL_TAG = 'live_entry_vod';
    /**
     * @inheritDoc
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        if ($object instanceof LiveEntry)
        {
            $this->handleLiveEntryChanged($object, $modifiedColumns);
        }

        if ($object instanceof thumbAsset)
        {
            $this->handleThumbAssetChanged($object);
        }

        return true;
    }

    protected function handleThumbAssetChanged (asset $object)
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

        if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE){
             $liveThumbAsset = $object->copyToEntry($entry->getRootEntryId());
             $liveThumbAsset->setTags(self::LIVE_STREAM_VOD_THUMBNAIL_TAG);
             $liveThumbAsset->save();
        }

        return true;
    }

    protected function handleLiveEntryChanged(entry $object, array $modifiedColumns)
    {
        if (!($object->getRecordStatus()))
        {
            KalturaLog::info("Entry {$object->getId()} does not have recording enabled. Skipping.");
            return true;
        }

        if(isset($modifiedColumns[kObjectChangedEvent::CUSTOM_DATA_OLD_VALUES][''][LiveEntry::RECORDED_ENTRY_ID])) // array_key_exists
        {
            $this->removeVODThumbAssetFromLiveEntry($object->getId());
        }

        /* @var $object LiveEntry */
        $recordedEntry = entryPeer::retrieveByPK($object->getRecordedEntryId());
        if (!$recordedEntry)
        {
            KalturaLog::info("Recorded entry {$object->getRecordedEntryId()} could not be retrieved. Skipping.");
            return true;
        }

        $recordedEntry->setDescription($object->getDescription());
        if (strpos($recordedEntry->getName(), $object->getName()) !== 0)
        {
            $recordedEntry->setName($object->getName());
        }
		multiLingualUtils::copyMultiLingualValues($recordedEntry, $object);
        KalturaLog::info("Resetting recorded entry ID {$object->getRecordedEntryId()} name/description");
        $recordedEntry->save();

        return true;
    }

    /**
     * @param string $entryId
     * @throws PropelException
     */
    public function removeVODThumbAssetFromLiveEntry($entryId)
    {
        $entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($entryId);
        foreach ($entryThumbAssets as $entryThumbAsset)
        {
            if($entryThumbAsset->hasTag(self::LIVE_STREAM_VOD_THUMBNAIL_TAG))
            {
                $entryThumbAsset->setStatus(thumbAsset::ASSET_STATUS_DELETED);
                $entryThumbAsset->setDeletedAt(time());
                $entryThumbAsset->save();
            }
        }
    }
    /**
     * @inheritDoc
     */
    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        if (!($object instanceof LiveEntry) && !($object instanceof thumbAsset))
        {
            return false;
        }

        if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SYNC_VOD_LIVE_METADATA, kCurrentContext::getCurrentPartnerId()))
        {
            //This feature is dependent on a partner level permission
            return false;
        }

        if ($object instanceof LiveEntry)
        {
            if (!$object->getRecordStatus())
            {
                return false;
            }

            return true;
        }

        if ($object instanceof thumbAsset)
        {
            if (!in_array(assetPeer::STATUS, $modifiedColumns) || $object->getStatus() != asset::ASSET_STATUS_READY)
            {
                return false;
            }

            $entry = entryPeer::retrieveByPK($object->getEntryId());
            if (!$entry || (!($entry instanceof LiveStreamEntry) && $entry->getSourceType() != EntrySourceType::KALTURA_RECORDED_LIVE))
            {
                return false;
            }

            $thumbAssetTags = $object->getTagsArray();
            $excludedTags = kConf::get('default_live_thumbasset_tags');
            $excludedTags[] = self::LIVE_STREAM_VOD_THUMBNAIL_TAG;
            if (count(array_intersect($thumbAssetTags, $excludedTags)))
            {
                return false;
            }
            //if thumbAsset belongs to recorded entry but live entry has its own custom thumbnails with tags other than set list - return false

            if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE)
            { // its recorded entry
                $liveEntry = entryPeer::retrieveByPK($entry->getRootEntryId());
                $liveEntryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($liveEntry->getId());// live entry has its own custom thumbnails

                //Recalculate the excluded tag list
                $excludedTags = kConf::get('default_live_thumbasset_tags');
                foreach($liveEntryThumbAssets as $entryThumbAsset)
                {
                    /* @var $entryThumbAsset thumbAsset */
                    if (!count(array_intersect($entryThumbAsset->getTagsArray(), $excludedTags))) // there is at least one thumbnail with no excluded tags.
                    {
                        return false;
                    }
                }
            }

            return true;

        }

        return false;

    }


}
