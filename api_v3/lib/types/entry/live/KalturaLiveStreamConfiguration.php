<?php
/**
 * A representation of a live stream configuration
 * 
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamConfiguration extends KalturaObject
{
	/**
	 * @var KalturaPlaybackProtocol
	 */
	public $protocol;
	
	/**
	 * @var string
	 */
	public $url;
	
	private static $mapBetweenObjects = array
	(
		"protocol", "url",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function toObject($dbObject = null, $propsToSkip = null)
	{
		if (!$dbObject)
		{
			$dbObject = new KLiveStreamConfiguration();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	public function fromObject($dbObject)
	{
		/* @var $dbObject KLiveStreamConfiguration */
		$this->protocol = $dbObject->getProtocol();
		$this->url = $dbObject->getUrl();
		
		parent::fromObject($source_object);
	}
	
}