<?php
/**
 * @package plugins.playReady
 * @subpackage api.objects
 */
class KalturaPlayReadyCopyRight extends KalturaPlayReadyRight
{
    /**
	 * @var int
	 */
	public $copyCount;
	
	/**
	 * @var KalturaPlayReadyCopyEnablerHolderArray
	 */
	public $copyEnablers;
	
	private static $map_between_objects = array(
		'copyCount',
    	'copyEnablers',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new PlayReadyCopyRight();
			
		parent::toObject($dbObject, $skip);
					
		return $dbObject;
	}
}


