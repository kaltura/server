<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamEntry extends KalturaMediaEntry
{
	/**
	 * The message to be presented when the stream is offline
	 * 
	 * @var string
	 */
	public $offlineMessage;
	
	/**
	 * The stream id as provided by the provider
	 * 
	 * @var string
	 * @readonly
	 */
	public $streamRemoteId;
	
	/**
	 * The backup stream id as provided by the provider
	 * 
	 * @var string
	 * @readonly
	 */
	public $streamRemoteBackupId;
	
	/**
	 * Array of supported bitrates
	 * 
	 * @var KalturaLiveStreamBitrateArray
	 */
	public $bitrates;
	
	
	private static $map_between_objects = array
	(
		"offlineMessage",
		"streamRemoteId",
	 	"streamRemoteBackupId",
	);

	public function __construct()
	{
		parent::__construct();
		
		$this->type = KalturaEntryType::LIVE_STREAM;
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function fromObject ( $source_object )
	{
		if(!($source_object instanceof entry))
			return;
			
		parent::fromObject($source_object);

		$bitrates = $source_object->getStreamBitrates();
		if(is_array($bitrates))
			$this->bitrates = KalturaLiveStreamBitrateArray::fromLiveStreamBitrateArray($bitrates);
	}
	
	public function toObject ( $dbObject = null , $props_to_skip = array() )
	{
		parent::toObject($dbObject, $props_to_skip);
		
		if($this->bitrates)
			$dbObject->setStreamBitrates($this->bitrates->toArray());
			
		return $dbObject;
	}
}
?>