<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileGenericHttp extends KalturaDeliveryProfile {
	
	/**
	 * @var string
	 */
	public $pattern;

	/**
	 * @var int
	 */
	public $maxSize;
	
	
	private static $map_between_objects = array
	(
			"pattern",
			"maxSize"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

