<?php
/**
 * @package plugins.tvComDistribution
 * @subpackage api.objects
 */
class KalturaTVComDistributionProfile extends KalturaConfigurableDistributionProfile
{	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */
	public $feedTitle;
	
	/**
	 * @var string
	 */
	public $feedLink;
	
	/**
	 * @var string
	 */
	public $feedDescription;
	
	/**
	 * @var string
	 */
	public $feedLanguage;
	
	/**
	 * @var string
	 */
	public $feedCopyright;
	
	/**
	 * @var string
	 */
	public $feedImageTitle;
	
	/**
	 * @var string
	 */
	public $feedImageUrl;
	
	/**
	 * @var string
	 */
	public $feedImageLink;
	
	/**
	 * @var int
	 */
	public $feedImageWidth;
	
	/**
	 * @var int
	 */
	public $feedImageHeight;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'metadataProfileId',
		'feedUrl',
		'feedTitle',
		'feedLink',
		'feedDescription',
		'feedLanguage',
		'feedCopyright',
		'feedImageTitle',
		'feedImageUrl',
		'feedImageLink',
		'feedImageWidth',
		'feedImageHeight',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
		
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new TVComDistributionProfile();
		
		return parent::toObject($object, $skip);
	}
}