<?php
class kEntryContext extends kContext
{
    /**
     * Entry in the context of which the playlist is constructed
     * @var entry
     */
    protected $entry;
    
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

}