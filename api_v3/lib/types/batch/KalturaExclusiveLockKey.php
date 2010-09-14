<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaExclusiveLockKey extends KalturaObject
{
	/**
	 * @var int
	 */
	public $schedulerId;
	
    
	/**
	 * @var int
	 */
	public $workerId;
	
    
	/**
	 * @var int
	 */
	public $batchIndex;
	
    
	private static $map_between_objects = array
	(
		"schedulerId" ,
		"workerId" ,
		"batchIndex" ,
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new kExclusiveLockKey();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}

?>