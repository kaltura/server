<?php
/**
 * Used to ingest media that is already ingested to Kaltura system as a different file in the past, the new created flavor asset will be ready immediately using a file sync of link type that will point to the existing file sync.
 *
 * @package Core
 * @subpackage model.data
 */
class kLiveEntryResource extends kContentResource 
{
	/**
	 * The live entry to be used as source 
	 * @var LiveEntry
	 */
	private $entry;
	
	/**
	 * @return LiveEntry $entry
	 */
	public function getEntry()
	{
		return $this->entry;
	}

	/**
	 * @param LiveEntry $entry
	 */
	public function setEntry(LiveEntry $entry)
	{
		$this->entry = $entry;
	}
}