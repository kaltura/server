<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaMediaServerNode extends KalturaDeliveryServerNode
{
	/**
	 * Media server application name
	 *
	 * @var string
	 */
	public $applicationName;
			
	/**
	 * Media server port per protcol configuration
	 *
	 * @var KalturaKeyValueArray
	 */
	public $protocolPortConfig;
	
	private static $mapBetweenObjects = array
	(
		'applicationName',
		'protocolPortConfig',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		$dbObject = parent::toObject($dbObject, $skip);
	
		if (!is_null($this->protocolPortConfig))
		{
			$protocolPortConfig = array();
			foreach($this->protocolPortConfig as $keyValue)
				$protocolPortConfig[$keyValue->key] = $keyValue->value;
			$dbObject->setProtocolPortConfig($protocolPortConfig);
		}
	
		return $dbObject;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $source_object MediaServerNode */
		parent::doFromObject($source_object, $responseProfile);
	
		if($this->shouldGet('protocolPortConfig', $responseProfile) && !is_null($source_object->getProtocolPortConfig()))
			$this->protocolPortConfig = KalturaKeyValueArray::fromKeyValueArray($source_object->getProtocolPortConfig());
	}
}