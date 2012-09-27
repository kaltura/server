<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryContext extends KalturaContext
{
    /**
     * The entry ID in the context of which the playlist should be built
     * @var string
     */
    public $entryId;
    
    /* (non-PHPdoc)
     * @see KalturaPlaylistContext::validate()
     */
    protected function validate ()
    {
        //Validate the provided entryId belongs to the partner and that it is a valid entry (status READY, etc)
        $entry = entryPeer::retrieveByPK($this->entryId);
        if (!$entry)
            throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
        
    }
    
    /* (non-PHPdoc)
     * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
     */
    public function toObject($dbObject = null, $skip = array())
    {
        $this->validate();
        if (!$dbObject)
        {
            $dbObject = new kEntryContext();
        }
        
        parent::toObject($dbObject);
        $dbObject->setEntry(entryPeer::retrieveByPK($this->entryId));
        return $dbObject;
    }
}