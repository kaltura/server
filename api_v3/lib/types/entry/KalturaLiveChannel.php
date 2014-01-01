<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveChannel extends KalturaLiveEntry
{
	/**
	 * Playlist id to be played
	 * 
	 * @var string
	 */
	public $playlistId;
	
	/**
	 * Indicates that the segments should be repeated for ever
	 * @var bool
	 */
	public $repeat;
	
	private static $map_between_objects = array
	(
		'playlistId',
		'repeat',
	);

	public function __construct()
	{
		parent::__construct();
		
		$this->type = KalturaEntryType::LIVE_CHANNEL;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::fromSourceType()
	 */
	protected function fromSourceType(entry $entry) 
	{
		$this->sourceType = KalturaSourceType::LIVE_CHANNEL;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaMediaEntry::toSourceType()
	 */
	protected function toSourceType(entry $entry) 
	{
		$entry->setSource(KalturaSourceType::LIVE_CHANNEL);
	}
}
