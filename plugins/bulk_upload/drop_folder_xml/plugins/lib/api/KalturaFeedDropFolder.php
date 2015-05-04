<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage api.objects
 */
class KalturaFeedDropFolder extends KalturaDropFolder
{
	/**
	 * @var int
	 */
	public $itemHandlingLimit;
	
	/**
	 * @var KalturaFeedItemInfo
	 */
	public $feedItemInfo;
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'itemHandlingLimit',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new FeedDropFolder();
			
		if ($this->feedItemInfo)
			$dbObject->setFeedItemInfo($this->feedItemInfo->toObject());
			
		return parent::toObject($dbObject, $skip);
	}
	
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $srcObj FeedDropFolder */
		parent::doFromObject($srcObj);
		$this->feedItemInfo = new KalturaFeedItemInfo ();
		$this->feedItemInfo->fromObject($srcObj->getFeedItemInfo());
		
		return $this;
	}
}
