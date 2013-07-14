<?php
/**
 * @package plugins.ndnDistribution
 * @subpackage api.objects
 */
class KalturaNdnDistributionProfile extends KalturaConfigurableDistributionProfile
{		
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */
	public $channelTitle;
	
	/**
	 * @var string
	 */
	public $channelLink;
	
	/**
	 * @var string
	 */
	public $channelDescription;
	
	/**
	 * @var string
	 */
	public $channelLanguage;
	
	/**
	 * @var string
	 */
	public $channelCopyright;
	
	/**
	 * @var string
	 */
	public $channelImageTitle;
	
	/**
	 * @var string
	 */
	public $channelImageUrl;
	
	/**
	 * @var string
	 */
	public $channelImageLink;
	
	/**
	 * @var string
	 */
	public $itemMediaRating;
		
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
		'channelTitle',	
		'channelLink',	
		'channelDescription',	
		'channelLanguage',	
		'channelCopyright',	
		'channelImageTitle',	
		'channelImageUrl',	
		'channelImageLink',
		'itemMediaRating',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
		
	public function toObject($object = null, $skip = array())
	{
		/* @var $object NdnDistributionProfile */
		if(is_null($object))
			$object = new NdnDistributionProfile();
			
		$object = parent::toObject($object, $skip);
			
		return $object;
	}
}