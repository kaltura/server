<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileLivePackager extends KalturaDeliveryProfile {
	
	/**
	 * Domain used to sign the live url
	 * @var string
	 */
	public $livePackagerSigningDomain;

	/**
	 * @var bool
	 */
	public $shouldRedirect;
	
	private static $map_between_objects = array
	(
		"livePackagerSigningDomain",
		"shouldRedirect"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
}

