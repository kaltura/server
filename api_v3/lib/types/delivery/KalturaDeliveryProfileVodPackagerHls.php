<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileVodPackagerHls extends KalturaDeliveryProfileVodPackagerPlayServer
{
	/**
	 * @var bool
	 */
	public $allowFairplayOffline;

	/**
	 * @var bool
	 */
	public $supportFmp4;

	private static $map_between_objects = array
	(
		'allowFairplayOffline',
		'supportFmp4',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}
