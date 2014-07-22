<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileGenericAppleHttp extends KalturaDeliveryProfile {
	
	/**
	 * @var string
	 */
	public $pattern;
	
	/**
	 * rendererClass
	 * @var string
	 */
	public $rendererClass;
	
	/**
	 * Enable to make playManifest redirect to the domain of the delivery profile
	 *
	 * @var KalturaNullableBoolean
	 */
	public $manifestRedirect;
	
	
	private static $map_between_objects = array
	(
			"pattern",
			"rendererClass",
			"manifestRedirect",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}

