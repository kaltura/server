<?php


class kLiveStreamEventConsumer implements kObjectChangedEventConsumer, kObjectDataChangedEventConsumer
{

    /**
     * @inheritDoc
     */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        // TODO: Implement objectChanged() method.
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

        if (!($object instanceof entry))
        {
            return false;
        }

        if (!($object instanceof LiveEntry))
        {
            return false;
        }

        if (!in_array(entryPeer::NAME, $modifiedColumns) && !in_array(entryPeer::DESCRIPTION, $modifiedColumns))
        {
            return false;
        }

        return true;

    }

    /**
     * @inheritDoc
     */
    public function objectDataChanged(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null)
    {

    }

    /**
     * @inheritDoc
     */
    public function shouldConsumeDataChangedEvent(BaseObject $object, $previousVersion = null)
    {
        // if object is not thumbAsset - return false

        // if thumbAsset entry ID is not a live entry or recorded entry - return false

        // if thumbAsset tags are part of a set list - return false

        //if thumbAsset belongs to recorded entry but live entry has its own custom thumbnails with tags other than set list - return false
    }
}