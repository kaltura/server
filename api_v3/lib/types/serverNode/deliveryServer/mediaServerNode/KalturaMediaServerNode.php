<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaServerNode extends KalturaDeliveryServerNode
{	
	/**
	 * Media server app prefix
	 *
	 * @var string
	 */
	public $appPrefix;
	
	/**
	 * Media server app prefix
	 *
	 * @var KalturaKeyValueArray
	 */
	public $protocolPort;
	
	private static $mapBetweenObjects = array
	(
		'appPrefix',
		'protocolPort',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new MediaServer();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new MediaServer();
	
		$dbObject = parent::toObject($dbObject, $skip);
	
		if (!is_null($this->protocolPort))
		{
			$protocolPort = array();
			foreach($this->protocolPort as $keyValue)
				$protocolPort[$keyValue->key] = $keyValue->value;
			$dbObject->setProtocolPort($protocolPort);
		}
	
		return $dbObject;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $source_object MediaServer */
		parent::doFromObject($source_object, $responseProfile);
	
		if($this->shouldGet('protocolPort', $responseProfile) && !is_null($source_object->getProtocolPort()))
			$this->protocolPort = KalturaKeyValueArray::fromKeyValueArray($source_object->getProtocolPort());
	}
}