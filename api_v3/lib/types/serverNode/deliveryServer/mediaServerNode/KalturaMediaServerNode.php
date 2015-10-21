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
}