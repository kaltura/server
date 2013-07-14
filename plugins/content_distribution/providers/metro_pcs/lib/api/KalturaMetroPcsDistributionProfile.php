<?php
/**
 * @package plugins.metroPcsDistribution
 * @subpackage api.objects
 */
class KalturaMetroPcsDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $ftpHost;
	
	/**
	 * @var string
	 */
	public $ftpLogin;
	
	/**
	 * @var string
	 */
	public $ftpPass;
	
	/**
	 * @var string
	 */
	public $ftpPath;
	
	/**
	 * @var string
	 */
	public $providerName;
	
	/**
	 * @var string
	 */
	public $providerId;
	
	/**
	 * @var string
	 */
	public $copyright;
	
	/**
	 * @var string
	 */
	public $entitlements;
	
	/**
	 * @var string
	 */
	public $rating;
	
	/**
	 * @var string
	 */
	public $itemType;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'ftpHost',
		'ftpLogin',
		'ftpPass',
		'ftpPath',
		'providerName',
		'providerId',
		'copyright',
		'entitlements',
		'rating',
		'itemType',
	);
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}