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
	 * Media server playback port configuration by protocol and format
	 *
	 * @var KalturaKeyValueArray
	 */
	public $mediaServerPortConfig;
	
	/**
	 * Media server playback Domain configuration by protocol and format
	 *
	 * @var KalturaKeyValueArray
	 */
	public $mediaServerPlaybackDomainConfig;
	
	private static $mapBetweenObjects = array
	(
		'applicationName',
		'mediaServerPortConfig',
		'mediaServerPlaybackDomainConfig',
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
			$dbObject->setMediaServerPortConfig($this->protocolPortConfig->toObjectsArray());
		
		if(!is_null($this->mediaServerPlaybackDomainConfig))
			$dbObject->setMediaServerPlaybackDomainConfig($this->mediaServerPlaybackDomainConfig->toObjectsArray());
	
		return $dbObject;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $source_object MediaServerNode */
		parent::doFromObject($source_object, $responseProfile);
	
		if($this->shouldGet('mediaServerPortConfig', $responseProfile) && !is_null($source_object->getMediaServerPortConfig()))
			$this->mediaServerPortConfig = KalturaKeyValueArray::fromKeyValueArray($source_object->getMediaServerPortConfig());
		
		if($this->shouldGet('mediaServerPlaybackDomainConfig', $responseProfile) && !is_null($source_object->getMediaServerPlaybackDomainConfig()))
			$this->mediaServerPlaybackDomainConfig = KalturaKeyValueArray::fromKeyValueArray($source_object->getMediaServerPlaybackDomainConfig());
	}
}