<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileVodPackagerHls extends KalturaDeliveryProfile
{
	/**
	 * @var bool
	 */
	public $allowFairplayOffline;

	private static $map_between_objects = array
	(
		'allowFairplayOffline',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}