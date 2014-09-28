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
	
	/**
	 * @var bool
	 */
	public $forceProxy;
	
	private static $map_between_objects = array
	(
			"disableExtraAttributes",
			"forceProxy"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
}

