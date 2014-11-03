<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileAkamaiAppleHttpManifest extends KalturaDeliveryProfile {
	
	/**
	 * Should we use timing parameters - clipTo / seekFrom
	 * 
	 * @var bool
	 */
	public $supportClipping;
	
	private static $map_between_objects = array
	(
			"supportClipping",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
}

