<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileLivePackagerHls extends KalturaDeliveryProfileLivePackager
{
	/**
	 * @var bool
	 */
	public $disableExtraAttributes;
	
	/**
	 * @var bool
	 */
	public $forceProxy;
	
	/**
	 * Domain used to sign the live url
	 * @var string
	 */
	public $livePackagerSigningDomain;
	
	private static $map_between_objects = array
	(
		"disableExtraAttributes",
		"forceProxy",
		"livePackagerSigningDomain",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
}

