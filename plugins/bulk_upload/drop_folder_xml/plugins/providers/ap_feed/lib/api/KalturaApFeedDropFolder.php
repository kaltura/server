<?php
/**
 * @package plugins.ApFeedDropFolder
 * @subpackage api.objects
 */
class KalturaApFeedDropFolder extends KalturaFeedDropFolder
{
	/**
	 * @var string
	 */
	public $apApiKey;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(
		'apApiKey',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new ApFeedDropFolder();
		
		if ($this->feedItemInfo)
			$dbObject->setFeedItemInfo($this->feedItemInfo->toObject());
		
		return parent::toObject($dbObject, $skip);
	}
	
}