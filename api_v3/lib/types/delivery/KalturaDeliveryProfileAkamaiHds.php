<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileAkamaiHds extends KalturaDeliveryProfile {
	
	/**
	 * Should we use timing parameters - clipTo / seekFrom
	 * 
	 * @var bool
	 */
	public $useTimingParameters;
	
	private static $map_between_objects = array
	(
			"useTimingParameters",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

