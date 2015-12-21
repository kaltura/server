<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEdgeServerNode extends KalturaDeliveryServerNode
{
	/**
	 * Delivery profile ids
	 * @var KalturaKeyValueArray
	 */
	public $deliveryProfileIds;
	
	/**
	 * Overdie edge server default configuration - json format
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
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		return parent::validateForInsertByType($propertiesToSkip, serverNodeType::EDGE);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		return parent::validateForUpdateByType($sourceObject, $propertiesToSkip, serverNodeType::EDGE);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new EdgeServerNode();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
}