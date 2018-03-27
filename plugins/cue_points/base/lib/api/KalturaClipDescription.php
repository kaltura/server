<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaClipDescription extends KalturaObject
{
	/**
	 * @var string
	 */
	public $sourceEntryId;
	
	/**
	 * 
	 * @var int
	 */
	public $startTime;
	
	/**
	 * 
	 * @var int
	 */
	public $duration;
	
	private static $map_between_objects = array
	(
		"sourceEntryId" ,
		"startTime" ,
		"duration" ,
	);


	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kClipDescription();
			
		return parent::toObject($dbObject, $skip);
	}
}