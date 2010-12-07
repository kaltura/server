<?php
class KalturaDistributionThumbDimensions extends KalturaObject
{
	/**
	 * @var int
	 */
	public $width;
	
	/**
	 * @var int
	 */
	public $height;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
	 	"width", 
	 	"height", 
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if(is_null($dbObject))
			$dbObject = new kDistributionThumbDimensions();
			
		return parent::toObject($dbObject, $skip);
	}
}