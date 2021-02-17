<?php


class kLiveStreamEventHandler implements kObjectChangedEventConsumer, kObjectDataChangedEventConsumer
{
    const LIVE_STREAM_VOD_THUMBNAIL_TAG = 'live_entry_vod';
    /**
     * @param LiveStreamEntry $object
     * @param array $modifiedColumns
     * @return bool|void
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        $recordedEntryId = $object->getRecordedEntryId();
        if(in_array(ESearchEntryFieldName::RECORDED_ENTRY_ID, $modifiedColumns) && is_null($recordedEntryId))
        {
            $this->removeThumbAssetFromEntry($object->getId());
        }
        elseif(!is_null($recordedEntryId))
        {
            $this->copyThumbAssetFromEntryToEntry($recordedEntryId, $object->getId());
        }
    }
    /**
     * @param BaseObject $object
     * @param array $modifiedColumns
     * @return false
     */
    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        if(!($object instanceof entry))
        {
            return false;
        }
        if(!($object instanceof LiveStreamEntry))
        {
            return false;
        }
        return true;
    }
    /**
     * @param BaseObject $object
     * @param null $previousVersion
     * @param BatchJob|null $raisedJob
     * @return bool|void
     */
    public function objectDataChanged(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null)
    {
        // TODO: ?
    }
    /**
     * @param BaseObject $object
     * @param null $previousVersion
     * @return bool
     * @throws Exception
     */
    public function shouldConsumeDataChangedEvent(BaseObject $object, $previousVersion = null)
    {
        /** @var thumbAsset $object */
        if(!($object instanceof thumbAsset))
        {
            return false;
        }
        $entry = entryPeer::retrieveByPK($object->getEntryId());
        $rootEntry = null;
        if(!($entry instanceof LiveStreamEntry))
        {
            if($entry->getRootEntryId() == $entry->getId())
            {
                return false;
            }
            $rootEntry = entryPeer::retrieveByPK($entry->getRootEntryId()); // recorded entry
            if(!($rootEntry instanceof LiveStreamEntry))
            {
                return false;
            }
        }
        $tagsContainsDefault = $this->thumbAssetHasOneOfTheDefaultTags($object);
        if($tagsContainsDefault === false)
        {
            return false;
        }
        if(!is_null($rootEntry))
        {
            /** @var thumbAsset[] $entryThumbAssets */
            $entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($rootEntry->getId());
            if(!empty($entryThumbAssets))
            {
                $tagsContainsDefault = $this->thumbAssetsHasOneOfTheDefaultTags($entryThumbAssets);
                if($tagsContainsDefault === false)
                {
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * @param thumbAsset[] $thumbAssets
     * @return bool
     * @throws Exception
     */
    private function thumbAssetsHasOneOfTheDefaultTags(array $thumbAssets){
        foreach($thumbAssets as $entryThumbAsset)
        {
            if($this->thumbAssetHasOneOfTheDefaultTags($entryThumbAsset))
            {
                return true;
            }
        }
        return false;
    }
    /**
     * @param thumbAsset $object
     * @return bool
     * @throws Exception
     */
    private function thumbAssetHasOneOfTheDefaultTags(thumbAsset $object)
    {
        $defaultTags = kConf::get('default_live_thumbasset_tags');
        foreach ($defaultTags as $defaultTag)
        {
            if($object->hasTag($defaultTag))
            {
                return true;
            }
        }
        return false;
    }
    /**
     * @param string $entryId
     * @throws PropelException
     */
    private function removeThumbAssetFromEntry($entryId){
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
     * @param string $oldentryId
     * @param string $newEntryId
     * @throws PropelException
     */
    private function copyThumbAssetFromEntryToEntry($oldentryId, $newEntryId){
        $entryThumbAssets = assetPeer::retrieveThumbnailsByEntryId($oldentryId);
        /** @var thumbAsset $dbThumbAsset */
        $dbThumbAsset = $entryThumbAssets[0];
        $dbThumbAsset->setEntryId($newEntryId);
        $dbThumbAsset->setCreatedAt(time());
        $dbThumbAsset->setUpdatedAt(time());
        $dbThumbAsset->tags(self::LIVE_STREAM_VOD_THUMBNAIL_TAG);
        $dbThumbAsset->save();
    }
}
