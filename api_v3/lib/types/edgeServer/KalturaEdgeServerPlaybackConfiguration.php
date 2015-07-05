<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEdgeServerPlaybackConfiguration extends KalturaObject
{	
	/**
	 * Is Kaltura unicast to multicast feature enabled on this edge server
	 * @var bool
	 */
	public $multicastEnabled;
	
	private static $map_between_objects = array
	(
		"multicastEnabled",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new EdgeServerPlyabackConfiguration();
		}
	
		return parent::toObject($dbObject, $propsToSkip);
	}
}

