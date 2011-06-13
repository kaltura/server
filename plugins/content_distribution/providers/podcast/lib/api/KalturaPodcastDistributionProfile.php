<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 */
class KalturaPodcastDistributionProfile extends KalturaDistributionProfile
{	
	/**
	 * @var string
	 */
	public $xsl;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $feedId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'xsl',
		'feedId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
		
	/**
	 * @param PodcastDistributionProfile $object_to_fill
	 * @param array $props_to_skip
	 * @return PodcastDistributionProfile
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new PodcastDistributionProfile();
		
		kPodcastFeedManager::validateXsl($this->xsl);	
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	/**
	 * @param PodcastDistributionProfile $object_to_fill
	 * @param array $props_to_skip
	 * @return PodcastDistributionProfile
	 */
	public function toUpdatableObject ( $object_to_fill , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new PodcastDistributionProfile();
		
		kPodcastFeedManager::validateXsl($this->xsl);
		
		return parent::toUpdatableObject($object_to_fill, $props_to_skip );
	}
	
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new PodcastDistributionProfile();
		
		return parent::toObject($object, $skip);
	}
}