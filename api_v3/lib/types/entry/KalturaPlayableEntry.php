<?php 
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayableEntry extends KalturaBaseEntry
{
	/**
	 * Number of plays
	 * 
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $plays;
	
	/**
	 * Number of views
	 * 
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $views;

	/**
	 * The last time the entry was played
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $lastPlayedAt;
	
	/**
	 * The width in pixels
	 * 
	 * @var int
	 * @readonly
	 */
	public $width;
	
	/**
	 * The height in pixels
	 * 
	 * @var int
	 * @readonly
	 */
	public $height;
	
	/**
	 * The duration in seconds
	 * 
	 * @var int
	 * @readonly
	 * @filter lt,gt,lte,gte,order
	 */
	public $duration;
	
	/**
	 * The duration in miliseconds
	 * 
	 * @var int
	 * 
	 */
	public $msDuration;
	
	/**
	 * The duration type (short for 0-4 mins, medium for 4-20 mins, long for 20+ mins)
	 * 
	 * @var KalturaDurationType
	 * @readonly
	 * @filter matchor
	 */
	public $durationType;
	
	private static $map_between_objects = array
	(
		"plays",
		"views",
		"lastPlayedAt",
		"width",
		"height",
		"msDuration" => "lengthInMsecs",
		"duration" => "durationInt"
	);
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntry::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseEntry::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		/* @var $dbObject entry */
		parent::toObject($dbObject, $skip);
		
		if($this->msDuration)
			$dbObject->setCalculateDuration(false);
			
		return $dbObject;
	}

	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($sourceObject, $responseProfile);
		$recordedLengthInMs = $sourceObject->getRecordedLengthInMsecs();
		if ($sourceObject->getIsRecordedEntry() && $recordedLengthInMs > 0 && myEntryUtils::shouldServeVodFromLive($sourceObject))
		{
			$this->msDuration = $recordedLengthInMs;
			$this->duration = (int)round($recordedLengthInMs / 1000);
		}
		return $this;
	}


}