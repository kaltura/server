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
	 * @var KalturaNullableBoolean
	 */
	public $loop;

	/**
	 * the status of the entry of type EntryServerNodeStatus
	 * @var KalturaEntryServerNodeStatus
	 */
	public $liveChannelStatus;

	private static $map_between_objects = array
	(
		'playlistId',
		'loop' => 'repeat',
		'liveChannelStatus',
	);

	/* (non-PHPdoc)
	 * @see KalturaLiveEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
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

	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		$this->validteInsertOrUpdate();
		$this->validatePropertyNotNull('startDate');
		$this->validatePropertyNotNull('playlistId');
		if (!(isset($this->endDate)|| is_null($this->endDate)) && !(isset($this->loop) || $this->loop === false))
			throw new KalturaAPIException(KalturaErrors::SIMU_LIVE_END_DATE_OR_LOOP);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
		$this->validteInsertOrUpdate();
	}


	private function validteInsertOrUpdate()
	{
		if (isset($this->endDate) && $this->endDate < $this->startDate)
			throw new KalturaAPIException(KalturaErrors::SIMU_LIVE_START_DATE_GREATER_THAN_END_DATE);

		if (isset($this->playlistId))
		{
			$playlist = entryPeer::retrieveByPK($this->playlistId);
			if (!$playlist)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->playlistId);
		}
	}
}
