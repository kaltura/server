<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileLiveAppleHttp extends KalturaDeliveryProfile {
	
	/**
	 * @var bool
	 */
	public $disableExtraAttributes;
	
	private static $map_between_objects = array
	(
			"disableExtraAttributes",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
}

