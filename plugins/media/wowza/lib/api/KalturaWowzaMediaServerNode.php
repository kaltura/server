<?php
/**
 * @package plugins.wowza
 * @subpackage api.objects
 */
class KalturaWowzaMediaServerNode extends KalturaMediaServerNode
{	
	/**
	 * Wowza Media server app prefix
	 *
	 * @var string
	 */
	public $appPrefix;
	
	/**
	 * Wowza Media server transcoder configuration overide
	 *
	 * @var string
	 */
	public $transcoder;
	
	/**
	 * Wowza Media server GPU index id
	 *
	 * @var int
	 */
	public $GPUID;
	
	/**
	 * Live service port
	 *
	 * @var int
	 */
	public $liveServicePort;
	
	/**
	 * Live service protocol
	 *
	 * @var string
	 */
	public $liveServiceProtocol;
	
	/**
	 * Wowza media server live service internal domain
	 *
	 * @var string
	 */
	public $liveServiceInternalDomain;
	
	private static $mapBetweenObjects = array
	(
		'appPrefix',
		'transcoder',
		'GPUID',
		'liveServicePort',
		'liveServiceProtocol',
		'liveServiceInternalDomain',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		return parent::validateForInsertByType($propertiesToSkip, WowzaPlugin::getWowzaMediaServerTypeCoreValue(WowzaMediaServerNodeType::WOWZA_MEDIA_SERVER));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		return parent::validateForUpdateByType($sourceObject, $propertiesToSkip, WowzaPlugin::getWowzaMediaServerTypeCoreValue(WowzaMediaServerNodeType::WOWZA_MEDIA_SERVER));
	}
	
	
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
			$object_to_fill = new WowzaMediaServerNode();
			
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new WowzaMediaServerNode();
	
		return parent::toObject($dbObject, $skip);
	}
}
