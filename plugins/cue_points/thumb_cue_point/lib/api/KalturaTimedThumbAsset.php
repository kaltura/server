<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage api.objects
 */
class KalturaTimedThumbAsset extends KalturaThumbAsset  
{
	/**
	 * Associated thumb cue point ID
	 * @var string
	 */
	public $thumbCuePointId;

	
	private static $map_between_objects = array
	(
		"thumbCuePointId",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new timedThumbAsset();
		
		return parent::toInsertableObject ($object_to_fill, $props_to_skip);
	}
}