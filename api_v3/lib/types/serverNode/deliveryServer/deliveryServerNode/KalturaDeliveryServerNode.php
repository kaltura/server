<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaDeliveryServerNode extends KalturaServerNode
{
	/**
	 * Delivery profile ids
	 * @var KalturaKeyValueArray
	 */
	public $deliveryProfileIds;

	/**
	 * Override server node default configuration - json format
	 * @var string
	 */
	public $config;

	private static $map_between_objects = array 
	(
		"deliveryProfileIds",
		"config",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}