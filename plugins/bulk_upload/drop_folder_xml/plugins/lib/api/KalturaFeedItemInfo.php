<?php
/**
 * @package plugins.FeedDropFolder
 * @subpackage api.objects
 */
class KalturaFeedItemInfo extends KalturaObject
{
	/**
	 * @var string
	 */
	public $itemXPath;
	
	/**
	 * @var string
	 */
	public $itemPublishDateXPath;
	
	/**
	 * @var string
	 */
	public $itemUniqueIdentifierXPath;
	
	/**
	 * @var string
	 */
	public $itemContentFileSizeXPath;
	
	/**
	 * @var string
	 */
	public $itemContentUrlXPath;
	
	/**
	 * @var string
	 */
	public $itemContentBitrateXPath;
	
	/**
	 * @var string
	 */
	public $itemHashXPath;
	
	/**
	 * @var string
	 */
	public $itemContentXpath;
	
	/**
	 * @var string
	 */
	public $contentBitrateAttributeName;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'itemXPath',
		'itemPublishDateXPath',
		'itemUniqueIdentifierXPath',
		'itemContentFileSizeXPath',
		'itemContentUrlXPath',
		'itemHashXPath',
		'itemContentXpath',
		'contentBitrateAttributeName',
		'itemContentBitrateXPath',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new FeedItemInfo();
			
		$this->validate();
		return parent::toObject($dbObject, $skip);
	}
	
	public function validate ()
	{
		if (! isset ($this->itemXPath))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'itemXPath');
			
		if (! isset ($this->itemUniqueIdentifierXPath))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'itemUniqueIdentifierXPath');
			
		if (! isset ($this->itemPublishDateXPath))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'itemPublishDateXPath');
			
		if (! isset ($this->itemContentUrlXPath))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'itemContentUrlXPath');
			
		if (! isset ($this->itemContentXpath))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'itemContentXpath');
	}
}