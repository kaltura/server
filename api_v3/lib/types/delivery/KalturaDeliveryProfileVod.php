<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDeliveryProfileVod extends KalturaDeliveryProfile
{

	/**
	 * @var bool
	 */
	public $simuliveSupport;

	/**
	 * @var string
	 */
	public $enforceDeliveriesSupport;

	private static $map_between_objects = array
	(
		'simuliveSupport',
		'enforceDeliveriesSupport'
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge (parent::getMapBetweenObjects() , self::$map_between_objects);
	}
}