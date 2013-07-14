<?php
/**
 * @package plugins.msnDistribution
 * @subpackage api.objects
 */
class KalturaMsnDistributionProfile extends KalturaConfigurableDistributionProfile
{
/**
	 * @var string
	 */
	public $username;
	
	/**
	 * @var string
	 */
	public $password;
	
	/**
	 * @var string
	 */
	public $domain;
	
	/**
	 * @var string
	 */
	public $csId;
	
	/**
	 * @var string
	 */
	public $source;
	
	/**
	 * @var string
	 */
	public $sourceFriendlyName;
	
	/**
	 * @var string
	 */
	public $pageGroup;
	
	/**
	 * @var int
	 */
	public $sourceFlavorParamsId;
	
	/**
	 * @var int
	 */
	public $wmvFlavorParamsId;
	
	/**
	 * @var int
	 */
	public $flvFlavorParamsId;
	
	/**
	 * @var int
	 */
	public $slFlavorParamsId;
	
	/**
	 * @var int
	 */
	public $slHdFlavorParamsId;
	
	/**
	 * @var string
	 */
	public $msnvideoCat;
	
	/**
	 * @var string
	 */
	public $msnvideoTop;
	
	/**
	 * @var string
	 */
	public $msnvideoTopCat;

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'username',
		'password',
		'domain',
		'configType',
		'csId',
		'source',
		'sourceFriendlyName',
		'pageGroup',
		'metadataProfileId',
		'sourceFlavorParamsId',
		'flvFlavorParamsId',
		'wmvFlavorParamsId',
		'slFlavorParamsId',
		'slHdFlavorParamsId',
		'msnvideoCat',
		'msnvideoTop',
		'msnvideoTopCat',
	
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}