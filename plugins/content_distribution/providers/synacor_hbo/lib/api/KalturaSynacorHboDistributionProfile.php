<?php
/**
 * @package plugins.synacorHboDistribution
 * @subpackage api.objects
 */
class KalturaSynacorHboDistributionProfile extends KalturaConfigurableDistributionProfile
{	
	
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
	public $feedSubtitle;
	
	/**
	 * @var string
	 */
	public $feedLink;
	
	/**
	 * @var string
	 */
	public $feedAuthorName;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'feedUrl',
	    'feedTitle',
	    'feedSubtitle',
	    'feedLink',
	    'feedAuthorName',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
		
	public function toObject($object = null, $skip = array())
	{
		/* @var $object SynacorHboDistributionProfile */
		if(is_null($object))
			$object = new SynacorHboDistributionProfile();
			
		$object = parent::toObject($object, $skip);
			
		return $object;
	}
}