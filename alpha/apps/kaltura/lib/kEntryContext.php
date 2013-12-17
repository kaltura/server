<?php
class kEntryContext extends kContext
{
    /**
     * Entry in the context of which the playlist is constructed
     * @var entry
     */
    protected $entry;
    
    /**
     * Flag indicating whether to replace and with the one that
     * it's pointing to via entry->redirectEntryId (if at all). 
     * @var boolean
     */    
    protected $followEntryRedirect;
    
    
    /**
     * @return entry
     */
    public function getEntry ()
    {
        return $this->entry;
    }

	/**
     * @param entry $entry
     */
    public function setEntry ($entry)
    {
        $this->entry = $entry;
    }
    
	/**
     * @return boolean
     */
    public function getFollowEntryRedirect()
    {
        return $this->followEntryRedirect;
    }

	/**
     * @param boolean $followEntryRedirect
     */
    public function setFollowEntryRedirect( $followEntryRedirect )
    {
        $this->followEntryRedirect = $followEntryRedirect;
    }
}