<?php
/**
 * @package plugins.podcastDistribution
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

	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'xsl',
		'feedId',
		'metadataProfileId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
		
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new PodcastDistributionProfile();
		
		return parent::toObject($object, $skip);
	}
}