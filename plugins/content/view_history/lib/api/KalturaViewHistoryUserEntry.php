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
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'playbackContext',
		'lastTimeReached',
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
	 * @see KalturaObject::toUpdatableObject()
	 */
	public function toUpdatableObject($dbObject = null, $propertiesToSkip = array())
	{
		if(is_null($dbObject))
			$dbObject = new ViewHistoryUserEntry();
		
		$dbObject->setUpdatedAt(time());	
		return parent::toUpdatableObject($dbObject, $propertiesToSkip);
	}
}