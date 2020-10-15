<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileGenericHttp extends KalturaDeliveryProfileHttp {
	
	/**
	 * @var string
	 */
	public $pattern;
	
	
	private static $map_between_objects = array
	(
			"pattern"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

