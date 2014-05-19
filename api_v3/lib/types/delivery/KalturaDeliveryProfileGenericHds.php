<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileGenericHds extends KalturaDeliveryProfile {
	
	/**
	 * @var string
	 */
	public $pattern;
	
	
	/**
	 * rendererClass
	 * @var string
	 */
	public $rendererClassParam;
	
	
	private static $map_between_objects = array
	(
			"pattern",
			"rendererClassParam",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

