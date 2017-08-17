<?php
/**
 * @package plugins.viewHistory
 * @subpackage api
 */
class KalturaViewHistoryUserEntry extends KalturaUserEntry
{
	/**
	 * Playback context
	 * @var string 
	 */
	public $playbackContext;
	
	/**
	 * Last playback time reached by user
	 * @var int
	 */
	public $lastTimeReached;
	
	/**
	 * @var time
	 */
	public $lastUpdateTime;

	/**
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'playbackContext',
		'lastTimeReached',
		'lastUpdateTime',
	);
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}	
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new ViewHistoryUserEntry();
			
		return parent::toObject($dbObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toInsertableObject($object_to_fill, $props_to_skip);
		if (kCurrentContext::getCurrentSessionType() == SessionType::USER)
		{
			if ($this->userId && (!kCurrentContext::getCurrentKsKuser() || kCurrentContext::getCurrentKsKuser()->getPuserId() != $this->userId))
			{
				throw new KalturaAPIException (KalturaErrors::INVALID_USER_ID);	
			}
		}
		
		return $object_to_fill;
	}
	
}