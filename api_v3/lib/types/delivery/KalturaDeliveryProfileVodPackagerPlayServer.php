<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileVodPackagerPlayServer extends KalturaDeliveryProfile
{
	/**
	 * @var bool
	 */
	public $adStitchingEnabled;

	/**
	 * @var bool
	 */
	public $simuliveSupport;

	private static $map_between_objects = array
	(
		'adStitchingEnabled',
		'simuliveSupport'
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}