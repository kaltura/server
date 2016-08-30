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

	private static $map_between_objects = array 
	(
		"deliveryProfileIds",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}